
<div class="page-header">
    <div>
        <h1>Chi tiết sách</h1>
        <p>Thông tin và ảnh bìa của sách.</p>
    </div>

    <a href="<?= e(url('books')) ?>" class="btn btn-outline">
        Quay lại danh sách
    </a>
</div>

<div class="card" style="padding: 1.5rem;">
    <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: flex-start;">

        <div style="width: 220px; max-width: 100%;">
            <?php if (!empty($book['anh_bia'])): ?>
                <img
                    src="<?= e('/thuvien-mini/public/' . ltrim($book['anh_bia'], '/')) ?>"
                    alt="<?= e($book['ten_sach']) ?>"
                    style="display: block; width: 100%; max-height: 340px; object-fit: contain; border: 1px solid var(--border); border-radius: var(--radius);"
                >
            <?php else: ?>
                <div style="padding: 3rem 1rem; text-align: center; border: 1px dashed var(--border); border-radius: var(--radius); color: var(--text-muted);">
                    Chưa có ảnh bìa
                </div>
            <?php endif; ?>
        </div>

        <div style="flex: 1; min-width: 250px;">
            <h2 style="margin-top: 0;">
                <?= e($book['ten_sach']) ?>
            </h2>

            <p><strong>ISBN:</strong>
                <?= e($book['isbn'] ?: 'Chưa cập nhật') ?>
            </p>

            <p><strong>Nhà xuất bản:</strong>
                <?= e($book['nha_xuat_ban'] ?: 'Chưa cập nhật') ?>
            </p>

            <p><strong>Năm xuất bản:</strong>
                <?= e($book['nam_xuat_ban'] ?: 'Chưa cập nhật') ?>
            </p>

            <p><strong>Ngôn ngữ:</strong>
                <?= e($book['ngon_ngu'] ?? 'Chưa cập nhật') ?>
            </p>

            <p><strong>Mô tả:</strong></p>

            <div style="white-space: pre-line; line-height: 1.7;">
                <?= e($book['mo_ta'] ?: 'Chưa có mô tả cho sách này.') ?>
            </div>

            <div style="margin-top: 1.5rem;">
                <a href="<?= e(url('books/edit/' . $book['id'])) ?>"
                   class="btn">
                    Sửa thông tin sách
                </a>

                <a href="<?= e(url('books')) ?>"
                   class="btn btn-outline">
                    Quay lại
                </a>
            </div>
        </div>

    </div>
</div>
