<?php

class Csrf
{
    // Token của phiên hiện tại (tạo nếu chưa có)
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Token gửi lên: ô ẩn "csrf_token" (form) hoặc header X-CSRF-Token (Fetch)
    public static function verify(): bool
    {
        $sent = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $real = $_SESSION['csrf_token'] ?? '';
        return is_string($sent) && $sent !== ''
            && is_string($real) && $real !== ''
            && hash_equals($real, $sent);
    }

    // Gọi tập trung trong public/index.php cho mọi request POST
    public static function guard(): void
    {
        if (self::verify()) {
            return;
        }

        http_response_code(403);
        $wantsJson = stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false
            || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if ($wantsJson) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'CSRF token không hợp lệ hoặc đã hết hạn.'], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: text/html; charset=utf-8');
            echo 'Yêu cầu bị từ chối: CSRF token không hợp lệ hoặc phiên làm việc đã hết hạn. '
               . 'Hãy quay lại, tải lại trang rồi thử lại.';
        }
        exit;
    }
}
