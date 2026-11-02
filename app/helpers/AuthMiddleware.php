<?php
class AuthMiddleware {
    // Kiểm tra xem người dùng đã đăng nhập chưa
    public static function checkLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?url=auth/login');
            exit();
        }
    }

    // Kiểm tra quyền (Role-based Access Control)
    public static function allowRoles($allowedRoles = []) {
        self::checkLogin();
        
        $userRole = $_SESSION['role'] ?? '';
        if (!in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            echo "<h2 style='color:red; text-align:center; margin-top:50px;'>403 - Truy cập bị từ chối! Bạn không có quyền hạn thực hiện chức năng này.</h2>";
            echo "<p style='text-align:center;'><a href='index.php'>Quay lại trang chủ</a></p>";
            exit();
        }
    }
}
?>