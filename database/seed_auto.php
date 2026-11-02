<?php
$config = require_once '../app/config/database.php';

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    
    // Tạo bảng loans nếu chưa có để test tích hợp
    $pdo->exec("CREATE TABLE IF NOT EXISTS loans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        copy_id INT NOT NULL,
        borrow_date DATE NOT NULL,
        return_date DATE DEFAULT NULL,
        status VARCHAR(50) DEFAULT 'pending'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    echo "<h3 style='color:green;'>[Thành công] Đã tự động khởi tạo/kiểm tra cấu trúc bảng `loans` thành công!</h3>";
    echo "<p><a href='check_integration.php'>Bấm vào đây để chạy lại công cụ kiểm thử</a></p>";

} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Lỗi khởi tạo dữ liệu: " . $e->getMessage() . "</h3>";
}
?>