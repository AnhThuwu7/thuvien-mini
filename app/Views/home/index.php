<?php
$books = $books ?? [];
$activeLoans = $activeLoans ?? [];
$keyword = $keyword ?? '';
$isReader = !empty($isReader);
$isGuest = !empty($isGuest);

$statusLoan = [
    'dang_muon' => ['label' => 'Đang mượn', 'style' => 'background:#dbeafe;color:#1e40af;'],
    'qua_han'   => ['label' => 'Quá hạn',   'style' => 'background:#fee2e2;color:#991b1b;'],
];
?>

<?php if (!empty($isReader) && !empty(Auth::user()['bi_khoa'])): ?>
<div class="alert alert-warning" style="margin:0.75rem 0 1rem;">
    <strong>Tài khoản đang bị khóa.</strong>
    Bạn xem được sách nhưng <strong>không gửi yêu cầu / mượn được</strong>.
    <?php if (!empty(Auth::user()['ly_do_khoa'])): ?>
        Lý do: <?= e((string) Auth::user()['ly_do_khoa']) ?>.
    <?php endif; ?>
    Liên hệ thủ thư để mở khóa.
</div>
<?php endif; ?>

<section style="width:100%;padding:0.25rem 0 2rem;">
<?php if ($isReader): ?>
    <div style="margin-bottom:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;margin-bottom:1rem;">
            <div>
                <h1 style="margin:0 0 0.35rem;font-size:1.5rem;">Sách bạn đang mượn</h1>
                <p style="margin:0;color:var(--text-muted);">Ấn vào thẻ để xem ngày mượn, hạn trả chi tiết.</p>
            </div>
            <a href="<?= e(url('loans/mine')) ?>" class="btn btn-outline">Xem lịch sử mượn</a>
        </div>

        <?php if (empty($activeLoans)): ?>
            <div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);color:var(--text-muted);">
                Bạn chưa mượn cuốn nào. Hãy <a href="<?= e(url('catalog')) ?>">tra cứu sách</a> và gửi yêu cầu mượn.
            </div>
        <?php else: ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;">
                <?php foreach ($activeLoans as $loan): ?>
                    <?php
                        $st = $loan['trang_thai_hien_thi'] ?? 'dang_muon';
                        $meta = $statusLoan[$st] ?? $statusLoan['dang_muon'];
                    ?>
                    <a href="<?= e(url('loans/show/' . (int) $loan['phieu_muon_id'])) ?>"
                       style="text-decoration:none;color:inherit;display:flex;flex-direction:column;background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:0 2px 8px rgba(60,40,20,.06);">
                        <div style="height:200px;background:#f5f0eb;display:flex;align-items:center;justify-content:center;">
                            <?php if (!empty($loan['anh_bia'])): ?>
                                <img src="<?= e(url(ltrim($loan['anh_bia'], '/'))) ?>" alt=""
                                     style="max-height:100%;max-width:100%;object-fit:contain;">
                            <?php else: ?>
                                <span style="color:var(--text-muted);font-size:0.9rem;">Không có ảnh</span>
                            <?php endif; ?>
                        </div>
                        <div style="padding:0.9rem 1rem;flex:1;display:flex;flex-direction:column;gap:0.35rem;">
                            <strong style="font-size:1rem;line-height:1.35;"><?= e($loan['ten_sach'] ?? '') ?></strong>
                            <span style="font-size:0.85rem;color:var(--text-muted);"><?= e($loan['ma_ban_sao'] ?? '') ?></span>
                            <span style="display:inline-block;align-self:flex-start;padding:2px 10px;border-radius:999px;font-size:0.75rem;font-weight:600;<?= $meta['style'] ?>">
                                <?= e($meta['label']) ?>
                            </span>
                            <span style="font-size:0.85rem;color:var(--text-muted);margin-top:auto;">
                                Hạn trả: <?= e(function_exists('format_date') ? format_date($loan['han_tra'] ?? null) : substr((string)($loan['han_tra'] ?? ''), 0, 10)) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <hr style="border:none;border-top:1px solid var(--border);margin:1.5rem 0 2rem;">
<?php endif; ?>

    <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
        <div>
            <h1 style="margin:0 0 0.35rem;font-size:1.5rem;"><?= $isReader ? 'Khám phá sách' : 'Thư viện mini' ?></h1>
            <p style="margin:0;color:var(--text-muted);">
                <?php if ($isGuest): ?>
                    Xem sách tự do. Muốn đặt mượn hãy đăng nhập hoặc đăng ký tài khoản độc giả.
                <?php else: ?>
                    Tìm sách theo tên, tác giả, NXB…
                <?php endif; ?>
            </p>
        </div>
        
    </div>

    <form method="get" action="<?= e(url()) ?>" style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:1.25rem;">
        <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Tìm tên sách, tác giả, NXB…"
               style="flex:1;min-width:200px;padding:0.55rem 0.85rem;border:1px solid var(--border);border-radius:var(--radius);">
        <button type="submit" class="btn">Tìm</button>
        <?php if ($keyword !== ''): ?>
            <a href="<?= e(url()) ?>" class="btn btn-outline">Xóa lọc</a>
        <?php endif; ?>
    </form>

    <?php if (empty($books)): ?>
        <p style="color:var(--text-muted);">Không có sách phù hợp.</p>
    <?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;">
            <?php foreach ($books as $b): ?>
                <?php $avail = (int) ($b['so_con_trong'] ?? 0); ?>
                <a href="<?= e(url('catalog/show/' . (int) $b['id'])) ?>"
                   style="text-decoration:none;color:inherit;display:flex;flex-direction:column;background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
                    <div style="height:220px;background:#f5f0eb;display:flex;align-items:center;justify-content:center;">
                        <?php if (!empty($b['anh_bia'])): ?>
                            <img src="<?= e(url(ltrim($b['anh_bia'], '/'))) ?>" alt=""
                                 style="max-height:100%;max-width:100%;object-fit:contain;">
                        <?php else: ?>
                            <span style="color:var(--text-muted);">Không có ảnh</span>
                        <?php endif; ?>
                    </div>
                    <div style="padding:0.85rem 1rem;flex:1;display:flex;flex-direction:column;gap:0.25rem;">
                        <strong style="line-height:1.35;"><?= e($b['ten_sach'] ?? '') ?></strong>
                        <span style="font-size:0.85rem;color:var(--text-muted);">
                            <?= e($b['ten_tac_gia'] ?: 'Đang cập nhật') ?>
                        </span>
                        <span style="font-size:0.8rem;color:var(--text-muted);margin-top:auto;">
                            <?php if (!empty($isGuest)): ?>
                                <?= $avail > 0 ? 'Có thể mượn' : 'Hết bản trống' ?>
                            <?php else: ?>
                                <?= $avail > 0 ? ('Còn ' . $avail . ' bản') : 'Hết bản trống' ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>