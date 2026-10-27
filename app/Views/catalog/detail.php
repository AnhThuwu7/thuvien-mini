<?php 
if (function_exists('view')) {
    view('layouts/header');
} else {
    require_once __DIR__ . '/../layouts/header.php';
}
?>

<div class="container my-4">
    <a href="index.php?url=catalog" style="color: var(--primary); text-decoration: none; font-weight: 500; display: inline-block; margin-bottom: 20px;">&larr; Quay lại danh sách sách</a>

    <div class="card" style="background: var(--card-bg); padding: 30px; border-radius: var(--radius); box-shadow: var(--shadow);">
        <?php if (!empty($book)): ?>
            <h2 style="color: var(--text); margin-bottom: 15px;"><?= e($book['ten_sach']) ?></h2>
            <hr style="border: 0; border-top: 1px solid var(--border); margin-bottom: 20px;">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; line-height: 1.8;">
                <div>
                    <p><strong>ISBN:</strong> <?= e($book['isbn'] ?? 'N/A') ?></p>
                    <p><strong>Nhà xuất bản:</strong> <?= e($book['nha_xuat_ban'] ?? 'Đang cập nhật') ?></p>
                    <p><strong>Năm xuất bản:</strong> <?= e($book['nam_xuat_ban'] ?? 'N/A') ?></p>
                </div>
                <div>
                    <p><strong>Ngôn ngữ:</strong> <?= e(($book['ngon_ngu'] ?? '') === 'vi' ? 'Tiếng Việt' : ($book['ngon_ngu'] ?? 'Tiếng Việt')) ?></p>
                    <p><strong>Ngày nhập:</strong> <?= e($book['tao_luc'] ?? 'N/A') ?></p>
                </div>
            </div>

            <div style="margin-top: 25px;">
                <h4 style="margin-bottom: 10px; color: var(--text);">Mô tả nội dung:</h4>
                <p style="color: var(--text-muted); background: var(--bg); padding: 15px; border-radius: var(--radius-sm);"><?= e($book['mo_ta'] ?? 'Chưa có mô tả chi tiết cho cuốn sách này.') ?></p>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--text-muted); padding: 20px;">Không tìm thấy thông tin cuốn sách.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
if (function_exists('view')) {
    view('layouts/footer');
} else {
    require_once __DIR__ . '/../layouts/footer.php';
}
?>