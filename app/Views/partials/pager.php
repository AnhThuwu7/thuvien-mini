<?php
/**
 * Thanh phân trang dùng chung.
 * Biến cần có trước khi require:
 *   @var array  $pagination  kết quả của paginate()
 *   @var string $pagerPath   đường dẫn route, ví dụ 'books'
 *   @var array  $pagerQuery  tham số lọc cần giữ lại (không gồm page), ví dụ ['q' => 'php']
 */
$pagerQuery = array_filter($pagerQuery ?? [], static fn($v) => $v !== '' && $v !== null);
$current = (int) ($pagination['page'] ?? 1);
$last = (int) ($pagination['total_pages'] ?? 1);

if ($last > 1):
    // Hiện trang đầu, trang cuối và 2 trang quanh trang hiện tại; phần còn lại thay bằng "…"
    $pages = [];
    for ($i = 1; $i <= $last; $i++) {
        if ($i === 1 || $i === $last || abs($i - $current) <= 2) {
            $pages[] = $i;
        }
    }
    $prev = 0;
?>
    <nav aria-label="Phân trang" style="margin-top: 1.25rem; display: flex; justify-content: center; gap: 0.35rem; flex-wrap: wrap;">
        <?php foreach ($pages as $i): ?>
            <?php if ($prev && $i - $prev > 1): ?>
                <span style="align-self: center; color: var(--text-muted);">…</span>
            <?php endif; ?>
            <a href="<?= e(url($pagerPath . '?' . http_build_query($pagerQuery + ['page' => $i]))) ?>"
               class="btn btn-small <?= $i === $current ? '' : 'btn-outline' ?>"
               <?= $i === $current ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            <?php $prev = $i; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>