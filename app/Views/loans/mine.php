<?php
$loans = $loans ?? [];
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
<section style="max-width:1000px;margin:0 auto;padding:1rem 0 2rem;">
    <div style="margin-bottom:1.25rem;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="margin:0 0 0.35rem;">Lịch sử mượn sách</h1>
            <p style="margin:0;color:var(--text-muted);">Ấn vào một dòng để xem ảnh bìa và chi tiết phiếu.</p>
        </div>
        <a href="<?= e(url()) ?>" class="btn btn-outline">Về trang chủ</a>
    </div>

    <?php if (empty($loans)): ?>
        <p style="color:var(--text-muted);">Bạn chưa có phiếu mượn nào.</p>
    <?php else: ?>
        <div class="card" style="overflow-x:auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
            <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                <thead>
                    <tr style="background:var(--primary-light,#f5f0eb);text-align:left;">
                        <th style="padding:0.85rem;">Sách</th>
                        <th style="padding:0.85rem;">Mã bản sao</th>
                        <th style="padding:0.85rem;">Mượn</th>
                        <th style="padding:0.85rem;">Hạn trả</th>
                        <th style="padding:0.85rem;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $r): ?>
                        <?php $st = $r['trang_thai_hien_thi'] ?? ''; ?>
                        <tr style="border-top:1px solid var(--border);cursor:pointer;"
                            onclick="location.href='<?= e(url('loans/show/' . (int) $r['phieu_muon_id'])) ?>'">
                            <td style="padding:0.85rem;">
                                <div style="display:flex;gap:0.75rem;align-items:center;">
                                    <?php if (!empty($r['anh_bia'])): ?>
                                        <img src="<?= e(url(ltrim($r['anh_bia'], '/'))) ?>" alt=""
                                             style="width:40px;height:56px;object-fit:cover;border-radius:4px;border:1px solid var(--border);">
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= e($r['ten_sach'] ?? '') ?></strong>
                                        <div style="font-size:0.8rem;color:var(--text-muted);">#<?= (int) $r['phieu_muon_id'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:0.85rem;"><?= e($r['ma_ban_sao'] ?? '') ?></td>
                            <td style="padding:0.85rem;white-space:nowrap;">
                                <?= e(function_exists('format_date') ? format_date($r['muon_luc'] ?? null) : substr((string)($r['muon_luc'] ?? ''), 0, 10)) ?>
                            </td>
                            <td style="padding:0.85rem;white-space:nowrap;">
                                <?= e(function_exists('format_date') ? format_date($r['han_tra'] ?? null) : substr((string)($r['han_tra'] ?? ''), 0, 10)) ?>
                            </td>
                            <td style="padding:0.85rem;">
                                <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:0.8rem;font-weight:600;<?= $styles[$st] ?? '' ?>">
                                    <?= e($labels[$st] ?? $st) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>