<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Database.php';

class StatsController extends Controller {
    
    // Trang Dashboard thống kê tổng quan và báo cáo quá hạn
    public function index(): void {
        // Kiểm tra quyền (chỉ Quản trị viên hoặc Thủ thư mới được phép truy cập)
        $this->requireRole('thu_thu', 'quan_tri');
        
        $db = Database::getInstance()->getConnection();
        
        // 1. Thống kê số sách đang mượn
        $stmt1 = $db->query("SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL");
        $dangMuon = (int) $stmt1->fetchColumn();
        
        // 2. Thống kê số lượt quá hạn
        $stmt2 = $db->query("SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL AND han_tra < CURDATE()");
        $quaHan = (int) $stmt2->fetchColumn();
        
        // 3. Lấy danh sách chi tiết các phiếu đang quá hạn để hiển thị báo cáo
        $stmt3 = $db->query("
            SELECT pm.*, nd.ho_ten, nd.email, bs.ma_ban_sao, s.ten_sach 
            FROM phieu_muon pm
            JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
            JOIN ban_sao bs ON pm.ban_sao_id = bs.id
            JOIN sach s ON bs.sach_id = s.id
            WHERE pm.tra_luc IS NULL AND pm.han_tra < CURDATE()
            ORDER BY pm.han_tra ASC
        ");
        $danhSachQuaHan = $stmt3->fetchAll();
        
        // Truyền dữ liệu sang View
        $this->view('stats/index', [
            'dangMuon' => $dangMuon,
            'quaHan' => $quaHan,
            'danhSachQuaHan' => $danhSachQuaHan
        ]);
    }
}