<?php
class AuthHelper {
    // Kiểm tra đã đăng nhập chưa
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Yêu cầu quyền truy cập (vd: 'admin', 'librarian', 'reader')
    public static function requireRole($roles = []) {
        if (!self::isLoggedIn()) {
            header('Location: index.php?url=auth/login');
            exit();
        }

        if (!empty($roles) && !in_array($_SESSION['role'], $roles)) {
            http_response_code(403);
            echo "<h2>403 - Bạn không có quyền truy cập chức năng này!</h2>";
            exit();
        }
    }
}
?>