<?php
/**
 * TV5 – Báo cáo & Thống kê
 * Routes:
 *   GET  /reports/stats           → Dashboard thống kê
 *   GET  /reports/overdue         → Báo cáo quá hạn / sắp đến hạn
 *   GET  /reports/export-overdue  → Xuất CSV quá hạn (UTF-8 BOM)
 */
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Database.php';

class ReportsController extends Controller
{
    /** Dashboard thống kê tổng quan */
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

        $tongBanSao = (int) $db->query(
            "SELECT COUNT(*) FROM ban_sao WHERE xoa_luc IS NULL"
        )->fetchColumn();

        $tongDocGia = (int) $db->query(
            "SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = 'doc_gia' AND xoa_luc IS NULL"
        )->fetchColumn();

        $tongLuotMuon = (int) $db->query(
            "SELECT COUNT(*) FROM phieu_muon"
        )->fetchColumn();

        // Top 5 sách mượn nhiều nhất
        $topBooks = $db->query(
            "SELECT s.id, s.ten_sach, COUNT(pm.id) AS so_luot_muon
             FROM phieu_muon pm
             JOIN ban_sao bs ON pm.ban_sao_id = bs.id
             JOIN sach s ON bs.sach_id = s.id
             GROUP BY s.id, s.ten_sach
             ORDER BY so_luot_muon DESC
             LIMIT 5"
        )->fetchAll(PDO::FETCH_ASSOC);

        // Lượt mượn theo tháng (năm hiện tại)
        $theoThang = $db->query(
            "SELECT MONTH(muon_luc) AS thang, COUNT(*) AS so_luot
             FROM phieu_muon
             WHERE YEAR(muon_luc) = YEAR(CURDATE())
             GROUP BY MONTH(muon_luc)
             ORDER BY thang ASC"
        )->fetchAll(PDO::FETCH_ASSOC);

        // Phân bố trạng thái bản sao
        $trangThaiBanSao = $db->query(
            "SELECT trang_thai, COUNT(*) AS so_luong
             FROM ban_sao
             WHERE xoa_luc IS NULL
             GROUP BY trang_thai"
        )->fetchAll(PDO::FETCH_ASSOC);

