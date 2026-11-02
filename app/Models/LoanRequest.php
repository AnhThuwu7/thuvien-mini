<?php

class LoanRequest extends Model
{
    /**
     * Hết hạn giữ chỗ: da_duyet quá han_den_lay -> het_han, trả bản sao về con_trong.
     */
    public function expireOverdueHolds(): int
    {
        $expired = $this->fetchAll(
            "SELECT id, ban_sao_id
             FROM yeu_cau_muon
             WHERE trang_thai = 'da_duyet'
               AND han_den_lay IS NOT NULL
               AND han_den_lay < NOW()
               AND ban_sao_id IS NOT NULL"
        );

        if (!$expired) {
            return 0;
        }

        $count = 0;
        foreach ($expired as $row) {
            $this->transaction(function () use ($row, &$count): void {
                $req = $this->fetchOne(
                    "SELECT id, ban_sao_id, trang_thai
                     FROM yeu_cau_muon
                     WHERE id = ?
                     FOR UPDATE",
                    [(int) $row['id']]
                );
                if (!$req || $req['trang_thai'] !== 'da_duyet') {
                    return;
                }

                $this->execute(
                    "UPDATE yeu_cau_muon
                     SET trang_thai = 'het_han'
                     WHERE id = ?
                       AND trang_thai = 'da_duyet'",
                    [(int) $req['id']]
                );

                if (!empty($req['ban_sao_id'])) {
                    $this->execute(
                        "UPDATE ban_sao
                         SET trang_thai = 'con_trong'
                         WHERE id = ?
                           AND trang_thai = 'dang_giu_cho'
                           AND xoa_luc IS NULL",
                        [(int) $req['ban_sao_id']]
                    );
                }
                $count++;
            });
        }

        return $count;
    }

    public function getPendingRequests(): array
    {
        return $this->fetchAll(
            "SELECT
                yc.id,
                nd.ma_doc_gia,
                nd.ho_ten,
                nd.id AS nguoi_dung_id,
                s.ten_sach,
                s.id AS sach_id,
                yc.yeu_cau_luc,
                yc.ghi_chu,
                yc.trang_thai
             FROM yeu_cau_muon AS yc
             INNER JOIN nguoi_dung AS nd ON nd.id = yc.nguoi_dung_id
             INNER JOIN sach AS s ON s.id = yc.sach_id
             WHERE yc.trang_thai = 'cho_duyet'
             ORDER BY yc.yeu_cau_luc ASC, yc.id ASC"
        );
    }

    public function getApprovedHolds(): array
    {
        return $this->fetchAll(
            "SELECT
                yc.id,
                nd.ma_doc_gia,
                nd.ho_ten,
                nd.id AS nguoi_dung_id,
                s.ten_sach,
                s.id AS sach_id,
                bs.ma_ban_sao,
                yc.ban_sao_id,
                yc.yeu_cau_luc,
                yc.xu_ly_luc,
                yc.han_den_lay,
                yc.trang_thai
             FROM yeu_cau_muon AS yc
             INNER JOIN nguoi_dung AS nd ON nd.id = yc.nguoi_dung_id
             INNER JOIN sach AS s ON s.id = yc.sach_id
             LEFT JOIN ban_sao AS bs ON bs.id = yc.ban_sao_id
             WHERE yc.trang_thai = 'da_duyet'
               AND (yc.han_den_lay IS NULL OR yc.han_den_lay >= NOW())
             ORDER BY yc.han_den_lay ASC, yc.id ASC"
        );
    }

    public function getClosedRequests(int $limit = 50): array
    {
        return $this->fetchAll(
            "SELECT
                yc.id,
                nd.ma_doc_gia,
                nd.ho_ten,
                s.ten_sach,
                yc.trang_thai,
                yc.ly_do_tu_choi,
                yc.xu_ly_luc,
                yc.han_den_lay,
                yc.yeu_cau_luc
             FROM yeu_cau_muon AS yc
             INNER JOIN nguoi_dung AS nd ON nd.id = yc.nguoi_dung_id
             INNER JOIN sach AS s ON s.id = yc.sach_id
             WHERE yc.trang_thai IN ('tu_choi', 'het_han', 'da_nhan')
             ORDER BY COALESCE(yc.xu_ly_luc, yc.yeu_cau_luc) DESC
             LIMIT " . (int) $limit
        );
    }

