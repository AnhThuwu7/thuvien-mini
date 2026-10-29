<?php

class Loan extends Model
{
    public function getActiveReaders(): array
    {
        return $this->fetchAll(
            "SELECT id, ho_ten, email
             FROM nguoi_dung
             WHERE vai_tro = 'doc_gia'
               AND bi_khoa = 0
               AND xoa_luc IS NULL
             ORDER BY ho_ten ASC"
        );
    }

    public function getAvailableCopies(): array
    {
        return $this->fetchAll(
            "SELECT bs.id, bs.ma_ban_sao, s.ten_sach
             FROM ban_sao AS bs
             INNER JOIN sach AS s ON bs.sach_id = s.id
             WHERE bs.trang_thai = 'con_trong'
               AND bs.xoa_luc IS NULL
               AND s.xoa_luc IS NULL"
        );
    }

    /**
     * Lập phiếu mượn.
     * Hạn trả tính trên server từ cau_hinh.so_ngay_muon (không lấy từ form).
     */
    public function createLoan(
        int $userId,
        int $copyId,
        int $staffId
    ): void {
        $this->transaction(function () use (
            $userId, $copyId, $staffId
        ): void {
            $user = $this->fetchOne(
                "SELECT id, bi_khoa
                 FROM nguoi_dung
                 WHERE id = ?
                   AND vai_tro = 'doc_gia'
                   AND xoa_luc IS NULL
                 FOR UPDATE",
                [$userId]
            );

            if (!$user) {
                throw new RuntimeException(
                    'Không tìm thấy độc giả hợp lệ.'
                );
            }

            if ((int) ($user['bi_khoa'] ?? 0) === 1) {
                throw new RuntimeException(
                    'Tài khoản độc giả đang bị khóa.'
                );
            }

            $limitRow = $this->fetchOne(
                "SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_sach_toi_da' LIMIT 1"
            );
            $maxBooks = (int) ($limitRow['gia_tri'] ?? 5);
            if ($maxBooks < 1) {
                $maxBooks = 5;
            }

            $activeLoans = $this->fetchOne(
                "SELECT COUNT(*) AS total
                 FROM phieu_muon
                 WHERE nguoi_dung_id = ?
                   AND tra_luc IS NULL",
                [$userId]
            );

            if ((int) ($activeLoans['total'] ?? 0) >= $maxBooks) {
                throw new RuntimeException(
                    'Độc giả đã đạt giới hạn số sách được mượn (' . $maxBooks . ' quyển).'
                );
            }

            $copy = $this->fetchOne(
                "SELECT bs.id, bs.trang_thai
                 FROM ban_sao AS bs
                 INNER JOIN sach AS s ON s.id = bs.sach_id
                 WHERE bs.id = ?
                   AND bs.xoa_luc IS NULL
                   AND s.xoa_luc IS NULL
                 FOR UPDATE",
                [$copyId]
            );

            if (!$copy) {
                throw new RuntimeException(
                    'Không tìm thấy bản sao sách.'
                );
            }

            if ($copy['trang_thai'] !== 'con_trong') {
                throw new RuntimeException(
                    'Bản sao sách hiện không có sẵn.'
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
                    (
                        nguoi_dung_id,
                        ban_sao_id,
                        nguoi_cho_muon_id,
                        muon_luc,
                        han_tra
                    )
                 VALUES (?, ?, ?, NOW(), ?)",
                [$userId, $copyId, $staffId, $dueDate]
            );

            $loanId = $this->lastInsertId();

            $this->execute(
                "UPDATE ban_sao
                 SET trang_thai = 'dang_muon'
                 WHERE id = ?",
                [$copyId]
            );

            $this->execute(
                "INSERT INTO nhat_ky_muon
                    (
                        phieu_muon_id,
                        nguoi_thuc_hien_id,
                        hanh_dong,
                        chi_tiet
                    )
                 VALUES (?, ?, 'cho_muon', ?)",
                [
                    $loanId,
                    $staffId,
                    'Thủ thư lập phiếu mượn mới'
                ]
            );
        });
    }

    // ===== GIỮ NGUYÊN TỪ ĐÂY XUỐNG (getHistoryByReaderId, returnBook, ...) =====

    public function getHistoryByReaderId(int $readerId): array
    {
        $sql = "
            SELECT
                pm.id AS phieu_muon_id,
                s.ten_sach,
                bs.ma_ban_sao,
                pm.muon_luc,
                pm.han_tra,
                pm.tra_luc,
                pm.tinh_trang_tra,
                pm.tien_phat,
                CASE
                    WHEN pm.tra_luc IS NOT NULL
                        THEN 'da_tra'
                    WHEN pm.han_tra < CURDATE()
                        THEN 'qua_han'
                    ELSE 'dang_muon'
                END AS trang_thai_hien_thi
            FROM phieu_muon AS pm
            INNER JOIN ban_sao AS bs
                ON pm.ban_sao_id = bs.id
            INNER JOIN sach AS s
                ON bs.sach_id = s.id
            WHERE pm.nguoi_dung_id = ?
            ORDER BY pm.muon_luc DESC, pm.id DESC
        ";

        return $this->fetchAll($sql, [$readerId]);
    }

    private function loanFilterSql(
        string $keyword,
        string $status,
        array &$params
    ): string {
        $where = [];

        if ($keyword !== '') {
            $where[] = "(
                CAST(pm.id AS CHAR) LIKE ?
                OR nd.ho_ten LIKE ?
                OR s.ten_sach LIKE ?
                OR bs.ma_ban_sao LIKE ?
            )";

            $term = '%' . $keyword . '%';

            array_push(
                $params,
                $term,
                $term,
                $term,
                $term
            );
        }

        if (in_array(
            $status,
            ['dang_muon', 'qua_han', 'da_tra'],
            true
        )) {
            if ($status === 'da_tra') {
                $where[] = 'pm.tra_luc IS NOT NULL';
            } elseif ($status === 'qua_han') {
                $where[] = '
                    pm.tra_luc IS NULL
                    AND pm.han_tra < CURDATE()
                ';
            } else {
                $where[] = '
                    pm.tra_luc IS NULL
                    AND pm.han_tra >= CURDATE()
                ';
            }
        }

        return $where
            ? ' WHERE ' . implode(' AND ', $where)
            : '';
    }

