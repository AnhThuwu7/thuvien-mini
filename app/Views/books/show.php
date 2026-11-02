<?php
/**
 * Chi tiết sách — THỦ THƯ / ADMIN
 */
$book = $book ?? [];
$copies = $copies ?? [];
$so_con_trong = (int) ($so_con_trong ?? 0);
$tong_ban_sao = (int) ($tong_ban_sao ?? count($copies));

$statusLabel = [
    'con_trong'    => 'Còn trống',
    'dang_giu_cho' => 'Đang giữ chỗ',
    'dang_muon'    => 'Đang mượn',
    'hong'         => 'Hỏng',
    'mat'          => 'Mất',
    'thanh_ly'     => 'Thanh lý',
];
$statusStyle = [
    'con_trong'    => 'background:#dcfce7;color:#166534;',
    'dang_giu_cho' => 'background:#fef3c7;color:#92400e;',
    'dang_muon'    => 'background:#fee2e2;color:#991b1b;',
    'hong'         => 'background:#fce7f3;color:#9d174d;',
    'mat'          => 'background:#e5e7eb;color:#374151;',
    'thanh_ly'     => 'background:#e5e7eb;color:#374151;',
];
?>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
    <div>
        <h1 style="margin:0 0 0.35rem;">Chi tiết sách</h1>
        <p style="margin:0;color:var(--text-muted);">Thông tin đầu sách và tình trạng từng bản sao (kèm người đang mượn).</p>
    </div>
    <a href="<?= e(url('books')) ?>" class="btn btn-outline">Quay lại danh sách</a>
</div>

<div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:1.25rem;">
    <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;">
        <div style="width:220px;max-width:100%;">
            <?php if (!empty($book['anh_bia'])): ?>
                <img
                    src="<?= e(url(ltrim($book['anh_bia'], '/'))) ?>"
                    alt="<?= e($book['ten_sach'] ?? '') ?>"
                    style="display:block;width:100%;max-height:340px;object-fit:contain;border:1px solid var(--border);border-radius:var(--radius);"
                >
            <?php else: ?>
                <div style="padding:3rem 1rem;text-align:center;border:1px dashed var(--border);border-radius:var(--radius);color:var(--text-muted);">
                    Chưa có ảnh bìa
                </div>
            <?php endif; ?>
        </div>

        <div style="flex:1;min-width:250px;">
            <h2 style="margin-top:0;"><?= e($book['ten_sach'] ?? '') ?></h2>
            <p><strong>ISBN:</strong> <?= e($book['isbn'] ?: 'Chưa cập nhật') ?></p>
            <p><strong>Nhà xuất bản:</strong> <?= e($book['nha_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
            <p><strong>Năm xuất bản:</strong> <?= e($book['nam_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
            <p><strong>Ngôn ngữ:</strong> <?= e($book['ngon_ngu'] ?? 'Chưa cập nhật') ?></p>
            <p><strong>Kho:</strong>
                <?= $so_con_trong > 0
                    ? ('Còn ' . $so_con_trong . '/' . $tong_ban_sao . ' bản trống')
                    : ($tong_ban_sao > 0 ? 'Hết bản trống' : 'Chưa có bản sao') ?>
            </p>
            <p><strong>Mô tả:</strong></p>
            <div style="white-space:pre-line;line-height:1.7;color:var(--text-muted);">
                <?= e($book['mo_ta'] ?: 'Chưa có mô tả cho sách này.') ?>
            </div>
            <div style="margin-top:1.5rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                <a href="<?= e(url('books/edit/' . (int) ($book['id'] ?? 0))) ?>" class="btn">Sửa thông tin sách</a>
                <a href="<?= e(url('copies')) ?>" class="btn btn-outline">Quản lý bản sao</a>
                <a href="<?= e(url('books')) ?>" class="btn btn-outline">Quay lại</a>
            </div>
        </div>
    </div>
</div>

<div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
    <h2 style="margin:0 0 1rem;font-size:1.15rem;">
        Bản sao trong kho
        <span style="font-weight:500;color:var(--text-muted);font-size:0.95rem;">
            (<?= $so_con_trong ?>/<?= $tong_ban_sao ?> trống)
        </span>
    </h2>

    <?php if (empty($copies)): ?>
        <p style="color:var(--text-muted);margin:0;">Chưa có bản sao. Hãy thêm bản sao để cho mượn.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                <thead>
                    <tr style="background:var(--primary-light,#f5f0eb);text-align:left;">
                        <th style="padding:0.75rem;">Mã bản sao</th>
                        <th style="padding:0.75rem;">Trạng thái</th>
                        <th style="padding:0.75rem;">Kệ</th>
                        <th style="padding:0.75rem;">Đang mượn bởi</th>
                        <th style="padding:0.75rem;">Mã phiếu</th>
                        <th style="padding:0.75rem;">Hạn trả</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($copies as $c): ?>
                        <?php
                            $st = $c['trang_thai'] ?? '';
                            $label = $statusLabel[$st] ?? $st;
                            $style = $statusStyle[$st] ?? 'background:#e5e7eb;color:#374151;';
                            $dangMuon = $st === 'dang_muon' && !empty($c['phieu_muon_id']);
                        ?>
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:0.75rem;font-weight:600;"><?= e($c['ma_ban_sao'] ?? '') ?></td>
                            <td style="padding:0.75rem;">
                                <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:0.8rem;font-weight:600;<?= $style ?>">
                                    <?= e($label) ?>
                                </span>
                            </td>
                            <td style="padding:0.75rem;color:var(--text-muted);"><?= e($c['vi_tri_ke'] ?: '—') ?></td>
                            <td style="padding:0.75rem;">
                                <?php if ($dangMuon): ?>
                                    <div style="font-weight:600;"><?= e($c['ten_doc_gia'] ?? '—') ?></div>
                                    <?php if (!empty($c['ma_doc_gia'])): ?>
                                        <div style="font-size:0.8rem;color:var(--text-muted);">Mã ĐG: <?= e($c['ma_doc_gia']) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem;">
                                <?php if ($dangMuon): ?>
                                    #<?= (int) $c['phieu_muon_id'] ?>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem;">
                                <?php if ($dangMuon && !empty($c['han_tra'])): ?>
                                    <?= e(function_exists('format_date') ? format_date($c['han_tra']) : substr((string) $c['han_tra'], 0, 10)) ?>
                                <?php else: ?>
                                    <span style="color:var(--text-muted);">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.75rem;white-space:nowrap;">
                                <a href="<?= e(url('copies/edit/' . (int) $c['id'])) ?>" class="btn btn-outline" style="padding:0.35rem 0.65rem;font-size:0.85rem;">Sửa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>