    private function holdHours(): int
    {
        $row = $this->fetchOne(
            "SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_gio_giu_sach' LIMIT 1"
        );
        if ($row && is_numeric($row['gia_tri'])) {
            $h = (int) $row['gia_tri'];
            return $h >= 1 ? $h : 12;
        }

        $row = $this->fetchOne(
            "SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_ngay_giu_sach' LIMIT 1"
        );
        if ($row && is_numeric($row['gia_tri'])) {
            $d = (int) $row['gia_tri'];
            if ($d >= 1) {
                return $d * 24;
            }
        }

        return 12;
    }

    public function approveRequest(int $requestId, int $staffId): void
    {
        if ($requestId <= 0 || $staffId <= 0) {
            throw new RuntimeException('Mã yêu cầu hoặc mã nhân viên không hợp lệ.');
        }

        $this->transaction(function () use ($requestId, $staffId): void {
            $request = $this->fetchOne(
                "SELECT id, sach_id, trang_thai
                 FROM yeu_cau_muon
                 WHERE id = ?
                 FOR UPDATE",
                [$requestId]
            );

            if (!$request) {
                throw new RuntimeException('Không tìm thấy yêu cầu mượn sách.');
            }
            if ($request['trang_thai'] !== 'cho_duyet') {
                throw new RuntimeException('Yêu cầu này không còn ở trạng thái chờ duyệt.');
            }

            $holdHours = $this->holdHours();

            $copy = $this->fetchOne(
                "SELECT id
                 FROM ban_sao
                 WHERE sach_id = ?
                   AND trang_thai = 'con_trong'
                   AND xoa_luc IS NULL
                 ORDER BY id ASC
                 LIMIT 1
                 FOR UPDATE",
                [(int) $request['sach_id']]
            );

            if (!$copy) {
                throw new RuntimeException('Sách hiện không còn bản sao trống để giữ chỗ.');
            }

            $copyId = (int) $copy['id'];

            $n = $this->execute(
                "UPDATE ban_sao
                 SET trang_thai = 'dang_giu_cho'
                 WHERE id = ?
                   AND trang_thai = 'con_trong'
                   AND xoa_luc IS NULL",
                [$copyId]
            );
            if ($n !== 1) {
                throw new RuntimeException('Bản sao vừa được thay đổi bởi thao tác khác. Hãy thử lại.');
            }

            $sql = "UPDATE yeu_cau_muon
                    SET trang_thai = 'da_duyet',
                        ban_sao_id = ?,
                        nguoi_xu_ly_id = ?,
                        xu_ly_luc = NOW(),
                        han_den_lay = DATE_ADD(NOW(), INTERVAL " . (int) $holdHours . " HOUR)
                    WHERE id = ?
                      AND trang_thai = 'cho_duyet'";
            $n = $this->execute($sql, [$copyId, $staffId, $requestId]);
            if ($n !== 1) {
                throw new RuntimeException('Không thể cập nhật trạng thái yêu cầu mượn.');
            }
        });
    }

    public function rejectRequest(
        int $requestId,
        int $staffId,
        string $reason = 'Yêu cầu không được chấp thuận.'
    ): void {
        if ($requestId <= 0 || $staffId <= 0) {
            throw new RuntimeException('Mã yêu cầu hoặc mã nhân viên không hợp lệ.');
        }

        $reason = trim($reason);
        if ($reason === '') {
            $reason = 'Yêu cầu không được chấp thuận.';
        }
        if (function_exists('mb_substr')) {
            $reason = mb_substr($reason, 0, 255, 'UTF-8');
        } else {
            $reason = substr($reason, 0, 255);
        }

        $this->transaction(function () use ($requestId, $staffId, $reason): void {
            $request = $this->fetchOne(
                "SELECT id, trang_thai
                 FROM yeu_cau_muon
                 WHERE id = ?
                 FOR UPDATE",
                [$requestId]
            );

            if (!$request) {
                throw new RuntimeException('Không tìm thấy yêu cầu mượn sách.');
            }
            if ($request['trang_thai'] !== 'cho_duyet') {
                throw new RuntimeException('Yêu cầu này không còn ở trạng thái chờ duyệt.');
            }

            $n = $this->execute(
                "UPDATE yeu_cau_muon
                 SET trang_thai = 'tu_choi',
                     nguoi_xu_ly_id = ?,
                     xu_ly_luc = NOW(),
                     ly_do_tu_choi = ?
                 WHERE id = ?
                   AND trang_thai = 'cho_duyet'",
                [$staffId, $reason, $requestId]
            );
            if ($n !== 1) {
                throw new RuntimeException('Không thể cập nhật trạng thái từ chối yêu cầu.');
            }
        });
    }

