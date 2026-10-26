
<?php
$categories = $categories ?? [];
?>

<section style="max-width: 1100px; margin: 0 auto; padding: 1.5rem 0;">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.8rem; margin-bottom: 0.35rem;">
            Quản lý danh mục sách
        </h1>
        <p style="color: var(--text-muted);">
            Thêm, sửa và quản lý các danh mục của thư viện.
        </p>
    </div>
    <form
    method="GET"
    action="<?= e(url('categories')) ?>"
    style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1rem; margin-bottom: 1.5rem;"
>
    <input
        type="search"
        name="q"
        value="<?= e($keyword ?? '') ?>"
        placeholder="Tìm theo tên hoặc mô tả danh mục..."
        style="flex: 1; min-width: 240px; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
    >

    <button type="submit" class="btn">
        Tìm kiếm
    </button>

    <a href="<?= e(url('categories')) ?>" class="btn btn-outline">
        Làm mới
    </a>
</form>
    <!-- Form thêm danh mục -->
    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.2rem; margin: 0 0 1rem;">
            Thêm danh mục mới
        </h2>

        <form method="POST" action="<?= e(url('categories/store')) ?>">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1rem;">
                <label for="ten" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Tên danh mục <span style="color: #dc2626;">*</span>
                </label>
                <input
                    id="ten"
                    name="ten"
                    type="text"
                    maxlength="100"
                    required
                    placeholder="Ví dụ: Văn học Việt Nam"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
                >
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="mo_ta" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Mô tả
                </label>
                <textarea
                    id="mo_ta"
                    name="mo_ta"
                    maxlength="255"
                    rows="3"
                    placeholder="Mô tả ngắn về danh mục"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); resize: vertical;"
                ></textarea>
            </div>

            <button type="submit" class="btn">
                + Thêm danh mục
            </button>
        </form>
    </div>

    <!-- Danh sách danh mục -->
    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); overflow-x: auto;">
        <table style="width: 100%; min-width: 750px; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--primary-light); text-align: left;">
                    <th style="padding: 0.85rem;">ID</th>
                    <th style="padding: 0.85rem;">Tên danh mục</th>
                    <th style="padding: 0.85rem;">Mô tả</th>
                    <th style="padding: 0.85rem; text-align: center;">Số sách</th>
                    <th style="padding: 0.85rem; text-align: center;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            Chưa có danh mục nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <tr style="border-top: 1px solid var(--border);">
                            <td style="padding: 0.85rem;">
                                <?= (int) $category['id'] ?>
                            </td>

                            <td style="padding: 0.85rem; font-weight: 600;">
                                <?= e($category['ten']) ?>
                            </td>

                            <td style="padding: 0.85rem;">
                                <?= e($category['mo_ta'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem; text-align: center;">
                                <?= (int) $category['so_sach'] ?>
                            </td>

                            <td style="padding: 0.85rem; text-align: center; white-space: nowrap;">
                                <a
                                    href="<?= e(url('categories/edit/' . (int) $category['id'])) ?>"
                                    class="btn"
                                >
                                    Sửa
                                </a>

                                <form
                                    method="POST"
                                    action="<?= e(url('categories/delete/' . (int) $category['id'])) ?>"
                                    style="display: inline;"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này không?');"
                                >
                                    <?= csrf_field() ?>

                                    <button
                                        type="submit"
                                        class="btn"
                                        style="background: #dc2626; color: #fff; border: none; margin-left: 0.25rem;"
                                    >
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p style="margin-top: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">
        Tổng số danh mục: <?= count($categories) ?>
    </p>

    <div style="margin-top: 1rem;">
        <a href="<?= e(url('books')) ?>" class="btn btn-outline">
            Quay lại quản lý sách
        </a>
    </div>
</section>
