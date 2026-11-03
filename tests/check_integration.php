<?php
/**
 * Kiểm tra nhanh (smoke test) tính toàn vẹn của hệ thống Thư viện Mini.
 *
 * Cách chạy (khuyên dùng, tại thư mục gốc dự án):
 *     php tests/check_integration.php
 * Thư mục tests/ nằm ngoài public/ nên không chạy qua trình duyệt được.
 *
 * Kiểm tra: kết nối CSDL, đủ bảng, đủ file controller, tài khoản demo cho từng vai trò,
 * mật khẩu đã băm, và các quy tắc nghiệp vụ cốt lõi của chủ đề 2 (mượn/trả/giữ chỗ).
 * Kết thúc bằng mã thoát 0 nếu mọi mục đạt, 1 nếu có mục lỗi.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Chạy bằng dòng lệnh: php tests/check_integration.php');
}

$root = dirname(__DIR__);
$failed = 0;

function check(string $label, bool $ok, string $detail = ''): void
{
    global $failed;
    if (!$ok) {
        $failed++;
    }
    echo ($ok ? '[ĐẠT]  ' : '[LỖI]  ') . $label . ($detail !== '' ? ' — ' . $detail : '') . PHP_EOL;
}

// ---- 1. Kết nối CSDL -------------------------------------------------------
echo "== 1. Kết nối CSDL ==" . PHP_EOL;
$configPath = $root . '/config/database.php';
if (!is_file($configPath)) {
    exit("Không thấy config/database.php (sao chép từ config/database.example.php)." . PHP_EOL);
}
$config = require $configPath;

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'] ?? 3306,
        $config['dbname'],
        $config['charset'] ?? 'utf8mb4'
    );
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    check('Kết nối được CSDL "' . $config['dbname'] . '"', true);
} catch (PDOException $e) {
    exit('[LỖI]  Không kết nối được CSDL: ' . $e->getMessage() . PHP_EOL);
}

function scalar(PDO $pdo, string $sql): int
{
    return (int) $pdo->query($sql)->fetchColumn();
}

// ---- 2. Bảng và file --------------------------------------------------------
echo PHP_EOL . "== 2. Cấu trúc ==" . PHP_EOL;
$tables = ['cau_hinh', 'nguoi_dung', 'danh_muc', 'tac_gia', 'sach', 'sach_tac_gia',
           'ban_sao', 'yeu_cau_muon', 'phieu_muon', 'nhat_ky_muon'];
$existing = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    check('Có bảng ' . $t, in_array($t, $existing, true));
}

$files = [
    'app/Controllers/AuthController.php', 'app/Controllers/BooksController.php',
    'app/Controllers/CatalogController.php', 'app/Controllers/CategoriesController.php',
    'app/Controllers/CopiesController.php', 'app/Controllers/LoansController.php',
    'app/Controllers/LoanRequestsController.php', 'app/Controllers/ReportsController.php',
    'app/Controllers/UsersController.php', 'app/Controllers/SettingsController.php',
    'app/Controllers/ApiController.php', 'core/Router.php', 'core/Csrf.php', 'core/Auth.php',
];
foreach ($files as $f) {
    check('Có file ' . $f, is_file($root . '/' . $f));
}

// ---- 3. Tài khoản và dữ liệu mẫu -------------------------------------------
echo PHP_EOL . "== 3. Tài khoản & dữ liệu mẫu ==" . PHP_EOL;
foreach (['quan_tri', 'thu_thu', 'doc_gia'] as $role) {
    $n = scalar($pdo, "SELECT COUNT(*) FROM nguoi_dung WHERE vai_tro = '$role' AND bi_khoa = 0 AND xoa_luc IS NULL");
    check('Có tài khoản demo vai trò ' . $role, $n >= 1, $n . ' tài khoản');
}
$plain = scalar($pdo, "SELECT COUNT(*) FROM nguoi_dung WHERE mat_khau_bam NOT LIKE '\$2y\$%' AND mat_khau_bam NOT LIKE '\$argon2%'");
check('Mọi mật khẩu đều đã băm (password_hash)', $plain === 0, $plain . ' tài khoản chưa băm');
check('Có sách mẫu', scalar($pdo, 'SELECT COUNT(*) FROM sach WHERE xoa_luc IS NULL') > 0);
check('Có bản sao mẫu', scalar($pdo, 'SELECT COUNT(*) FROM ban_sao WHERE xoa_luc IS NULL') > 0);

// ---- 4. Quy tắc nghiệp vụ ---------------------------------------------------
echo PHP_EOL . "== 4. Quy tắc nghiệp vụ (dữ liệu phải nhất quán) ==" . PHP_EOL;

$n = scalar($pdo, "SELECT COUNT(*) FROM ban_sao bs
                   WHERE bs.xoa_luc IS NULL AND bs.trang_thai = 'dang_muon'
                     AND NOT EXISTS (SELECT 1 FROM phieu_muon pm WHERE pm.ban_sao_id = bs.id AND pm.tra_luc IS NULL)");
check('Bản sao "đang mượn" đều có phiếu mượn chưa trả', $n === 0, $n . ' bản sao lệch');

$n = scalar($pdo, "SELECT COUNT(*) FROM phieu_muon pm
                   JOIN ban_sao bs ON bs.id = pm.ban_sao_id
                   WHERE pm.tra_luc IS NULL AND bs.trang_thai <> 'dang_muon'");
check('Phiếu chưa trả đều ứng với bản sao "đang mượn"', $n === 0, $n . ' phiếu lệch');

$n = scalar($pdo, "SELECT COUNT(*) FROM ban_sao bs
                   WHERE bs.xoa_luc IS NULL AND bs.trang_thai = 'dang_giu_cho'
                     AND NOT EXISTS (SELECT 1 FROM yeu_cau_muon yc WHERE yc.ban_sao_id = bs.id AND yc.trang_thai = 'da_duyet')");
check('Bản sao "đang giữ chỗ" đều có yêu cầu đã duyệt', $n === 0, $n . ' bản sao lệch');

$n = scalar($pdo, "SELECT COUNT(*) FROM (SELECT ban_sao_id FROM phieu_muon WHERE tra_luc IS NULL
                   GROUP BY ban_sao_id HAVING COUNT(*) > 1) t");
check('Mỗi bản sao tối đa một lượt mượn chưa trả', $n === 0, $n . ' bản sao bị trùng');

$n = scalar($pdo, "SELECT COUNT(*) FROM phieu_muon WHERE han_tra < DATE(muon_luc) OR (tra_luc IS NOT NULL AND tra_luc < muon_luc)");
check('Hạn trả và ngày trả không nhỏ hơn ngày mượn', $n === 0, $n . ' phiếu sai');

$n = scalar($pdo, "SELECT COUNT(*) FROM phieu_muon pm JOIN nguoi_dung nd ON nd.id = pm.nguoi_dung_id
                   WHERE pm.tra_luc IS NULL AND nd.bi_khoa = 1");
check('Không có tài khoản bị khóa đang giữ sách (thông tin tham khảo)', $n === 0, $n . ' tài khoản (có thể bị khóa sau khi mượn)');

$max = (int) ($pdo->query("SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_sach_toi_da'")->fetchColumn() ?: 5);
$n = scalar($pdo, "SELECT COUNT(*) FROM (SELECT nguoi_dung_id FROM phieu_muon WHERE tra_luc IS NULL
                   GROUP BY nguoi_dung_id HAVING COUNT(*) > $max) t");
check('Không độc giả nào vượt hạn mức ' . $max . ' sách', $n === 0, $n . ' độc giả vượt');

echo PHP_EOL . ($failed === 0 ? 'TẤT CẢ ĐẠT.' : "CÓ $failed MỤC LỖI.") . PHP_EOL;
exit($failed === 0 ? 0 : 1);