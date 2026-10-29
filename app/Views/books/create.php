
<?php
$form = $form ?? [];
$errors = $errors ?? [];
$categories = $categories ?? [];
?>

<section style="max-width: 850px; margin: 0 auto; padding: 1.5rem 0;">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.8rem; margin-bottom: 0.4rem;">
            Thêm sách mới
        </h1>
        <p style="color: var(--text-muted);">
            Nhập thông tin đầu sách vào thư viện.
        </p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" style="margin-bottom: 1rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="alert alert-warning">
            Chưa có danh mục sách. Bạn cần tạo danh mục trước khi thêm sách.
        </div>
    <?php endif; ?>

    
<form method="POST"
      action="<?= e(url('books/store')) ?>"
      enctype="multipart/form-data"

          style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem;">

        <?= csrf_field() ?>

        <div style="margin-bottom: 1.25rem;">
            <label for="ten_sach" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                Tên sách <span style="color: #dc2626;">*</span>
            </label>
            <input
                id="ten_sach"
                name="ten_sach"
                type="text"
                maxlength="255"
                required
                value="<?= e($form['ten_sach'] ?? '') ?>"
                placeholder="Nhập tên sách"
                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
            >
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem;">
            <div style="margin-bottom: 1rem;">
                <label for="isbn" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    ISBN
                </label>
                <input
                    id="isbn"
                    name="isbn"
                    type="text"
                    maxlength="20"
                    value="<?= e($form['isbn'] ?? '') ?>"
                    placeholder="Có thể để trống"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
                >
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="danh_muc_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Danh mục <span style="color: #dc2626;">*</span>
                </label>
                <select
                    id="danh_muc_id"
                    name="danh_muc_id"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
                >
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?= (int) $category['id'] ?>"
                            <?= (string) ($form['danh_muc_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>
                        >
                            <?= e($category['ten']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="nha_xuat_ban" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Nhà xuất bản
                </label>
                <input
                    id="nha_xuat_ban"
                    name="nha_xuat_ban"
                    type="text"
                    maxlength="150"
                    value="<?= e($form['nha_xuat_ban'] ?? '') ?>"
                    placeholder="Nhập nhà xuất bản"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
                >
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="nam_xuat_ban" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Năm xuất bản
                </label>
                <input
                    id="nam_xuat_ban"
                    name="nam_xuat_ban"
                    type="number"
                    min="1000"
                    max="2100"
                    value="<?= e($form['nam_xuat_ban'] ?? '') ?>"
                    placeholder="Ví dụ: 2025"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
                >
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="ngon_ngu" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Ngôn ngữ
                </label>
                <select
                    id="ngon_ngu"
                    name="ngon_ngu"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
                >
                    <option value="vi" <?= ($form['ngon_ngu'] ?? 'vi') === 'vi' ? 'selected' : '' ?>>
                        Tiếng Việt
                    </option>
                    <option value="en" <?= ($form['ngon_ngu'] ?? 'vi') === 'en' ? 'selected' : '' ?>>
                        Tiếng Anh
                    </option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="mo_ta" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                Mô tả
            </label>
            <textarea
                id="mo_ta"
                name="mo_ta"
                rows="4"
                placeholder="Nhập mô tả sách nếu có"
                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); resize: vertical;"
            ><?= e($form['mo_ta'] ?? '') ?></textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
    <label for="anh_bia" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
        Ảnh bìa sách
    </label>

    <input
        id="anh_bia"
        name="anh_bia"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        style="display: block; width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;"
    >

    <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.4rem;">
        Chọn ảnh JPG, PNG hoặc WebP. Có thể để trống nếu chưa có ảnh bìa.
    </p>

    <img
        id="anh_bia_preview"
        alt="Xem trước ảnh bìa"
        style="display: none; max-width: 180px; max-height: 250px; object-fit: contain; margin-top: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius);"
    >
</div>

<script>
document.getElementById('anh_bia').addEventListener('change', function () {
    const preview = document.getElementById('anh_bia_preview');
    const file = this.files[0];

    if (!file) {
        preview.removeAttribute('src');
        preview.style.display = 'none';
        return;
    }

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        alert('Vui lòng chọn ảnh JPG, PNG hoặc WebP.');
        this.value = '';
        preview.removeAttribute('src');
        preview.style.display = 'none';
        return;
    }

    preview.src = URL.createObjectURL(file);
    preview.style.display = 'block';
});
</script>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button
                type="submit"
                class="btn"
                <?= empty($categories) ? 'disabled' : '' ?>
            >
                Lưu sách
            </button>

            <a href="<?= e(url('books')) ?>" class="btn btn-outline">
                Quay lại danh sách
            </a>
        </div>
    </form>
</section>
