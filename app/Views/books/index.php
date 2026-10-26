
<section style="max-width: 1200px; margin: 0 auto; padding: 1.5rem 0;">
    <!-- Tiêu đề và nút thêm sách -->
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.8rem; margin-bottom: 0.35rem;">
                Quản lý sách
            </h1>

            <p style="color: var(--text-muted); margin-bottom: 1rem;">
                Quản lý thông tin đầu sách trong thư viện.
            </p>
        </div>

        <a href="<?= e(url('books/create')) ?>" class="btn">
            + Thêm sách
        </a>
    </div>

    <!-- Form tìm kiếm -->
    <div style="background: #fff; padding: 1rem; border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 1.25rem;">
        <form method="get" action="<?= e(url('books')) ?>"
              style="display: flex; gap: 0.75rem; flex-wrap: wrap;">

            <input
                type="search"
                name="q"
                value="<?= e($keyword) ?>"
                placeholder="Tìm theo tên sách, ISBN, nhà xuất bản hoặc danh mục..."
                style="flex: 1; min-width: 240px; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
            >

            <button type="submit" class="btn">
                Tìm kiếm
            </button>

            <a href="<?= e(url('books')) ?>" class="btn btn-outline">
                Làm mới
            </a>
        </form>
    </div>

    <!-- Bảng danh sách sách -->
    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 950px;">
            <thead>
                <tr style="background: var(--primary-light); text-align: left;">
                    <th style="padding: 0.85rem;">ID</th>
                    <th style="padding: 0.85rem;">Tên sách</th>
                    <th style="padding: 0.85rem;">ISBN</th>
                    <th style="padding: 0.85rem;">Danh mục</th>
                    <th style="padding: 0.85rem;">Nhà xuất bản</th>
                    <th style="padding: 0.85rem;">Năm XB</th>
                    <th style="padding: 0.85rem; text-align: center;">Số bản sao</th>
                    <th style="padding: 0.85rem; text-align: center;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="8" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            <?php if ($keyword !== ''): ?>
                                Không tìm thấy sách phù hợp với từ khóa
                                "<?= e($keyword) ?>".
                            <?php else: ?>
                                Chưa có dữ liệu sách trong thư viện.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <tr style="border-top: 1px solid var(--border);">
                            <td style="padding: 0.85rem;">
                                <?= (int) $book['id'] ?>
                            </td>

                           
<td style="padding: 0.85rem; font-weight: 600;">
    <a
        href="<?= e(url('books/show/' . (int) $book['id'])) ?>"
        style="color: var(--primary); text-decoration: none;"
        title="Xem chi tiết sách"
    >
        <?= e($book['ten_sach']) ?>
    </a>
</td>


                            <td style="padding: 0.85rem;">
                                <?= e($book['isbn'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem;">
                                <?= e($book['ten_danh_muc'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem;">
                                <?= e($book['nha_xuat_ban'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem;">
                                <?= e($book['nam_xuat_ban'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem; text-align: center;">
                                <?= (int) ($book['tong_ban_sao'] ?? 0) ?>
                            </td>

                            <!-- Nút sửa thông tin sách -->
                            <td style="padding: 0.85rem; text-align: center; white-space: nowrap;">
    <a
        href="<?= e(url('books/edit/' . (int) $book['id'])) ?>"
        class="btn"
    >
        Sửa
    </a>

    <form
        method="POST"
        action="<?= e(url('books/delete/' . (int) $book['id'])) ?>"
        style="display: inline;"
        onsubmit="return confirm('Bạn có chắc chắn muốn xóa sách này không?');"
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

    <!-- Tổng số sách -->
    <p style="margin-top: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">
        Tổng số sách đang hiển thị: <?= count($books) ?>
    </p>
</section>
```