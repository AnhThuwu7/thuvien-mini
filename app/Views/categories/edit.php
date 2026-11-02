
<?php
$category = $category ?? [];
$errors = $errors ?? [];
?>

<section style="max-width: 800px; margin: 0 auto; padding: 1.5rem 0;">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.8rem; margin-bottom: 0.35rem;">
            Sửa danh mục
        </h1>
        <p style="color: var(--text-muted);">
            Cập nhật tên và mô tả danh mục sách.
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

    <form
        method="POST"
        action="<?= e(url('categories/update/' . (int) $category['id'])) ?>"
        style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem;"
    >
        <?= csrf_field() ?>

        <div style="margin-bottom: 1.25rem;">
            <label for="ten" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                Tên danh mục <span style="color: #dc2626;">*</span>
            </label>

            <input
                id="ten"
                name="ten"
                type="text"
                maxlength="100"
                required
                value="<?= e($category['ten'] ?? '') ?>"
                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
            >
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label for="mo_ta" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                Mô tả
            </label>

            <textarea
                id="mo_ta"
                name="mo_ta"
                maxlength="255"
                rows="4"
                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); resize: vertical;"
            ><?= e($category['mo_ta'] ?? '') ?></textarea>
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button type="submit" class="btn">
                Lưu thay đổi
            </button>

            <a href="<?= e(url('categories')) ?>" class="btn btn-outline">
                Quay lại
            </a>
        </div>
    </form>
</section>
