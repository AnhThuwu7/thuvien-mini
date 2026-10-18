<?php

// ===== Cấu hình chung =====
define('APP_DEBUG', true);   // true khi phát triển; ĐỔI THÀNH false khi nộp bài / demo
define('ROOT_PATH', dirname(__DIR__));
// Thư mục web của dự án, ví dụ /thuvien-mini/public  (dùng cho url() và asset())
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
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

// ===== Mọi request POST đều phải có CSRF token hợp lệ =====
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    Csrf::guard();
}

// ===== Chạy Router =====
$router = new Router();
