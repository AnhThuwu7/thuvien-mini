<?php
$book = $book ?? [];
$copies = $copies ?? [];
$ten_danh_muc = $ten_danh_muc ?? '';
$ten_tac_gia = $ten_tac_gia ?? '';
$so_con_trong = (int) ($so_con_trong ?? 0);
$tong_ban_sao = (int) ($tong_ban_sao ?? count($copies));
$isGuest  = !empty($isGuest);
$isReader = !empty($isReader);
$isStaff  = !empty($isStaff);

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
$canRequest = $so_con_trong > 0 && !$isStaff;
?>

<section style="max-width:960px;margin:0 auto;padding:1.5rem 0;">
    <div style="margin-bottom:1rem;">
        <a href="<?= e(url($isGuest ? '' : 'catalog')) ?>" class="btn btn-outline">← Quay lại tra cứu</a>
    </div>

    <div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:1.25rem;">
        <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;">
            <div style="width:220px;max-width:100%;">
                <?php if (!empty($book['anh_bia'])): ?>
                    <img src="<?= e(url(ltrim($book['anh_bia'], '/'))) ?>" alt="<?= e($book['ten_sach'] ?? '') ?>"
                         style="display:block;width:100%;max-height:340px;object-fit:contain;border:1px solid var(--border);border-radius:var(--radius);">
                <?php else: ?>
                    <div style="padding:3rem 1rem;text-align:center;border:1px dashed var(--border);border-radius:var(--radius);color:var(--text-muted);">Chưa có ảnh bìa</div>
                <?php endif; ?>
            </div>
            <div style="flex:1;min-width:250px;">
                <h1 style="margin-top:0;font-size:1.5rem;"><?= e($book['ten_sach'] ?? '') ?></h1>
                <p style="margin:0.4rem 0;"><strong>Tác giả:</strong> <?= e($ten_tac_gia !== '' ? $ten_tac_gia : 'Đang cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Danh mục:</strong> <?= e($ten_danh_muc !== '' ? $ten_danh_muc : 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>ISBN:</strong> <?= e($book['isbn'] ?: 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Nhà xuất bản:</strong> <?= e($book['nha_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Năm xuất bản:</strong> <?= e($book['nam_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Tình trạng:</strong>
                    <?php if ($isGuest): ?>
                        <?= $so_con_trong > 0 ? 'Có thể mượn' : 'Hết bản trống' ?>
                    <?php else: ?>
                        <?= $so_con_trong > 0
                            ? ('Còn ' . $so_con_trong . '/' . $tong_ban_sao . ' bản trống')
                            : ($tong_ban_sao > 0 ? 'Hết sách (không còn bản trống)' : 'Chưa có bản sao') ?>
                    <?php endif; ?>
                </p>
                <p style="margin:1rem 0 0.35rem;"><strong>Mô tả:</strong></p>
                <div style="white-space:pre-line;line-height:1.7;color:var(--text-muted);">
                    <?= e($book['mo_ta'] ?: 'Chưa có mô tả cho sách này.') ?>
                </div>

                <div style="margin-top:1.25rem;display:flex;flex-wrap:wrap;gap:0.6rem;align-items:center;">
                    <?php if ($isStaff): ?>
                        <span style="color:var(--text-muted);font-size:0.95rem;">Vai trò thủ thư/admin: lập phiếu tại <a href="<?= e(url('loans')) ?>">Mượn–Trả</a>.</span>
                    <?php elseif ($isReader && $canRequest): ?>
                        <form method="post" action="<?= e(url('loan-requests/store')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="sach_id" value="<?= (int) ($book['id'] ?? 0) ?>">
                            <button type="submit" class="btn">Gửi yêu cầu mượn / giữ chỗ</button>
                        </form>
                    <?php elseif ($isReader && !$canRequest): ?>
                        <button type="button" class="btn btn-outline" disabled>Hết bản trống</button>
                    <?php elseif ($isGuest && $canRequest): ?>
                        <a class="btn" href="<?= e(url('catalog/request/' . (int)($book['id'] ?? 0))) ?>">Đăng nhập để đặt mượn</a>
                        <a class="btn btn-outline" href="<?= e(url('auth/register')) ?>">Đăng ký độc giả</a>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline" disabled>Hết bản trống</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$isGuest): ?>
    <div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
        <h2 style="margin:0 0 0.75rem;font-size:1.15rem;">
            Danh sách bản sao
            <span style="font-weight:500;color:var(--text-muted);font-size:0.95rem;">(<?= $so_con_trong ?>/<?= $tong_ban_sao ?> bản trống)</span>
        </h2>
        <?php if (empty($copies)): ?>
            <p style="color:var(--text-muted);margin:0;">Chưa có bản sao nào cho đầu sách này.</p>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                    <thead>
                        <tr style="background:var(--primary-light,#f5f0eb);text-align:left;">
                            <th style="padding:0.75rem;">Mã bản sao</th>
                            <th style="padding:0.75rem;">Trạng thái</th>
                            <th style="padding:0.75rem;">Vị trí kệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($copies as $c): ?>
                            <?php
                                $st = $c['trang_thai'] ?? '';
                                $label = $statusLabel[$st] ?? $st;
                                $style = $statusStyle[$st] ?? 'background:#e5e7eb;color:#374151;';
                            ?>
                            <tr style="border-top:1px solid var(--border);">
                                <td style="padding:0.75rem;font-weight:600;"><?= e($c['ma_ban_sao'] ?? '') ?></td>
                                <td style="padding:0.75rem;">
                                    <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:0.8rem;font-weight:600;<?= $style ?>"><?= e($label) ?></span>
                                </td>
                                <td style="padding:0.75rem;color:var(--text-muted);"><?= e($c['vi_tri_ke'] ?: '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>