    public function fulfillToLoan(int $requestId, int $staffId): void
    {
        if ($requestId <= 0 || $staffId <= 0) {
            throw new RuntimeException('Dữ liệu không hợp lệ.');
        }

        $this->expireOverdueHolds();

        $this->transaction(function () use ($requestId, $staffId): void {
            $req = $this->fetchOne(
                "SELECT id, nguoi_dung_id, ban_sao_id, trang_thai, han_den_lay
                 FROM yeu_cau_muon
                 WHERE id = ?
                 FOR UPDATE",
                [$requestId]
            );

            if (!$req) {
                throw new RuntimeException('Không tìm thấy yêu cầu.');
            }
            if ($req['trang_thai'] !== 'da_duyet') {
                throw new RuntimeException('Chỉ lập phiếu từ yêu cầu đã duyệt còn hiệu lực.');
            }
            if (empty($req['ban_sao_id'])) {
                throw new RuntimeException('Yêu cầu chưa được gán bản sao giữ chỗ.');
            }
            if (!empty($req['han_den_lay']) && strtotime($req['han_den_lay']) < time()) {
                throw new RuntimeException('Yêu cầu đã quá hạn đến lấy. Bản sao đã/ sẽ được trả kho.');
            }

            $userId = (int) $req['nguoi_dung_id'];
            $copyId = (int) $req['ban_sao_id'];

            $user = $this->fetchOne(
                "SELECT id, bi_khoa, vai_tro
                 FROM nguoi_dung
                 WHERE id = ? AND xoa_luc IS NULL
                 FOR UPDATE",
                [$userId]
            );
            if (!$user || ($user['vai_tro'] ?? '') !== 'doc_gia') {
                throw new RuntimeException('Độc giả không hợp lệ.');
            }
            if ((int) ($user['bi_khoa'] ?? 0) === 1) {
                throw new RuntimeException('Tài khoản độc giả đang bị khóa.');
            }

            $limitRow = $this->fetchOne(
                "SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_sach_toi_da' LIMIT 1"
            );
            $maxBooks = (int) ($limitRow['gia_tri'] ?? 5);
            if ($maxBooks < 1) {
                $maxBooks = 5;
            }
            $active = $this->fetchOne(
                "SELECT COUNT(*) AS total FROM phieu_muon
                 WHERE nguoi_dung_id = ? AND tra_luc IS NULL",
                [$userId]
            );
            if ((int) ($active['total'] ?? 0) >= $maxBooks) {
                throw new RuntimeException(
                    'Độc giả đã đạt giới hạn số sách được mượn (' . $maxBooks . ' quyển).'
                );
            }

            $copy = $this->fetchOne(
                "SELECT id, trang_thai FROM ban_sao
                 WHERE id = ? AND xoa_luc IS NULL
                 FOR UPDATE",
                [$copyId]
            );
            if (!$copy) {
                throw new RuntimeException('Không tìm thấy bản sao giữ chỗ.');
            }
            if ($copy['trang_thai'] !== 'dang_giu_cho') {
                throw new RuntimeException(
                    'Bản sao không còn ở trạng thái giữ chỗ (hiện: ' . ($copy['trang_thai'] ?? '?') . ').'
                );
            }

            $row = $this->fetchOne(
                "SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_ngay_muon' LIMIT 1"
            );
            $days = (int) ($row['gia_tri'] ?? 14);
            if ($days < 1) {
                $days = 14;
            }
            $dueDate = (new DateTimeImmutable('today'))
                ->modify('+' . $days . ' days')
                ->format('Y-m-d');

            $this->execute(
                "INSERT INTO phieu_muon
                    (nguoi_dung_id, ban_sao_id, nguoi_cho_muon_id, muon_luc, han_tra)
                 VALUES (?, ?, ?, NOW(), ?)",
                [$userId, $copyId, $staffId, $dueDate]
            );
            $loanId = $this->lastInsertId();

            $this->execute(
                "UPDATE ban_sao SET trang_thai = 'dang_muon' WHERE id = ?",
                [$copyId]
            );

            $this->execute(
                "UPDATE yeu_cau_muon
                 SET trang_thai = 'da_nhan',
                     nguoi_xu_ly_id = ?,
                     xu_ly_luc = NOW()
                 WHERE id = ?
                   AND trang_thai = 'da_duyet'",
                [$staffId, $requestId]
            );

            $this->execute(
                "INSERT INTO nhat_ky_muon
                    (phieu_muon_id, nguoi_thuc_hien_id, hanh_dong, chi_tiet)
                 VALUES (?, ?, 'cho_muon', ?)",
                [
                    $loanId,
                    $staffId,
                    'Lập phiếu từ yêu cầu giữ chỗ #' . $requestId,
                ]
            );
        });
    }

