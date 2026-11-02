<?php
$pending = $pending ?? [];
$approved = $approved ?? [];
$closed = $closed ?? [];
$hold_hours = (int) ($hold_hours ?? 12);
?>

<section style="max-width:1100px;margin:0 auto;padding:1rem 0;">
    <div style="margin-bottom:1.25rem;">
        <h1 style="margin:0 0 0.35rem;">Yêu cầu mượn / giữ chỗ</h1>
        <p style="margin:0;color:var(--text-muted);font-size:0.95rem;">
            Duyệt → giữ bản sao tối đa <strong><?= (int) $hold_hours ?> giờ</strong>.
            Độc giả tới thư viện → bấm <strong>Lập phiếu mượn</strong> (không chọn bản sao trên form mượn thường).
        </p>
    </div>

    <h2 style="font-size:1.15rem;margin:1.5rem 0 0.75rem;">1. Chờ duyệt (<?= count($pending) ?>)</h2>
    <?php if (!$pending): ?>
        <p style="color:var(--text-muted);">Không có yêu cầu chờ duyệt.</p>
    <?php else: ?>
        <div class="card" style="overflow-x:auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
            <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                <thead>
                    <tr style="background:var(--primary-light,#f5f0eb);text-align:left;">
                        <th style="padding:0.75rem;">#</th>
                        <th style="padding:0.75rem;">Độc giả</th>
                        <th style="padding:0.75rem;">Sách</th>
                        <th style="padding:0.75rem;">Gửi lúc</th>
                        <th style="padding:0.75rem;">Ghi chú</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending as $r): ?>
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:0.75rem;">#<?= (int) $r['id'] ?></td>
                            <td style="padding:0.75rem;">
                                <strong><?= e($r['ho_ten'] ?? '') ?></strong>
                                <div style="font-size:0.8rem;color:var(--text-muted);"><?= e($r['ma_doc_gia'] ?? '') ?></div>
                            </td>
                            <td style="padding:0.75rem;"><?= e($r['ten_sach'] ?? '') ?></td>
                            <td style="padding:0.75rem;white-space:nowrap;"><?= e(format_datetime($r['yeu_cau_luc'] ?? null) ?: ($r['yeu_cau_luc'] ?? '')) ?></td>
                            <td style="padding:0.75rem;color:var(--text-muted);"><?= e($r['ghi_chu'] ?? '—') ?></td>
                            <td style="padding:0.75rem;white-space:nowrap;">
                                <form method="post" action="<?= e(url('loan-requests/approve')) ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                    <button type="submit" class="btn" style="padding:0.35rem 0.7rem;font-size:0.85rem;">Duyệt</button>
                                </form>
                                <form method="post" action="<?= e(url('loan-requests/reject')) ?>" style="display:inline;margin-left:0.35rem;"
                                      onsubmit="var r=prompt('Lý do từ chối:'); if(r===null) return false; this.querySelector('[name=ly_do_tu_choi]').value=r; return true;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                    <input type="hidden" name="ly_do_tu_choi" value="">
                                    <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.7rem;font-size:0.85rem;">Từ chối</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h2 style="font-size:1.15rem;margin:1.75rem 0 0.75rem;">2. Đã duyệt — chờ lập phiếu (<?= count($approved) ?>)</h2>
    <p style="color:var(--text-muted);font-size:0.9rem;margin-top:0;">
        Bản sao đang <strong>giữ chỗ</strong>, không hiện trong form “Mượn–Trả” thường. Khi độc giả tới, bấm <strong>Lập phiếu mượn</strong>.
    </p>
    <?php if (!$approved): ?>
        <p style="color:var(--text-muted);">Không có giữ chỗ còn hạn.</p>
    <?php else: ?>
        <div class="card" style="overflow-x:auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
            <table style="width:100%;border-collapse:collapse;font-size:0.95rem;">
                <thead>
                    <tr style="background:#dbeafe;text-align:left;">
                        <th style="padding:0.75rem;">#</th>
                        <th style="padding:0.75rem;">Độc giả</th>
                        <th style="padding:0.75rem;">Sách / bản sao</th>
                        <th style="padding:0.75rem;">Hạn đến lấy</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approved as $r): ?>
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:0.75rem;">#<?= (int) $r['id'] ?></td>
                            <td style="padding:0.75rem;">
                                <strong><?= e($r['ho_ten'] ?? '') ?></strong>
                                <div style="font-size:0.8rem;color:var(--text-muted);"><?= e($r['ma_doc_gia'] ?? '') ?></div>
                            </td>
                            <td style="padding:0.75rem;">
                                <?= e($r['ten_sach'] ?? '') ?>
                                <div style="font-size:0.85rem;font-weight:600;"><?= e($r['ma_ban_sao'] ?? '—') ?></div>
                            </td>
                            <td style="padding:0.75rem;white-space:nowrap;">
                                <?= e(format_datetime($r['han_den_lay'] ?? null) ?: ($r['han_den_lay'] ?? '—')) ?>
                            </td>
                            <td style="padding:0.75rem;">
                                <form method="post" action="<?= e(url('loan-requests/fulfill')) ?>"
                                      onsubmit="return confirm('Lập phiếu mượn cho độc giả này từ bản sao đang giữ chỗ?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="request_id" value="<?= (int) $r['id'] ?>">
                                    <button type="submit" class="btn" style="padding:0.35rem 0.7rem;font-size:0.85rem;">Lập phiếu mượn</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <h2 style="font-size:1.15rem;margin:1.75rem 0 0.75rem;">3. Từ chối / hết hạn / đã nhận</h2>
    <?php if (!$closed): ?>
        <p style="color:var(--text-muted);">Chưa có bản ghi.</p>
    <?php else: ?>
        <?php
        $lab = [
            'tu_choi' => 'Từ chối',
            'het_han' => 'Hết hạn giữ chỗ',
            'da_nhan' => 'Đã lập phiếu',
        ];
        $sty = [
            'tu_choi' => 'background:#fee2e2;color:#991b1b;',
            'het_han' => 'background:#e5e7eb;color:#374151;',
            'da_nhan' => 'background:#dcfce7;color:#166534;',
        ];
        ?>
        <div class="card" style="overflow-x:auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
            <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
                <thead>
                    <tr style="background:var(--primary-light,#f5f0eb);text-align:left;">
                        <th style="padding:0.65rem;">#</th>
                        <th style="padding:0.65rem;">Độc giả</th>
                        <th style="padding:0.65rem;">Sách</th>
                        <th style="padding:0.65rem;">TT</th>
                        <th style="padding:0.65rem;">Ghi chú / lý do</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($closed as $r): ?>
                        <?php $st = $r['trang_thai'] ?? ''; ?>
                        <tr style="border-top:1px solid var(--border);">
                            <td style="padding:0.65rem;">#<?= (int) $r['id'] ?></td>
                            <td style="padding:0.65rem;"><?= e($r['ho_ten'] ?? '') ?></td>
                            <td style="padding:0.65rem;"><?= e($r['ten_sach'] ?? '') ?></td>
                            <td style="padding:0.65rem;">
                                <span style="padding:2px 8px;border-radius:999px;font-size:0.75rem;font-weight:600;<?= $sty[$st] ?? '' ?>">
                                    <?= e($lab[$st] ?? $st) ?>
                                </span>
                            </td>
                            <td style="padding:0.65rem;color:var(--text-muted);">
                                <?= e($r['ly_do_tu_choi'] ?? '—') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>