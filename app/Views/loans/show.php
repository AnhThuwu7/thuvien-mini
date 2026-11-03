<?php
$loan = $loan ?? [];
$st = $loan['trang_thai_hien_thi'] ?? '';
$labels = [
    'dang_muon' => 'Đang mượn',
    'qua_han'   => 'Quá hạn',
    'da_tra'    => 'Đã trả',
];
$styles = [
    'dang_muon' => 'background:#dbeafe;color:#1e40af;',
    'qua_han'   => 'background:#fee2e2;color:#991b1b;',
    'da_tra'    => 'background:#dcfce7;color:#166534;',
];
?>
<section style="max-width:900px;margin:0 auto;padding:1rem 0 2rem;">
    <div style="margin-bottom:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
        <a href="<?= e(url()) ?>" class="btn btn-outline">← Trang chủ</a>
        <a href="<?= e(url('loans/mine')) ?>" class="btn btn-outline">Lịch sử mượn</a>
    </div>

    <div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
        <div style="display:flex;gap:2rem;flex-wrap:wrap;align-items:flex-start;">
            <div style="width:200px;max-width:100%;">
                <?php if (!empty($loan['anh_bia'])): ?>
                    <img src="<?= e(url(ltrim($loan['anh_bia'], '/'))) ?>" alt=""
                         style="display:block;width:100%;max-height:300px;object-fit:contain;border:1px solid var(--border);border-radius:var(--radius);">
                <?php else: ?>
                    <div style="padding:3rem 1rem;text-align:center;border:1px dashed var(--border);border-radius:var(--radius);color:var(--text-muted);">Chưa có ảnh</div>
                <?php endif; ?>
            </div>
            <div style="flex:1;min-width:240px;">
                <h1 style="margin:0 0 0.5rem;font-size:1.4rem;"><?= e($loan['ten_sach'] ?? '') ?></h1>
                <p style="margin:0.35rem 0;">
                    <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:0.8rem;font-weight:600;<?= $styles[$st] ?? '' ?>">
                        <?= e($labels[$st] ?? $st) ?>
                    </span>
                </p>
                <p style="margin:0.4rem 0;"><strong>Mã phiếu:</strong> #<?= (int) ($loan['phieu_muon_id'] ?? 0) ?></p>
                <p style="margin:0.4rem 0;"><strong>Mã bản sao:</strong> <?= e($loan['ma_ban_sao'] ?? '—') ?></p>
                <?php if (!empty($loan['vi_tri_ke'])): ?>
                    <p style="margin:0.4rem 0;"><strong>Kệ:</strong> <?= e($loan['vi_tri_ke']) ?></p>
                <?php endif; ?>
                <p style="margin:0.4rem 0;"><strong>ISBN:</strong> <?= e($loan['isbn'] ?: '—') ?></p>
                <p style="margin:0.4rem 0;"><strong>NXB:</strong> <?= e($loan['nha_xuat_ban'] ?: '—') ?></p>
                <hr style="border:none;border-top:1px solid var(--border);margin:1rem 0;">
                <p style="margin:0.4rem 0;"><strong>Ngày mượn:</strong>
                    <?= e(function_exists('format_datetime') ? format_datetime($loan['muon_luc'] ?? null) : ($loan['muon_luc'] ?? '—')) ?>
                </p>
                <p style="margin:0.4rem 0;"><strong>Hạn trả:</strong>
                    <?= e(function_exists('format_date') ? format_date($loan['han_tra'] ?? null) : substr((string)($loan['han_tra'] ?? ''), 0, 10)) ?>
                </p>
                <p style="margin:0.4rem 0;"><strong>Ngày trả:</strong>
                    <?= !empty($loan['tra_luc'])
                        ? e(function_exists('format_datetime') ? format_datetime($loan['tra_luc']) : $loan['tra_luc'])
                        : 'Chưa trả' ?>
                </p>
                <?php if (!empty($loan['tinh_trang_tra'])): ?>
                    <p style="margin:0.4rem 0;"><strong>Tình trạng khi trả:</strong> <?= e($loan['tinh_trang_tra']) ?></p>
                <?php endif; ?>
                <?php if (isset($loan['tien_phat']) && (float)$loan['tien_phat'] > 0): ?>
                    <p style="margin:0.4rem 0;"><strong>Phí phạt:</strong>
                        <?= e(function_exists('format_money') ? format_money($loan['tien_phat']) : ($loan['tien_phat'] . ' đ')) ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($loan['sach_id'])): ?>
                    <p style="margin-top:1.25rem;">
                        <a href="<?= e(url('catalog/show/' . (int) $loan['sach_id'])) ?>" class="btn btn-outline">Xem trang sách</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>