<?php

class Copy extends Model
{
    /** Trạng thái do hệ thống tự đặt (khi lập phiếu / duyệt yêu cầu), không được sửa tay */
    public const SYSTEM_STATUSES = ['dang_muon', 'dang_giu_cho'];

    /**
     * Lấy danh sách bản sao, có thể tìm theo mã bản sao,
     * tên sách hoặc vị trí kệ.
     */
    /** Tổng số bản sao khớp bộ lọc (dùng cho phân trang). */
    public function countAll(string $keyword = '', string $status = ''): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM ban_sao bs
                INNER JOIN sach s ON s.id = bs.sach_id
                WHERE bs.xoa_luc IS NULL
                  AND s.xoa_luc IS NULL";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (bs.ma_ban_sao LIKE ? OR s.ten_sach LIKE ? OR bs.vi_tri_ke LIKE ?)";
            $search = '%' . $keyword . '%';
            array_push($params, $search, $search, $search);
        }
        if ($status !== '') {
            $sql .= " AND bs.trang_thai = ?";
            $params[] = $status;
        }

        $row = $this->fetchOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }

    public function getAll(
        string $keyword = '',
        string $status = '',
        int $limit = 0,
        int $offset = 0
    ): array {
        $sql = "SELECT
                    bs.id,
                    bs.sach_id,
                    bs.ma_ban_sao,
                    bs.trang_thai,
                    bs.vi_tri_ke,
                    bs.ngay_nhap,
                    bs.ghi_chu,
                    s.ten_sach
                FROM ban_sao bs
                INNER JOIN sach s ON s.id = bs.sach_id
                WHERE bs.xoa_luc IS NULL
                  AND s.xoa_luc IS NULL";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (
                bs.ma_ban_sao LIKE ?
                OR s.ten_sach LIKE ?
                OR bs.vi_tri_ke LIKE ?
            )";

            $search = '%' . $keyword . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if ($status !== '') {
            $sql .= " AND bs.trang_thai = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY bs.id DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int) $limit . " OFFSET " . max(0, (int) $offset);
        }

        return $this->fetchAll($sql, $params);
    }

    /**
     * Lấy một bản sao theo ID.
     */
    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT
                bs.id,
                bs.sach_id,
                bs.ma_ban_sao,
                bs.trang_thai,
                bs.vi_tri_ke,
                bs.ngay_nhap,
                bs.ghi_chu,
                s.ten_sach
             FROM ban_sao bs
             INNER JOIN sach s ON s.id = bs.sach_id
             WHERE bs.id = ?
               AND bs.xoa_luc IS NULL
               AND s.xoa_luc IS NULL",
            [$id]
        );
    }

    /**
     * Lấy danh sách đầu sách còn hoạt động để chọn khi thêm bản sao.
     */
    public function getBooks(): array
    {
        return $this->fetchAll(
            "SELECT id, ten_sach
             FROM sach
             WHERE xoa_luc IS NULL
             ORDER BY ten_sach ASC"
        );
    }

    /**
     * Kiểm tra mã bản sao đã tồn tại chưa.
     */
    public function isCodeExists(
        string $code,
        ?int $excludeId = null
    ): bool {
        if ($excludeId !== null) {
            $row = $this->fetchOne(
                "SELECT id
                 FROM ban_sao
                 WHERE ma_ban_sao = ?
                   AND id <> ?
                 LIMIT 1",
                [$code, $excludeId]
            );
        } else {
            $row = $this->fetchOne(
                "SELECT id
                 FROM ban_sao
                 WHERE ma_ban_sao = ?
                 LIMIT 1",
                [$code]
            );
        }

        return $row !== null;
    }

    /**
     * Thêm bản sao mới.
     */
    public function create(array $data): int
    {
        if (in_array($data['trang_thai'], self::SYSTEM_STATUSES, true)) {
            throw new RuntimeException(
                'Trạng thái "đang mượn / đang giữ chỗ" chỉ do hệ thống đặt khi lập phiếu hoặc duyệt yêu cầu.'
            );
        }

        $this->execute(
            "INSERT INTO ban_sao (
                sach_id,
                ma_ban_sao,
                trang_thai,
                vi_tri_ke,
                ngay_nhap,
                ghi_chu
            ) VALUES (?, ?, ?, ?, ?, ?)",
            [
                (int) $data['sach_id'],
                $data['ma_ban_sao'],
                $data['trang_thai'],
                $data['vi_tri_ke'] !== ''
                    ? $data['vi_tri_ke']
                    : null,
                $data['ngay_nhap'] !== ''
                    ? $data['ngay_nhap']
                    : null,
                $data['ghi_chu'] !== ''
                    ? $data['ghi_chu']
                    : null,
            ]
        );

        return $this->lastInsertId();
    }

    /**
     * Cập nhật thông tin bản sao.
     */
    public function update(int $id, array $data): int
    {
        // Khóa dòng bản sao để trạng thái không bị đổi giữa lúc kiểm tra và lúc ghi
        return $this->transaction(function () use ($id, $data): int {
            $current = $this->fetchOne(
                "SELECT sach_id, trang_thai
                 FROM ban_sao
                 WHERE id = ?
                   AND xoa_luc IS NULL
                 FOR UPDATE",
                [$id]
            );

            if (!$current) {
                throw new RuntimeException('Không tìm thấy bản sao.');
            }

            $isLocked = in_array($current['trang_thai'], self::SYSTEM_STATUSES, true);
            $wantsSystem = in_array($data['trang_thai'], self::SYSTEM_STATUSES, true);

            if ($isLocked) {
                // Đang có phiếu mượn / yêu cầu giữ chỗ: không cho đổi trạng thái hay đầu sách
                if (
                    $data['trang_thai'] !== $current['trang_thai']
                    || (int) $data['sach_id'] !== (int) $current['sach_id']
                ) {
                    throw new RuntimeException(
                        'Bản sao đang được mượn hoặc giữ chỗ nên không thể đổi trạng thái hoặc đầu sách. '
                        . 'Hãy ghi nhận trả sách hoặc hủy giữ chỗ trước.'
                    );
                }
            } elseif ($wantsSystem) {
                throw new RuntimeException(
                    'Trạng thái "đang mượn / đang giữ chỗ" chỉ do hệ thống đặt khi lập phiếu hoặc duyệt yêu cầu.'
                );
            }

            return $this->execute(
                "UPDATE ban_sao
                 SET sach_id = ?,
                     ma_ban_sao = ?,
                     trang_thai = ?,
                     vi_tri_ke = ?,
                     ngay_nhap = ?,
                     ghi_chu = ?
                 WHERE id = ?
                   AND xoa_luc IS NULL",
                [
                    (int) $data['sach_id'],
                    $data['ma_ban_sao'],
                    $data['trang_thai'],
                    $data['vi_tri_ke'] !== '' ? $data['vi_tri_ke'] : null,
                    $data['ngay_nhap'] !== '' ? $data['ngay_nhap'] : null,
                    $data['ghi_chu'] !== '' ? $data['ghi_chu'] : null,
                    $id,
                ]
            );
        });
    }

    /**
     * Xóa mềm bản sao.
     */
    public function softDelete(int $id): int
    {
        return $this->execute(
            "UPDATE ban_sao
             SET xoa_luc = NOW()
             WHERE id = ?
               AND xoa_luc IS NULL",
            [$id]
        );
    }

    /**
     * Kiểm tra bản sao còn cho mượn được không.
     */
    public function isAvailable(int $id): bool
    {
        $row = $this->fetchOne(
            "SELECT id
             FROM ban_sao
             WHERE id = ?
               AND xoa_luc IS NULL
               AND trang_thai NOT IN ('dang_muon', 'dang_giu_cho')",
            [$id]
        );

        return $row !== null;
    }

    /**
     * Đếm mọi bản sao của một đầu sách (kể cả đã xóa mềm)
     * để số thứ tự không bị trùng mã cũ.
     */
    public function countByBookId(int $sachId): int
    {
        $row = $this->fetchOne(
            'SELECT COUNT(*) AS total FROM ban_sao WHERE sach_id = ?',
            [$sachId]
        );
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Gợi ý mã bản sao tiếp theo: BK0027-01, BK0027-02, ...
     * @return array{code: string, sequence: int, is_first: bool}
     */
    public function suggestNextCode(int $sachId): array
    {
        $seq = $this->countByBookId($sachId) + 1;
        $code = sprintf('BK%04d-%02d', $sachId, $seq);

        $guard = 0;
        while ($this->isCodeExists($code) && $guard < 100) {
            $seq++;
            $code = sprintf('BK%04d-%02d', $sachId, $seq);
            $guard++;
        }

        return [
            'code'     => $code,
            'sequence' => $seq,
            'is_first' => $seq === 1,
        ];
    }
           /** Bản sao của một đầu sách (catalog độc giả) */
    public function getByBookId(int $bookId): array
    {
        return $this->fetchAll(
            "SELECT id, ma_ban_sao, trang_thai, vi_tri_ke
             FROM ban_sao
             WHERE sach_id = ?
               AND xoa_luc IS NULL
             ORDER BY ma_ban_sao ASC",
            [$bookId]
        );
    }

    /**
     * Bản sao + phiếu đang mượn (thủ thư / admin).
     */
    public function getByBookIdWithLoan(int $bookId): array
    {
        return $this->fetchAll(
            "SELECT
                bs.id,
                bs.ma_ban_sao,
                bs.trang_thai,
                bs.vi_tri_ke,
                pm.id AS phieu_muon_id,
                pm.han_tra,
                nd.ho_ten AS ten_doc_gia,
                nd.ma_doc_gia
             FROM ban_sao AS bs
             LEFT JOIN phieu_muon AS pm
                    ON pm.ban_sao_id = bs.id
                   AND pm.tra_luc IS NULL
             LEFT JOIN nguoi_dung AS nd
                    ON nd.id = pm.nguoi_dung_id
             WHERE bs.sach_id = ?
               AND bs.xoa_luc IS NULL
             ORDER BY bs.ma_ban_sao ASC",
            [$bookId]
        );
    }
}