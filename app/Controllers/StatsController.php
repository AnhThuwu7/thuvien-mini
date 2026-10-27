<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Database.php';

class StatsController extends Controller {
    
    public function index(): void {
        $this->requireRole('thu_thu', 'quan_tri');
        $db = Database::getInstance()->getConnection();
        
        // Lấy từ khóa tìm kiếm nếu có
        $keyword = trim($_GET['keyword'] ?? '');
        
        // 1. Thống kê tổng quan các thẻ
        $dangMuon = (int) $db->query("SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL")->fetchColumn();
        $quaHan = (int) $db->query("SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL AND han_tra < CURDATE()")->fetchColumn();
        $tongDauSach = (int) $db->query("SELECT COUNT(*) FROM sach")->fetchColumn();
        $tongDocGia = (int) $db->query("SELECT COUNT(*) FROM nguoi_dung")->fetchColumn();
        
        // 2. Danh sách chi tiết phiếu quá hạn (có kết hợp điều kiện tìm kiếm theo tên độc giả)
        $sqlQuaHan = "
            SELECT pm.*, nd.ho_ten, nd.email, bs.ma_ban_sao, s.ten_sach,
                   DATEDIFF(CURDATE(), pm.han_tra) AS so_ngay_qua_han
            FROM phieu_muon pm
            JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
            JOIN ban_sao bs ON pm.ban_sao_id = bs.id
            JOIN sach s ON bs.sach_id = s.id
            WHERE pm.tra_luc IS NULL AND pm.han_tra < CURDATE()
        ";
        
        $params = [];
        if (!empty($keyword)) {
            $sqlQuaHan .= " AND nd.ho_ten LIKE ?";
            $params[] = "%$keyword%";
        }
        $sqlQuaHan .= " ORDER BY pm.han_tra ASC";
        
        $stmt3 = $db->prepare($sqlQuaHan);
        $stmt3->execute($params);
        $danhSachQuaHan = $stmt3->fetchAll();

        // 3. Danh sách sắp đến hạn (trong vòng 3 ngày tới)
        $stmtSapDenHan = $db->query("
            SELECT pm.*, nd.ho_ten, nd.email, bs.ma_ban_sao, s.ten_sach,
                   DATEDIFF(pm.han_tra, CURDATE()) AS so_ngay_con_lai
            FROM phieu_muon pm
            JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
            JOIN ban_sao bs ON pm.ban_sao_id = bs.id
            JOIN sach s ON bs.sach_id = s.id
            WHERE pm.tra_luc IS NULL AND pm.han_tra >= CURDATE() AND pm.han_tra <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
            ORDER BY pm.han_tra ASC
        ");
        $danhSachSapDenHan = $stmtSapDenHan->fetchAll();

        // 4. Top 5 sách mượn nhiều nhất
        $stmtTopBooks = $db->query("
            SELECT s.ten_sach, COUNT(pm.id) AS so_luot_muon
            FROM phieu_muon pm
            JOIN ban_sao bs ON pm.ban_sao_id = bs.id
            JOIN sach s ON bs.sach_id = s.id
            GROUP BY s.id, s.ten_sach
            ORDER BY so_luot_muon DESC
            LIMIT 5
        ");
        $topBooks = $stmtTopBooks->fetchAll();
        
        // Truyền dữ liệu sang View
        $this->view('stats/index', [
            'dangMuon'          => $dangMuon,
            'quaHan'            => $quaHan,
            'tongDauSach'       => $tongDauSach,
            'tongDocGia'        => $tongDocGia,
            'danhSachQuaHan'    => $danhSachQuaHan,
            'danhSachSapDenHan' => $danhSachSapDenHan,
            'topBooks'          => $topBooks,
            'keyword'           => $keyword
        ]);
    }
}