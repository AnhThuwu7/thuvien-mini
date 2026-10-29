<?php 
// Nạp Header chung
if (function_exists('view')) {
    view('layouts/header');
} else {
    $headerPath = __DIR__ . '/../layouts/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
}

// Lấy danh sách sách mới truyền từ Controller, nếu chưa có thì dùng mảng mock data
$newBooks = isset($books) && is_array($books) ? array_slice($books, 0, 4) : [
    ['ten_sach' => 'Lập trình Web PHP cơ bản', 'tac_gia' => 'Nguyễn Văn A', 'nha_xuat_ban' => 'NXB Khoa Học', 'danh_muc_id' => '1'],
    ['ten_sach' => 'Cấu trúc dữ liệu và giải thuật', 'tac_gia' => 'Trần Văn B', 'nha_xuat_ban' => 'NXB Giáo Dục', 'danh_muc_id' => '1'],
    ['ten_sach' => 'Nhà Giả Kim', 'tac_gia' => 'Paulo Coelho', 'nha_xuat_ban' => 'NXB Văn Học', 'danh_muc_id' => '3'],
    ['ten_sach' => 'Đắc Nhân Tâm', 'tac_gia' => 'Dale Carnegie', 'nha_xuat_ban' => 'NXB Tổng Hợp', 'danh_muc_id' => '2']
];
?>

<div class="container my-5" style="max-width: 1100px; margin: 0 auto; padding: 0 15px;">
    <!-- Banner chào mừng & Ô tìm nhanh -->
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; padding: 45px 30px; border-radius: 16px; text-align: center; margin-bottom: 40px; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.2);">
        <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 10px;">Chào mừng đến với Thư Viện Mini</h1>
        <p style="font-size: 15px; opacity: 0.9; margin-bottom: 25px; max-width: 600px; margin-left: auto; margin-right: auto;">Khám phá kho tàng tri thức, tra cứu hàng ngàn đầu sách và quản lý lịch trình mượn trả dễ dàng.</p>
        
        <!-- Ô tìm kiếm nhanh chuyển hướng sang trang tra cứu (index.php) -->
        <form action="index.php" method="GET" style="display: flex; max-width: 550px; margin: 0 auto; background: #fff; padding: 6px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <input type="text" name="keyword" placeholder="Nhập tên sách hoặc tác giả bạn muốn tìm..." style="flex: 1; border: none; padding: 10px 14px; font-size: 14px; outline: none; border-radius: 8px;">
            <button type="submit" style="background: var(--primary, #4f46e5); color: white; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">Tìm kiếm</button>
        </form>
    </div>

    <!-- Phần hiển thị vài cuốn "Sách mới" -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: var(--text); font-weight: 700; font-size: 20px; margin: 0;">📚 Sách mới cập nhật</h2>
        <a href="index.php" style="color: var(--primary, #4f46e5); font-weight: 600; text-decoration: none; font-size: 14px;">Xem tất cả &rarr;</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
        <?php foreach ($newBooks as $book): ?>
            <div style="background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                        <span style="font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase;">Mã DM: <?= htmlspecialchars($book['danh_muc_id'] ?? 'N/A') ?></span>
                        <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Mới</span>
                    </div>
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text); margin: 0 0 6px 0;"><?= htmlspecialchars($book['ten_sach']) ?></h3>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 4px 0;">Tác giả: <?= htmlspecialchars($book['tac_gia']) ?></p>
                    <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 15px 0;">NXB: <?= htmlspecialchars($book['nha_xuat_ban']) ?></p>
                </div>
                
                <a href="index.php?keyword=<?= urlencode($book['ten_sach']) ?>" style="text-align: center; background: #f3f4f6; color: #1f2937; border: 1px solid #d1d5db; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 13px; text-decoration: none; display: block; transition: 0.2s;">Xem chi tiết</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php 
// Nạp Footer chung
if (function_exists('view')) {
    view('layouts/footer');
} else {
    $footerPath = __DIR__ . '/../layouts/footer.php';
    if (file_exists($footerPath)) require_once $footerPath;
}
?>