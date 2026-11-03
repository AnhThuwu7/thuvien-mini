<?php

class Book extends Model
{
    /**
     * Điều kiện tìm kiếm dùng chung cho getAll() và countBooks().
     * @return array{0:string,1:array} [đoạn SQL bắt đầu bằng " AND ...", tham số]
     */
    private function keywordFilter(string $keyword): array
    {
        if ($keyword === '') {
            return ['', []];
        }

        $sql = " AND (
                        s.ten_sach LIKE ?
                        OR s.isbn LIKE ?
                        OR s.nha_xuat_ban LIKE ?
                        OR dm.ten LIKE ?
                        OR EXISTS (
                            SELECT 1 FROM sach_tac_gia stg2
                            INNER JOIN tac_gia tg2 ON tg2.id = stg2.tac_gia_id
                            WHERE stg2.sach_id = s.id AND tg2.ten LIKE ?
                        )
                    )";
        $kw = '%' . $keyword . '%';

        return [$sql, [$kw, $kw, $kw, $kw, $kw]];
    }

    /** Tổng số đầu sách khớp từ khóa (dùng cho phân trang). */
    public function countBooks(string $keyword = ''): int
    {
        [$where, $params] = $this->keywordFilter($keyword);

        $row = $this->fetchOne(
            "SELECT COUNT(*) AS total
               FROM sach s
               JOIN danh_muc dm ON dm.id = s.danh_muc_id
              WHERE s.xoa_luc IS NULL" . $where,
            $params
        );

        return (int) ($row['total'] ?? 0);
    }

    /**
     * Danh sách sách. $limit > 0 thì phân trang (LIMIT/OFFSET là số nguyên nên an toàn để nối chuỗi).
     */
    public function getAll(string $keyword = '', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT s.id, s.isbn, s.ten_sach, s.nha_xuat_ban,
                       s.nam_xuat_ban, s.ngon_ngu, s.danh_muc_id,
                       s.mo_ta, s.anh_bia,
                       dm.ten AS ten_danh_muc,
                       (SELECT GROUP_CONCAT(tg.ten ORDER BY tg.ten SEPARATOR ', ')
                          FROM sach_tac_gia stg
                          INNER JOIN tac_gia tg ON tg.id = stg.tac_gia_id
                         WHERE stg.sach_id = s.id) AS ten_tac_gia,
                       (SELECT COUNT(*)
                          FROM ban_sao bs
                         WHERE bs.sach_id = s.id
                           AND bs.xoa_luc IS NULL) AS tong_ban_sao,
                       (SELECT COUNT(*)
                          FROM ban_sao bs
                         WHERE bs.sach_id = s.id
                           AND bs.xoa_luc IS NULL
                           AND bs.trang_thai = 'con_trong') AS so_con_trong
                  FROM sach s
                  JOIN danh_muc dm ON dm.id = s.danh_muc_id
                 WHERE s.xoa_luc IS NULL";

        [$where, $params] = $this->keywordFilter($keyword);
        $sql .= $where;

        $sql .= " ORDER BY s.id DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int) $limit . " OFFSET " . max(0, (int) $offset);
        }

        return $this->fetchAll($sql, $params);
    }

    public function all(): array
    {
        return $this->getAll('');
    }

    public function search(string $keyword): array
    {
        return $this->getAll(trim($keyword));
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

    /**
     * Gợi ý tên sách cho autocomplete (Fetch API).
     * Trả về tối đa $limit đầu sách, kèm số bản sao còn trống.
     */
    public function suggest(string $keyword, int $limit = 8): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return [];
        }

        $limit = max(1, min(20, $limit));
        // Escape ký tự đặc biệt của LIKE để người dùng gõ % hoặc _ không làm sai kết quả
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);

        return $this->fetchAll(
            "SELECT s.id,
                    s.ten_sach,
                    (SELECT GROUP_CONCAT(tg.ten ORDER BY tg.ten SEPARATOR ', ')
                       FROM sach_tac_gia stg
                       INNER JOIN tac_gia tg ON tg.id = stg.tac_gia_id
                      WHERE stg.sach_id = s.id) AS tac_gia,
                    (SELECT COUNT(*)
                       FROM ban_sao bs
                      WHERE bs.sach_id = s.id
                        AND bs.xoa_luc IS NULL
                        AND bs.trang_thai = 'con_trong') AS con_lai
             FROM sach s
             WHERE s.xoa_luc IS NULL
               AND s.ten_sach LIKE ?
             ORDER BY (s.ten_sach LIKE ?) DESC, s.ten_sach ASC
             LIMIT " . $limit,
            ['%' . $escaped . '%', $escaped . '%']
        );
    }

    public function getAuthorsForBook(int $bookId): string
    {
        $row = $this->fetchOne(
            "SELECT GROUP_CONCAT(tg.ten ORDER BY tg.ten SEPARATOR ', ') AS ten_tac_gia
             FROM sach_tac_gia stg
             INNER JOIN tac_gia tg ON tg.id = stg.tac_gia_id
             WHERE stg.sach_id = ?",
            [$bookId]
        );
        return (string) ($row['ten_tac_gia'] ?? '');
    }

    /**
     * Đồng bộ danh sách tác giả cho sách.
     * $names: chuỗi tên cách nhau bằng dấu phẩy. Rỗng = xóa hết liên kết.
     */
    public function syncAuthors(int $bookId, string $names): void
    {
        // Xóa liên kết cũ
        $this->execute(
            "DELETE FROM sach_tac_gia WHERE sach_id = ?",
            [$bookId]
        );

        $names = trim($names);
        if ($names === '') {
            return;
        }

        $parts = preg_split('/\s*,\s*/u', $names) ?: [];
        $order = 1;

        foreach ($parts as $name) {
            $name = trim($name);
            if ($name === '' || mb_strlen($name) > 150) {
                continue;
            }

            // Tìm hoặc tạo tác giả
            $existing = $this->fetchOne(
                "SELECT id FROM tac_gia WHERE ten = ? LIMIT 1",
                [$name]
            );

            if ($existing) {
                $authorId = (int) $existing['id'];
            } else {
                $this->execute(
                    "INSERT INTO tac_gia (ten) VALUES (?)",
                    [$name]
                );
                $authorId = (int) $this->lastInsertId();
            }

            $this->execute(
                "INSERT INTO sach_tac_gia (sach_id, tac_gia_id, thu_tu)
                 VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE thu_tu = VALUES(thu_tu)",
                [$bookId, $authorId, $order]
            );
            $order++;
        }
    }
}