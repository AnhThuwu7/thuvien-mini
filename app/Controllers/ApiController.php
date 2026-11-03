<?php

class ApiController extends Controller
{
    /**
     * Endpoint nội bộ: chưa đăng nhập → 401 JSON, sai vai trò → 403 JSON
     * (không chuyển hướng sang trang HTML như requireRole vì client là Fetch).
     */
    private function requireStaffJson(): void
    {
        if (!Auth::check()) {
            $this->jsonError('Chưa đăng nhập.', 401);
        }
        if (!Auth::hasRole('thu_thu', 'quan_tri')) {
            $this->jsonError('Bạn không có quyền truy cập.', 403);
        }
    }

    /**
     * GET /api/books?q=keyword  — gợi ý tên sách (autocomplete, Fetch API)
     * Công khai như trang tra cứu. Trả về JSON: {ok:true, data:[{id, ten_sach, tac_gia, con_lai}]}
     * q rỗng hoặc quá ngắn → danh sách rỗng (vẫn là JSON hợp lệ, status 200).
     */
    public function books(): void
    {
        $q = trim(input('q'));

        if (mb_strlen($q) > 100) {
            $this->jsonError('Từ khóa quá dài (tối đa 100 ký tự).', 422);
        }
        if (mb_strlen($q) < 2) {
            $this->jsonSuccess([]);
        }

        $rows = $this->model('Book')->suggest($q, 8);

        $data = array_map(static function (array $r): array {
            return [
                'id'       => (int) $r['id'],
                'ten_sach' => (string) $r['ten_sach'],
                'tac_gia'  => (string) ($r['tac_gia'] ?? ''),
                'con_lai'  => (int) $r['con_lai'],
            ];
        }, $rows);

        $this->jsonSuccess($data);
    }

    // GET /api/users?q=keyword
    public function users(): void
    {
        // Cho phép Thủ thư và Quản trị truy cập API
        $this->requireStaffJson();

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
        $this->requireStaffJson();

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