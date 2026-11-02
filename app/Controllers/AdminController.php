<?php
require_once '../app/helpers/AuthHelper.php';

class AdminController {
    public function index() {
        // Chỉ cho phép Admin và Thủ thư truy cập
        AuthHelper::requireRole(['admin', 'librarian']);

        // Gọi các file view layout tích hợp
        require_once '../app/views/admin/dashboard.php';
    }
}
?>