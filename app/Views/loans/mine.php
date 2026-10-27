<?php 
// Nhúng header chung chuẩn MVC
if (function_exists('view')) {
    view('layouts/header');
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>

<div class="container my-4">
    <h2 class="mb-4" style="color: var(--text);">Sách đang mượn & Lịch sử mượn</h2>

    <div class="card" style="background: var(--card-bg); padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow);">
        <?php if (!empty($loans)): ?>
            <table class="table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                        <th style="padding: 10px;">Tên sách</th>
                        <th style="padding: 10px;">Ngày mượn</th>
                        <th style="padding: 10px;">Hạn trả</th>
                        <th style="padding: 10px;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 10px; font-weight: 500;"><?= e($loan['ten_sach'] ?? 'N/A') ?></td>
                            <td style="padding: 10px;"><?= e($loan['ngay_muon'] ?? '') ?></td>
                            <td style="padding: 10px;"><?= e($loan['han_tra'] ?? '') ?></td>
                            <td style="padding: 10px; color: var(--primary); font-weight: 500;"><?= e($loan['trang_thai'] ?? 'Đang mượn') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: var(--text-muted); padding: 20px;">Bạn chưa mượn cuốn sách nào.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
// Nhúng footer chung
if (function_exists('view')) {
    view('layouts/footer');
} else {
    // require_once __DIR__ . '/../layouts/footer.php'; 
}
?>