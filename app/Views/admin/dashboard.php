<?php
// Đảm bảo đã gọi session và kiểm tra quyền
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản trị Hệ thống - Thư viện Mini</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f9; }
        .sidebar { width: 250px; background: #343a40; color: #fff; position: fixed; height: 100%; padding: 20px; }
        .sidebar h2 { font-size: 20px; text-align: center; margin-bottom: 30px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 10px 15px; margin-bottom: 5px; border-radius: 4px; }
        .sidebar a:hover { background: #495057; color: #fff; }
        .main-content { margin-left: 270px; padding: 30px; }
        .card-box { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Thư Viện Mini</h2>
        <a href="index.php?url=admin/index">📊 Tổng quan (Dashboard)</a>
        <a href="index.php?url=books/index">📚 Quản lý Sách (TV2)</a>
        <a href="index.php?url=categories/index">📂 Quản lý Danh mục (TV2)</a>
        <a href="index.php?url=loans/index">📋 Quản lý Mượn/Trả (TV4)</a>
        <a href="index.php?url=auth/logout" style="color: #ff6b6b; margin-top: 50px;">🚪 Đăng xuất</a>
    </div>

    <div class="main-content">
        <h1>Bảng điều khiển Tích hợp hệ thống</h1>
        <p>Xin chào, <b><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></b> (Vai trò: <i><?php echo htmlspecialchars($_SESSION['role'] ?? 'N/A'); ?></i>)</p>
        
        <div class="card-box">
            <div class="card">
                <h3>Trạng thái Tích hợp</h3>
                <p style="color: green; font-weight: bold;">✔ Đã gộp thành công các module Xác thực, Sách, Tra cứu và Mượn trả.</p>
            </div>
            <div class="card">
                <h3>Thao tác nhanh</h3>
                <p><a href="../tests/check_integration.php" target="_blank">Chạy lại công cụ kiểm thử hệ thống</a></p>
            </div>
        </div>
    </div>

</body>
</html>