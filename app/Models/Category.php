
<?php

class Category extends Model
{
    
public function getAll(string $keyword = ''): array
{
    $sql = "SELECT
                dm.id,
                dm.ten,
                dm.mo_ta,
                COUNT(s.id) AS so_sach
            FROM danh_muc dm
            LEFT JOIN sach s
                ON s.danh_muc_id = dm.id
                AND s.xoa_luc IS NULL";

    $params = [];

    if ($keyword !== '') {
        $sql .= " WHERE dm.ten LIKE ? OR dm.mo_ta LIKE ?";
        $params[] = '%' . $keyword . '%';
        $params[] = '%' . $keyword . '%';
    }

    $sql .= " GROUP BY dm.id, dm.ten, dm.mo_ta
              ORDER BY dm.ten ASC";

    return $this->fetchAll($sql, $params);
}


    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT id, ten, mo_ta
             FROM danh_muc
             WHERE id = ?",
            [$id]
        );
    }

    public function isNameExists(string $ten, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $row = $this->fetchOne(
                "SELECT id
                 FROM danh_muc
                 WHERE ten = ? AND id <> ?
                 LIMIT 1",
                [$ten, $excludeId]
            );
        } else {
            $row = $this->fetchOne(
                "SELECT id
                 FROM danh_muc
                 WHERE ten = ?
                 LIMIT 1",
                [$ten]
            );
        }

        return $row !== null;
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO danh_muc (ten, mo_ta)
             VALUES (?, ?)",
            [
                $data['ten'],
                $data['mo_ta'] !== '' ? $data['mo_ta'] : null,
            ]
        );

        return $this->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        return $this->execute(
            "UPDATE danh_muc
             SET ten = ?, mo_ta = ?
             WHERE id = ?",
            [
                $data['ten'],
                $data['mo_ta'] !== '' ? $data['mo_ta'] : null,
                $id,
            ]
        );
    }

    public function hasBooks(int $id): bool
    {
        $row = $this->fetchOne(
            "SELECT id
             FROM sach
             WHERE danh_muc_id = ?
             LIMIT 1",
            [$id]
        );

        return $row !== null;
    }

    public function delete(int $id): int
    {
        return $this->execute(
            "DELETE FROM danh_muc WHERE id = ?",
            [$id]
        );
    }
}
