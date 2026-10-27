<?php
class Loan extends Model {
    public function getByUser($userId) {
        $sql = "SELECT m.*, s.ten_sach FROM phieu_muon m 
                JOIN sach s ON m.ban_sao_id = s.id 
                WHERE m.nguoi_dung_id = ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}