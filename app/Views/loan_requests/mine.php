<?php
$requests = $requests ?? [];
$labels = [
    'cho_duyet' => 'Chờ duyệt',
    'da_duyet'  => 'Đã duyệt — hãy tới lấy sách',
    'tu_choi'   => 'Từ chối',
    'da_huy'    => 'Đã hủy',
    'het_han'   => 'Hết hạn giữ chỗ',
    'da_nhan'   => 'Đã lập phiếu mượn',
];
$styles = [
    'cho_duyet' => 'background:#fef3c7;color:#92400e;',
    'da_duyet'  => 'background:#dbeafe;color:#1e40af;',
    'tu_choi'   => 'background:#fee2e2;color:#991b1b;',
    'da_huy'    => 'background:#e5e7eb;color:#374151;',
    'het_han'   => 'background:#e5e7eb;color:#374151;',
    'da_nhan'   => 'background:#dcfce7;color:#166534;',
];
?>

<section style="max-width:1000px;margin:0 auto;">
    <div style="margin-bottom:1.25rem;">
        <h1 style="margin:0 0 0.35rem;">Yêu cầu mượn của tôi</h1>
        <p style="margin:0;color:var(--text-muted);">
            Sau khi được duyệt, bạn cần tới thư viện trước hạn để thủ thư lập phiếu mượn.
            Quá hạn giữ chỗ sẽ bị hủy và sách trả lại kho.
        </p>
    </div>

    <?php if (!$requests): ?>
        <p style="color:var(--text-muted);">Bạn chưa gửi yêu cầu nào.</p>
    <?php else: ?>
        <div class="card" style="overflow-x:auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
            <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                <thead>
                    <tr style="background:var(--primary-light,#f5f0eb);text-align:left;">
                        <th style="padding:0.85rem;">Mã</th>
                        <th style="padding:0.85rem;">Tên sách</th>
                        <th style="padding:0.85rem;">Ngày gửi</th>
                        <th style="padding:0.85rem;">Trạng thái</th>
                        <th style="padding:0.85rem;">Hạn đến lấy / ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $r): ?>
                        <?php
                            $st = $r['trang_thai'] ?? '';
                            $label = $labels[$st] ?? $st;
                            $style = $styles[$st] ?? '';
                        ?>
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:0.85rem;">#<?= (int) $r['id'] ?></td>
                            <td style="padding:0.85rem;">
                                <a href="<?= e(url('catalog/show/' . (int) $r['sach_id'])) ?>" style="color:inherit;font-weight:600;">
                                    <?= e($r['ten_sach'] ?? '') ?>
                                </a>
                                <?php if (!empty($r['ma_ban_sao'])): ?>
                                    <div style="font-size:0.85rem;color:var(--text-muted);">Bản sao: <?= e($r['ma_ban_sao']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:0.85rem;white-space:nowrap;">
                                <?= e(format_datetime($r['yeu_cau_luc'] ?? null) ?: ($r['yeu_cau_luc'] ?? '')) ?>
                            </td>
                            <td style="padding:0.85rem;">
                                <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:0.8rem;font-weight:600;<?= $style ?>">
                                    <?= e($label) ?>
                                </span>
                            </td>
                            <td style="padding:0.85rem;color:var(--text-muted);font-size:0.9rem;">
                                <?php if ($st === 'da_duyet' && !empty($r['han_den_lay'])): ?>
                                    <strong>Hạn đến lấy:</strong>
                                    <?= e(format_datetime($r['han_den_lay']) ?: $r['han_den_lay']) ?>
                                <?php elseif ($st === 'tu_choi' && !empty($r['ly_do_tu_choi'])): ?>
                                    <strong>Lý do:</strong> <?= e($r['ly_do_tu_choi']) ?>
                                <?php elseif ($st === 'het_han'): ?>
                                    Đã quá hạn giữ chỗ — sách trả lại kho.
                                <?php elseif ($st === 'da_nhan'): ?>
                                    Đã chuyển thành phiếu mượn.
                                <?php elseif (!empty($r['ghi_chu'])): ?>
                                    <?= e($r['ghi_chu']) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>