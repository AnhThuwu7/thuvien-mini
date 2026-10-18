<?php

class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Trả về mọi dòng (mảng rỗng nếu không có)
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Trả về 1 dòng, hoặc null nếu không có
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    // INSERT / UPDATE / DELETE: trả về số dòng bị ảnh hưởng
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    protected function lastInsertId(): int
    {
        return (int) $this->db->lastInsertId();
    }

    // Chạy $fn trong 1 transaction: thành công -> commit, có lỗi -> rollback rồi ném lại lỗi
    // Dùng cho mượn / trả sách (cập nhật phieu_muon và ban_sao phải cùng thành công hoặc cùng hủy)
    protected function transaction(callable $fn)
    {
        $this->db->beginTransaction();
        try {
            $result = $fn();
            $this->db->commit();
            return $result;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}