        $this->view('reports/stats', [
            'title'           => 'Thống kê thư viện',
            'dangMuon'        => $dangMuon,
            'quaHan'          => $quaHan,
            'sapDenHan'       => $sapDenHan,
            'tongDauSach'     => $tongDauSach,
            'tongBanSao'      => $tongBanSao,
            'tongDocGia'      => $tongDocGia,
            'tongLuotMuon'    => $tongLuotMuon,
            'topBooks'        => $topBooks,
            'theoThang'       => $theoThang,
            'trangThaiBanSao' => $trangThaiBanSao,
        ]);
    }

    /**
     * Báo cáo quá hạn / sắp đến hạn
     * Query params:
     *   keyword   – tìm theo tên độc giả / email / mã bản sao / tên sách
     *   min_days  – chỉ hiện phiếu trễ ≥ N ngày (mặc định 0 = tất cả quá hạn)
     *   type      – overdue | soon | all  (mặc định overdue)
     */
    public function overdue(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $db = Database::getInstance()->getConnection();

        $keyword  = trim($_GET['keyword'] ?? '');
        $minDays  = max(0, (int) ($_GET['min_days'] ?? 0));
        $type     = $_GET['type'] ?? 'overdue';
        if (!in_array($type, ['overdue', 'soon', 'all'], true)) {
            $type = 'overdue';
        }

        $phiPhatMoiNgay  = (int) ($this->model('Setting')->getValue('tien_phat_moi_ngay', 2000));
        $soNgaySapDenHan = (int) ($this->model('Setting')->getValue('so_ngay_sap_den_han', 3));

        $params = [];
        $where  = ['pm.tra_luc IS NULL'];

        if ($type === 'overdue') {
            $where[] = 'pm.han_tra < CURDATE()';
            if ($minDays > 0) {
                $where[]  = 'DATEDIFF(CURDATE(), pm.han_tra) >= ?';
                $params[] = $minDays;
            }
        } elseif ($type === 'soon') {
            $where[]  = 'pm.han_tra >= CURDATE()';
            $where[]  = 'pm.han_tra <= DATE_ADD(CURDATE(), INTERVAL ? DAY)';
            $params[] = $soNgaySapDenHan;
        }
        // type=all: chỉ chưa trả

        if ($keyword !== '') {
            $where[]  = '(nd.ho_ten LIKE ? OR nd.email LIKE ? OR bs.ma_ban_sao LIKE ? OR s.ten_sach LIKE ?)';
            $like     = '%' . $keyword . '%';
            $params   = array_merge($params, [$like, $like, $like, $like]);
        }

        $sql = "
            SELECT
                pm.id,
                pm.muon_luc,
                pm.han_tra,
                pm.tien_phat,
                nd.id          AS nguoi_dung_id,
                nd.ho_ten,
                nd.email,
                nd.so_dien_thoai,
                nd.bi_khoa,
                bs.ma_ban_sao,
                s.id           AS sach_id,
                s.ten_sach,
                DATEDIFF(CURDATE(), pm.han_tra) AS so_ngay_qua_han,
                CASE
                    WHEN pm.han_tra < CURDATE()
                    THEN DATEDIFF(CURDATE(), pm.han_tra) * ?
                    ELSE 0
                END AS phi_phat_uoc_tinh
            FROM phieu_muon pm
            JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
            JOIN ban_sao bs ON pm.ban_sao_id = bs.id
            JOIN sach s ON bs.sach_id = s.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY pm.han_tra ASC
        ";

        // Thêm param phí phạt vào đầu (dùng trong SELECT)
        array_unshift($params, $phiPhatMoiNgay);

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $soQuaHan = (int) $db->query(
            "SELECT COUNT(*) FROM phieu_muon WHERE tra_luc IS NULL AND han_tra < CURDATE()"
        )->fetchColumn();

        $soSapDenHan = (int) $db->query(
            "SELECT COUNT(*) FROM phieu_muon
             WHERE tra_luc IS NULL
               AND han_tra >= CURDATE()
               AND han_tra <= DATE_ADD(CURDATE(), INTERVAL {$soNgaySapDenHan} DAY)"
        )->fetchColumn();

        $this->view('reports/overdue', [
            'title'           => 'Báo cáo quá hạn / sắp đến hạn',
            'danhSach'        => $danhSach,
            'keyword'         => $keyword,
            'minDays'         => $minDays,
            'type'            => $type,
            'soQuaHan'        => $soQuaHan,
            'soSapDenHan'     => $soSapDenHan,
            'phiPhatMoiNgay'  => $phiPhatMoiNgay,
            'soNgaySapDenHan' => $soNgaySapDenHan,
        ]);
    }

    /**
     * Xuất CSV danh sách quá hạn (UTF-8 BOM để Excel mở đúng tiếng Việt)
     * GET /reports/export-overdue?min_days=0
     */
    public function exportOverdue(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $db = Database::getInstance()->getConnection();

        $minDays = max(0, (int) ($_GET['min_days'] ?? 0));
        $phiPhatMoiNgay = (int) ($this->model('Setting')->getValue('tien_phat_moi_ngay', 2000));

        $params = [$phiPhatMoiNgay];
        $extra  = '';
        if ($minDays > 0) {
            $extra    = ' AND DATEDIFF(CURDATE(), pm.han_tra) >= ?';
            $params[] = $minDays;
        }

        $sql = "
            SELECT
                pm.id,
                nd.ho_ten,
                nd.email,
                nd.so_dien_thoai,
                s.ten_sach,
                bs.ma_ban_sao,
                DATE(pm.muon_luc) AS ngay_muon,
                pm.han_tra,
                DATEDIFF(CURDATE(), pm.han_tra) AS so_ngay_qua_han,
                DATEDIFF(CURDATE(), pm.han_tra) * ? AS phi_phat_uoc_tinh
            FROM phieu_muon pm
            JOIN nguoi_dung nd ON pm.nguoi_dung_id = nd.id
            JOIN ban_sao bs ON pm.ban_sao_id = bs.id
            JOIN sach s ON bs.sach_id = s.id
            WHERE pm.tra_luc IS NULL AND pm.han_tra < CURDATE()
            {$extra}
            ORDER BY pm.han_tra ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = 'bao_cao_qua_han_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM để Excel nhận đúng tiếng Việt
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($out, [
            'ID Phiếu',
            'Họ tên độc giả',
            'Email',
            'Số điện thoại',
            'Tên sách',
            'Mã bản sao',
            'Ngày mượn',
            'Hạn trả',
            'Số ngày quá hạn',
            'Phí phạt ước tính (VNĐ)',
        ], ';');

        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'],
                $r['ho_ten'],
                $r['email'],
                $r['so_dien_thoai'] ?? '',
                $r['ten_sach'],
                $r['ma_ban_sao'],
                $r['ngay_muon'],
                $r['han_tra'],
                $r['so_ngay_qua_han'],
                number_format((float) $r['phi_phat_uoc_tinh'], 0, ',', '.'),
            ], ';');
        }

        fclose($out);
        exit;
    }
}