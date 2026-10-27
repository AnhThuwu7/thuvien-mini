<?php
class Book extends Model {
    protected $table = 'sach';

    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE ten_sach LIKE ? OR nha_xuat_ban LIKE ? OR tac_gia LIKE ?";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%" . $keyword . "%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function all() {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->query($sql);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($books as &$book) {
            if (!isset($book['tac_gia'])) {
                $book['tac_gia'] = 'Chưa cập nhật';
            }
        }
        return $books;
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($book && !isset($book['tac_gia'])) {
            $book['tac_gia'] = 'Chưa cập nhật';
        }
        return $book;
    }
}