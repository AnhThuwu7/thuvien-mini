<?php

// ===== Cấu hình chung =====
// Mặc định TẮT debug (an toàn khi nộp bài / demo). Khi lập trình: tạo file rỗng config/.debug để bật.
define('APP_DEBUG', is_file(dirname(__DIR__) . '/config/.debug'));
define('ROOT_PATH', dirname(__DIR__));
// Thư mục web của dự án, ví dụ /thuvien-mini/public  (dùng cho url() và asset())
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
date_default_timezone_set('Asia/Ho_Chi_Minh');

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');

// ===== Session =====
ini_set('session.use_strict_mode', '1');
session_name('THUVIEN_SESSID');   // tên riêng để không đụng session của dự án khác trên localhost
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ===== Nạp core =====
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/helpers.php';
require_once ROOT_PATH . '/core/Csrf.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/LoginThrottle.php';

// ===== Lỗi không bắt được: ghi log, người dùng chỉ thấy trang lỗi chung (không lộ chi tiết) =====
set_exception_handler(static function (Throwable $e): void {
    error_log('[Uncaught] ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());

    if (!headers_sent()) {
        http_response_code(500);
    }
    if (APP_DEBUG) {
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }
    if (wants_json()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Lỗi hệ thống.'], JSON_UNESCAPED_UNICODE);
        return;
    }
    $message = 'Đã xảy ra lỗi. Vui lòng thử lại sau.';
    require ROOT_PATH . '/app/Views/errors/500.php';
});

// ===== Mọi request POST đều phải có CSRF token hợp lệ =====
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    Csrf::guard();
}

// ===== Chạy Router =====
$router = new Router();