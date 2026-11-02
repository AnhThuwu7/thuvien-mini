<?php
// Sử dụng đường dẫn tuyệt đối dựa trên thư mục hiện tại để không bị lỗi 'Failed to open stream'
$configPath = __DIR__ . '/../app/config/database.php';

// Nếu file database.php của bạn thực tế nằm ở chỗ khác, hãy kiểm tra lại đường dẫn cho khớp
if (!file_exists($configPath)) {
    // Thử tìm theo cấu trúc nếu thư mục config nằm ngay trong app
    $configPath = __DIR__ . '/../config/database.php'; 
}

$config = require_once $configPath;

echo "<h2>KẾT QUẢ KIỂM THỬ TÍCH HỢP HỆ THỐNG THUVIEN-MINI</h2>";

try {
    // Khởi tạo kết nối PDO từ mảng config
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
    $conn = new PDO($dsn, $config['username'], $config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green;'>[OK] Kết nối Cơ sở dữ liệu thành công tới database '{$config['dbname']}'!</p>";
} catch (PDOException $e) {
    die("<p style='color:red;'>[LỖI KẾT NỐI] " . $e->getMessage() . "</p>");
}

// 1. Kiểm tra sự tồn tại của các Controller từ TV1 -> TV4
$controllers = [
    'AuthController' => 'Thành viên 1 (Xác thực)',
    'BooksController' => 'Thành viên 2 (Quản lý Sách)',
    'CatalogController' => 'Thành viên 3 (Tra cứu Sách)',
    'LoansController' => 'Thành viên 4 (Mượn - Trả)'
];

echo "<h3>1. Kiểm tra các Module Controller:</h3><ul>";
foreach ($controllers as $file => $author) {
    $path = __DIR__ . "/../app/controllers/{$file}.php";
    if (file_exists($path)) {
        echo "<li style='color:green;'>[OK] {$file}.php - {$author}</li>";
    } else {
        echo "<li style='color:red;'>[THIẾU] {$file}.php - {$author} (Cần gộp file này)</li>";
    }
}
echo "</ul>";

// 2. Kiểm tra dữ liệu liên kết giữa Sách (TV2) và Mượn trả (TV4)
echo "<h3>2. Kiểm tra Dữ liệu liên kết CSDL:</h3>";
try {
    $stmt = $conn->query("SELECT l.id, u.username, b.title 
                          FROM loans l 
                          JOIN users u ON l.user_id = u.id 
                          JOIN copies c ON l.copy_id = c.id 
                          JOIN books b ON c.book_id = b.id 
                          LIMIT 5");
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p style='color:green;'>[OK] Truy vấn liên kết (JOIN) giữa Users, Books và Loans hoạt động bình thường!</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>[LỖI CSDL] Các bảng (loans, users, copies, books) có thể chưa đủ dữ liệu hoặc chưa khớp cấu trúc: " . $e->getMessage() . "</p>";
}
?>