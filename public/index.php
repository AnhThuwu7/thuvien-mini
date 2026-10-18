<?php
declare(strict_types=1);

/**
 * Cửa vào duy nhất của ứng dụng (front controller).
 * Mọi URL đều đi qua file này nhờ public/.htaccess.
 */

define('ROOT_PATH', dirname(__DIR__));

// true = hiện lỗi chi tiết (chỉ khi đang phát triển). PHẢI đặt false khi nộp bài / triển khai.
define('APP_DEBUG', true);

date_default_timezone_set('Asia/Ho_Chi_Minh');

error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');

// Header bảo mật cơ bản
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

// Session: cookie chỉ gửi qua HTTP (JS không đọc được)
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Nạp phần lõi (thứ tự quan trọng)
require ROOT_PATH . '/core/helpers.php';
require ROOT_PATH . '/core/Database.php';
require ROOT_PATH . '/core/Model.php';
require ROOT_PATH . '/core/Controller.php';
require ROOT_PATH . '/core/Router.php';

new Router();