    public function createRequest(int $userId, int $bookId, string $note = ''): int
    {
        if ($userId <= 0 || $bookId <= 0) {
            throw new RuntimeException('Dữ liệu yêu cầu không hợp lệ.');
        }

        $user = $this->fetchOne(
            "SELECT id, vai_tro, bi_khoa FROM nguoi_dung WHERE id = ? LIMIT 1",
            [$userId]
        );
        if (!$user || ($user['vai_tro'] ?? '') !== 'doc_gia') {
            throw new RuntimeException('Chỉ độc giả mới được gửi yêu cầu mượn.');
        }
        if ((int) ($user['bi_khoa'] ?? 0) === 1) {
            throw new RuntimeException('Tài khoản đang bị khóa, không thể gửi yêu cầu mượn.');
        }

        $book = $this->fetchOne(
            "SELECT id FROM sach WHERE id = ? AND xoa_luc IS NULL LIMIT 1",
            [$bookId]
        );
        if (!$book) {
            throw new RuntimeException('Không tìm thấy đầu sách.');
        }

        $existing = $this->fetchOne(
            "SELECT id FROM yeu_cau_muon
             WHERE nguoi_dung_id = ?
               AND sach_id = ?
               AND trang_thai IN ('cho_duyet', 'da_duyet')
             LIMIT 1",
            [$userId, $bookId]
        );
        if ($existing) {
            throw new RuntimeException('Bạn đã có yêu cầu đang chờ xử lý cho đầu sách này.');
        }

        $available = $this->fetchOne(
            "SELECT COUNT(*) AS total FROM ban_sao
             WHERE sach_id = ? AND xoa_luc IS NULL AND trang_thai = 'con_trong'",
            [$bookId]
        );
        if ((int) ($available['total'] ?? 0) < 1) {
            throw new RuntimeException('Hiện không còn bản sao trống. Không thể gửi yêu cầu mượn.');
        }

        if (function_exists('mb_substr')) {
            $note = mb_substr(trim($note), 0, 255, 'UTF-8');
        } else {
            $note = substr(trim($note), 0, 255);
        }

        $this->execute(
            "INSERT INTO yeu_cau_muon (nguoi_dung_id, sach_id, trang_thai, ghi_chu, yeu_cau_luc)
             VALUES (?, ?, 'cho_duyet', ?, NOW())",
            [$userId, $bookId, $note !== '' ? $note : null]
        );

        return $this->lastInsertId();
    }

    public function getByReaderId(int $userId): array
    {
        $this->expireOverdueHolds();

        return $this->fetchAll(
            "SELECT
                yc.id,
                yc.sach_id,
                s.ten_sach,
                yc.trang_thai,
                yc.ghi_chu,
                yc.yeu_cau_luc,
                yc.xu_ly_luc,
                yc.ly_do_tu_choi,
                yc.han_den_lay,
                bs.ma_ban_sao
             FROM yeu_cau_muon yc
             INNER JOIN sach s ON s.id = yc.sach_id
             LEFT JOIN ban_sao bs ON bs.id = yc.ban_sao_id
             WHERE yc.nguoi_dung_id = ?
             ORDER BY yc.yeu_cau_luc DESC, yc.id DESC",
            [$userId]
        );
    }
}