
<?php

class Copy extends Model
{
    /**
     * Lấy danh sách bản sao, có thể tìm theo mã bản sao,
     * tên sách hoặc vị trí kệ.
     */
    public function getAll(string $keyword = '', string $status = ''): array
    {
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
                $data['vi_tri_ke'] !== ''
                    ? $data['vi_tri_ke']
                    : null,
                $data['ngay_nhap'] !== ''
                    ? $data['ngay_nhap']
                    : null,
                $data['ghi_chu'] !== ''
                    ? $data['ghi_chu']
                    : null,
                $id,
            ]
        );
    }

    /**
     * Xóa mềm bản sao, không xóa hẳn khỏi cơ sở dữ liệu.
     */
    public function softDelete(int $id): int
    {
        return $this->execute(
            "UPDATE ban_sao
             SET xoa_luc = NOW()
             WHERE id = ?
               AND xoa_luc IS NULL
               AND trang_thai NOT IN ('dang_muon', 'dang_giu_cho')",
            [$id]
        );
    }
}
