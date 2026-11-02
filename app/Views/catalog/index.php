<?php
$books = $books ?? [];
$categories = $categories ?? [];
$keyword = $keyword ?? '';
$publisher = $publisher ?? '';
$category = $category ?? '';
$status = $status ?? '';

$perPage = 9;
$totalBooks = count($books);
$totalPages = max(1, (int) ceil($totalBooks / $perPage));
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;
$paginatedBooks = array_slice($books, $offset, $perPage);
?>

<section style="max-width: 1100px; margin: 0 auto; padding: 1.5rem 0;">
    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.6rem; margin: 0 0 0.35rem;">Tra cứu danh mục sách</h1>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.95rem;">
            Tìm kiếm, xem ảnh bìa và tình trạng còn sách trong thư viện.
        </p>
    </div>

    <form method="get" action="<?= e(url('catalog')) ?>"
          style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.5rem; background: #fff; padding: 1rem; border-radius: var(--radius); border: 1px solid var(--border);">
        <input type="text" name="keyword" value="<?= e($keyword) ?>"
               placeholder="Nhập tên sách..."
               style="flex: 1; min-width: 180px; padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: var(--radius-sm);">

        <input type="text" name="publisher" value="<?= e($publisher) ?>"
               placeholder="Nhà xuất bản..."
               style="width: 160px; padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: var(--radius-sm);">

        <select name="category" style="padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= (string) $category === (string) $c['id'] ? 'selected' : '' ?>>
                    <?= e($c['ten']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status" style="padding: 0.65rem 0.85rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;">
            <option value="">Tất cả trạng thái</option>
            <option value="con_sach" <?= $status === 'con_sach' ? 'selected' : '' ?>>Còn sách</option>
            <option value="het_sach" <?= $status === 'het_sach' ? 'selected' : '' ?>>Hết sách</option>
        </select>

        <button type="submit" class="btn">Tìm kiếm</button>
        <a href="<?= e(url('catalog')) ?>" class="btn btn-outline">Đặt lại</a>
    </form>

    <div class="catalog-grid" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; align-items: stretch;">
        <?php if (empty($paginatedBooks)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 2.5rem; background: #fff; border: 1px solid var(--border); border-radius: var(--radius); color: var(--text-muted);">
                Không tìm thấy cuốn sách nào phù hợp.
            </div>
        <?php else: ?>
            <?php foreach ($paginatedBooks as $book): ?>
                <?php
                    $tong = (int) ($book['tong_ban_sao'] ?? 0);
                    $con = (int) ($book['so_con_trong'] ?? 0);
                    $conSach = $con > 0;
                ?>
                <div class="catalog-card" style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1rem; display: flex; flex-direction: column; height: 100%; min-height: 210px; box-sizing: border-box;">
                    <div style="display: flex; gap: 0.75rem; flex: 1 1 auto;">
                        <div style="width: 72px; height: 96px; flex-shrink: 0; border-radius: 6px; overflow: hidden; border: 1px solid var(--border); background: #f3f4f6;">
                            <?php if (!empty($book['anh_bia'])): ?>
                                <img src="<?= e(url(ltrim($book['anh_bia'], '/'))) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--text-muted); text-align: center; padding: 4px;">Không ảnh</div>
                            <?php endif; ?>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.4rem; align-items: flex-start;">
                                <span style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.3;"><?= e($book['ten_danh_muc'] ?? ('DM ' . ($book['danh_muc_id'] ?? ''))) ?></span>
                                <span style="font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 4px; white-space: nowrap; background: <?= $conSach ? '#dcfce7' : '#fee2e2' ?>; color: <?= $conSach ? '#166534' : '#991b1b' ?>;">
                                    <?= $conSach ? ('Còn ' . $con . '/' . $tong) : ($tong ? 'Hết sách' : 'Chưa có bản') ?>
                                </span>
                            </div>
                            <h3 style="margin: 0 0 0.4rem; font-size: 1rem; line-height: 1.35; min-height: 2.7em;">
                                <a href="<?= e(url('catalog/show/' . (int) $book['id'])) ?>" style="color: inherit; text-decoration: none;"><?= e($book['ten_sach'] ?? '') ?></a>
                            </h3>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">NXB: <?= e($book['nha_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
                        </div>
                    </div>
                    <a href="<?= e(url('catalog/show/' . (int) $book['id'])) ?>" class="btn" style="display: block; width: 100%; text-align: center; margin-top: auto; padding-top: 0.65rem; padding-bottom: 0.65rem; box-sizing: border-box;">Chi tiết &amp; Bản sao</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <style>
        @media (max-width: 900px) {
            .catalog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        }
        @media (max-width: 560px) {
            .catalog-grid { grid-template-columns: 1fr !important; }
        }
    </style>

    <?php if ($totalPages > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 1.75rem; flex-wrap: wrap;">
            <?php
            $queryParams = $_GET;
            for ($i = 1; $i <= $totalPages; $i++):
                $queryParams['page'] = $i;
                $qs = http_build_query($queryParams);
                $active = $i === $page;
            ?>
                <a href="?<?= e($qs) ?>" class="btn <?= $active ? '' : 'btn-outline' ?>" style="min-width: 2.5rem; text-align: center;"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>