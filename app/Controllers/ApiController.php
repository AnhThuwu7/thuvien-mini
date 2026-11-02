<?php

class ApiController extends Controller
{
    // GET /api/users?q=keyword
    public function users(): void
    {
        // Cho phép Thủ thư và Quản trị truy cập API
        $this->requireRole('thu_thu', 'quan_tri');

        $keyword = input('q');
        $userModel = $this->model('User');
        $readers = $userModel->searchReadersForApi($keyword);

        $this->jsonSuccess($readers);
    }

    /**
     * GET /api/stats
     * TV5 – Endpoint JSON tổng quan thống kê
     * (đáp ứng yêu cầu /api/stats/overview trong phân công)
     * Trả về: dang_muon, qua_han, sap_den_han, tong_dau_sach, tong_doc_gia, top_sach
     */
    public function stats(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');

        $db = Database::getInstance()->getConnection();

        $dangMuon = (int) $db->query(
            "SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL"
        )->fetchColumn();

        $quaHan = (int) $db->query(
            "SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL AND han_tra < CURDATE()"
        )->fetchColumn();

        $sapDenHan = (int) $db->query(
            "SELECT COUNT(*) FROM phieu_muon
             WHERE tra_luc IS NULL
               AND han_tra >= CURDATE()
               AND han_tra <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)"
        )->fetchColumn();

        $tongDauSach = (int) $db->query(
            "SELECT COUNT(*) FROM sach WHERE xoa_luc IS NULL"
        )->fetchColumn();

        $tongDocGia = (int) $db->query(
            "SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = 'doc_gia' AND xoa_luc IS NULL"
        )->fetchColumn();

        $topSach = $db->query(
            "SELECT s.id, s.ten_sach, COUNT(pm.id) AS so_luot_muon
             FROM phieu_muon pm
             JOIN ban_sao bs ON pm.ban_sao_id = bs.id
             JOIN sach s ON bs.sach_id = s.id
             GROUP BY s.id, s.ten_sach
             ORDER BY so_luot_muon DESC
             LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->jsonSuccess([
            'dang_muon'     => $dangMuon,
            'qua_han'       => $quaHan,
            'sap_den_han'   => $sapDenHan,
            'tong_dau_sach' => $tongDauSach,
            'tong_doc_gia'  => $tongDocGia,
            'top_sach'      => $topSach,
            'cap_nhat_luc'  => date('Y-m-d H:i:s'),
        ]);
    }
}