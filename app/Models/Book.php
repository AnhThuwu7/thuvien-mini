
<?php

class Book extends Model
{
    public function getAll(string $keyword = ''): array
    {
        $sql = "SELECT s.id, s.isbn, s.ten_sach, s.nha_xuat_ban,
                       s.nam_xuat_ban, s.ngon_ngu, s.danh_muc_id,
                       dm.ten AS ten_danh_muc,
                       (SELECT COUNT(*)
                          FROM ban_sao bs
                         WHERE bs.sach_id = s.id
                           AND bs.xoa_luc IS NULL) AS tong_ban_sao
                  FROM sach s
                  JOIN danh_muc dm ON dm.id = s.danh_muc_id
                 WHERE s.xoa_luc IS NULL";

        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (
                        s.ten_sach LIKE ?
                        OR s.isbn LIKE ?
                        OR s.nha_xuat_ban LIKE ?
                        OR dm.ten LIKE ?
                    )";

            $kw = '%' . $keyword . '%';
            $params = [$kw, $kw, $kw, $kw];
        }

        $sql .= " ORDER BY s.id DESC";

        return $this->fetchAll($sql, $params);
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT *
               FROM sach
              WHERE id = ?
                AND xoa_luc IS NULL",
            [$id]
        );
    }

    public function getCategories(): array
    {
        return $this->fetchAll(
            "SELECT id, ten
               FROM danh_muc
              ORDER BY ten ASC"
        );
    }

    public function isIsbnExists(string $isbn, ?int $excludeId = null): bool
    {
        if ($isbn === '') {
            return false;
        }

        $sql = "SELECT COUNT(*) AS total
                  FROM sach
                 WHERE isbn = ?";

        $params = [$isbn];

        if ($excludeId !== null) {
            $sql .= " AND id <> ?";
            $params[] = $excludeId;
        }

        $row = $this->fetchOne($sql, $params);

        return (int) ($row['total'] ?? 0) > 0;
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO sach
                (danh_muc_id, isbn, ten_sach, nha_xuat_ban,
                 nam_xuat_ban, ngon_ngu, mo_ta, anh_bia)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['danh_muc_id'],
                $data['isbn'] !== '' ? $data['isbn'] : null,
                $data['ten_sach'],
                $data['nha_xuat_ban'] !== '' ? $data['nha_xuat_ban'] : null,
                $data['nam_xuat_ban'] !== '' ? $data['nam_xuat_ban'] : null,
                $data['ngon_ngu'],
                $data['mo_ta'] !== '' ? $data['mo_ta'] : null,
                $data['anh_bia'] ?? null,
            ]
        );

        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE sach
                   SET danh_muc_id = ?,
                       isbn = ?,
                       ten_sach = ?,
                       nha_xuat_ban = ?,
                       nam_xuat_ban = ?,
                       ngon_ngu = ?,
                       mo_ta = ?";

        $params = [
            $data['danh_muc_id'],
            $data['isbn'] !== '' ? $data['isbn'] : null,
            $data['ten_sach'],
            $data['nha_xuat_ban'] !== '' ? $data['nha_xuat_ban'] : null,
            $data['nam_xuat_ban'] !== '' ? $data['nam_xuat_ban'] : null,
            $data['ngon_ngu'],
            $data['mo_ta'] !== '' ? $data['mo_ta'] : null,
        ];

        // Chỉ cập nhật ảnh khi có ảnh mới được tải lên.
        // Nếu không chọn ảnh mới, giữ nguyên ảnh hiện tại.
        if (!empty($data['anh_bia'])) {
            $sql .= ", anh_bia = ?";
            $params[] = $data['anh_bia'];
        }

        $sql .= " WHERE id = ? AND xoa_luc IS NULL";
        $params[] = $id;

        return $this->execute($sql, $params) > 0;
    }

    public function softDelete(int $id): bool
    {
        $book = $this->findById($id);

        if ($book === null) {
            return false;
        }

        $copies = $this->fetchOne(
            "SELECT COUNT(*) AS total
               FROM ban_sao
              WHERE sach_id = ?
                AND xoa_luc IS NULL
                AND trang_thai <> 'thanh_ly'",
            [$id]
        );

        if ((int) ($copies['total'] ?? 0) > 0) {
            throw new RuntimeException(
                'Không thể xóa sách còn bản sao chưa thanh lý.'
            );
        }

        return $this->execute(
            "UPDATE sach
                SET xoa_luc = NOW()
              WHERE id = ?
                AND xoa_luc IS NULL",
            [$id]
        ) > 0;
    }
}
