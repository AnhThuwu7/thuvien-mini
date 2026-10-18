<?php
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}
class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', ['title' => 'Trang chủ']);
    }

    // Kiểm tra nhanh kết nối CSDL, trả JSON. Chỉ hoạt động khi APP_DEBUG = true.
    // (Chỉ để thử khung MVC; các chức năng thật phải truy vấn qua Model.)
    public function ping(): void
    {
        if (!APP_DEBUG) {
            abort(404);
        }
        try {
            $pdo    = Database::getInstance()->getConnection();
            $soSach = (int) $pdo->query('SELECT COUNT(*) FROM sach')->fetchColumn();
            $this->jsonSuccess(['so_sach' => $soSach]);
        } catch (Throwable $e) {
            error_log('[ping] ' . $e->getMessage());
            $this->jsonError('Không truy vấn được CSDL. Đã import schema.sql và seed.sql chưa?', 500);
        }
    }
}
 