    public function countFilteredLoans(
        string $keyword = '',
        string $status = ''
    ): int {
        $params = [];

        $whereSql = $this->loanFilterSql(
            $keyword,
            $status,
            $params
        );

        $sql = "
            SELECT COUNT(*) AS total
            FROM phieu_muon AS pm
            INNER JOIN nguoi_dung AS nd
                ON pm.nguoi_dung_id = nd.id
            INNER JOIN ban_sao AS bs
                ON pm.ban_sao_id = bs.id
            INNER JOIN sach AS s
                ON bs.sach_id = s.id
            {$whereSql}
        ";

        $result = $this->fetchOne($sql, $params);

        return (int) ($result['total'] ?? 0);
    }

    public function getLoanStats(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(
                    CASE
                        WHEN tra_luc IS NULL
                         AND han_tra >= CURDATE()
                        THEN 1 ELSE 0
                    END
                ), 0) AS borrowing,
                COALESCE(SUM(
                    CASE
                        WHEN tra_luc IS NULL
                         AND han_tra < CURDATE()
                        THEN 1 ELSE 0
                    END
                ), 0) AS overdue,
                COALESCE(SUM(
                    CASE
                        WHEN tra_luc IS NOT NULL
                        THEN 1 ELSE 0
                    END
                ), 0) AS returned
            FROM phieu_muon
        ";

        $stats = $this->fetchOne($sql);

        return [
            'total' => (int) ($stats['total'] ?? 0),
            'borrowing' => (int) ($stats['borrowing'] ?? 0),
            'overdue' => (int) ($stats['overdue'] ?? 0),
            'returned' => (int) ($stats['returned'] ?? 0),
        ];
    }

    public function getLoansPaginated(
        int $limit,
        int $offset,
        string $keyword = '',
        string $status = ''
    ): array {
        $limit = max(1, min($limit, 100));
        $offset = max(0, $offset);

        $params = [];

        $whereSql = $this->loanFilterSql(
            $keyword,
            $status,
            $params
        );

        $sql = "
            SELECT
                pm.id AS phieu_muon_id,
                pm.nguoi_dung_id,
                pm.ban_sao_id,
                pm.muon_luc,
                pm.han_tra,
                pm.tra_luc,
                pm.tinh_trang_tra,
                pm.tien_phat,
                nd.ho_ten AS ten_doc_gia,
                bs.ma_ban_sao,
                s.ten_sach,
                CASE
                    WHEN pm.tra_luc IS NOT NULL
                        THEN 'da_tra'
                    WHEN pm.han_tra < CURDATE()
                        THEN 'qua_han'
                    ELSE 'dang_muon'
                END AS trang_thai_hien_thi
            FROM phieu_muon AS pm
            INNER JOIN nguoi_dung AS nd
                ON pm.nguoi_dung_id = nd.id
            INNER JOIN ban_sao AS bs
                ON pm.ban_sao_id = bs.id
            INNER JOIN sach AS s
                ON bs.sach_id = s.id
            {$whereSql}
            ORDER BY
                (pm.tra_luc IS NULL) DESC,
                pm.han_tra ASC,
                pm.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        return $this->fetchAll($sql, $params);
    }

    public function getAllLoans(): array
    {
        return $this->getLoansPaginated(100, 0);
    }


    /**
     * Tính tiền phạt quá hạn theo cấu hình tien_phat_moi_ngay.
     * Công thức: max(0, số ngày quá hạn) × đơn giá/ngày.
     * Ngày trả dùng $returnDate (Y-m-d) hoặc hôm nay.
     *
     * @return array{days_overdue:int, fine_per_day:float, fine:float, han_tra:?string}
     */
    public function calculateOverdueFine(int $loanId, ?string $returnDate = null): array
    {
        $loan = $this->fetchOne(
            "SELECT id, han_tra, tra_luc
             FROM phieu_muon
             WHERE id = ?
             LIMIT 1",
            [$loanId]
        );

        if ($loan === null) {
            throw new RuntimeException('Không tìm thấy phiếu mượn.');
        }

        $settingRow = $this->fetchOne(
            "SELECT gia_tri FROM cau_hinh WHERE khoa = 'tien_phat_moi_ngay' LIMIT 1"
        );
        $finePerDay = (float) ($settingRow['gia_tri'] ?? 2000);
        if ($finePerDay < 0) {
            $finePerDay = 0;
        }

        $hanTra = $loan['han_tra'] ?? null;
        if ($hanTra === null || $hanTra === '') {
            return [
                'days_overdue' => 0,
                'fine_per_day' => $finePerDay,
                'fine' => 0.0,
                'han_tra' => null,
            ];
        }

        $han = new DateTimeImmutable(substr((string) $hanTra, 0, 10));
        if ($returnDate !== null && $returnDate !== '') {
            $ret = new DateTimeImmutable(substr($returnDate, 0, 10));
        } else {
            $ret = new DateTimeImmutable('today');
        }

        $days = (int) $han->diff($ret)->format('%r%a');
        // %r%a: âm nếu ret < han (chưa quá hạn)
        $daysOverdue = max(0, $days);

        return [
            'days_overdue' => $daysOverdue,
            'fine_per_day' => $finePerDay,
            'fine' => round($daysOverdue * $finePerDay, 2),
            'han_tra' => $han->format('Y-m-d'),
        ];
    }

    public function returnBook(
        int $loanId,
        int $staffId,
        string $condition,
        float $fine = 0,
        string $note = ''
    ): void {
        if ($loanId <= 0 || $staffId <= 0) {
            throw new RuntimeException(
                'Mã phiếu mượn hoặc nhân viên không hợp lệ.'
            );
        }

        $copyStatuses = [
            'tot' => 'con_trong',
            'hong' => 'hong',
            'mat' => 'mat'
        ];

        if (!isset($copyStatuses[$condition])) {
            throw new RuntimeException(
                'Tình trạng sách không hợp lệ.'
            );
        }

        if (!is_finite($fine) || $fine < 0) {
            throw new RuntimeException(
                'Tiền phạt không hợp lệ.'
            );
        }

        $note = trim($note);

        if (mb_strlen($note) > 255) {
            throw new RuntimeException(
                'Ghi chú không được vượt quá 255 ký tự.'
            );
        }

        $this->transaction(function () use (
            $loanId,
            $staffId,
            $condition,
            $copyStatuses,
            $fine,
            $note
        ): void {
            $loan = $this->fetchOne(
                "SELECT id, ban_sao_id, tra_luc
                 FROM phieu_muon
                 WHERE id = ?
                 FOR UPDATE",
                [$loanId]
            );

            if (!$loan) {
                throw new RuntimeException(
                    'Không tìm thấy phiếu mượn.'
                );
            }

            if ($loan['tra_luc'] !== null) {
                throw new RuntimeException(
                    'Phiếu mượn này đã được trả trước đó.'
                );
            }

            $copy = $this->fetchOne(
                "SELECT id, trang_thai
                 FROM ban_sao
                 WHERE id = ?
                   AND xoa_luc IS NULL
                 FOR UPDATE",
                [(int) $loan['ban_sao_id']]
            );

            if (!$copy) {
                throw new RuntimeException(
                    'Không tìm thấy bản sao sách hợp lệ.'
                );
            }

            if ($copy['trang_thai'] !== 'dang_muon') {
                throw new RuntimeException(
                    'Trạng thái bản sao không phù hợp để xác nhận trả.'
                );
            }

            $this->execute(
                "UPDATE phieu_muon
                 SET tra_luc = NOW(),
                     nguoi_nhan_tra_id = ?,
                     tinh_trang_tra = ?,
                     tien_phat = ?,
                     ghi_chu = ?
                 WHERE id = ?
                   AND tra_luc IS NULL",
                [
                    $staffId,
                    $condition,
                    $fine,
                    $note !== '' ? $note : null,
                    $loanId
                ]
            );

            $this->execute(
                "UPDATE ban_sao
                 SET trang_thai = ?
                 WHERE id = ?",
                [
                    $copyStatuses[$condition],
                    (int) $loan['ban_sao_id']
                ]
            );

            $detail = 'Tình trạng sách: ' . $condition
                . '; tiền phạt: '
                . number_format($fine, 2, '.', '');

            if ($note !== '') {
                $detail .= '; ghi chú: ' . $note;
            }

            $this->execute(
                "INSERT INTO nhat_ky_muon
                    (
                        phieu_muon_id,
                        nguoi_thuc_hien_id,
                        hanh_dong,
                        chi_tiet
                    )
                 VALUES (?, ?, 'tra_sach', ?)",
                [$loanId, $staffId, $detail]
            );
        });
    }
}