<?php
/**
 * Chi tiết sách — ĐỘC GIẢ
 * Chỉ xem: ảnh, thông tin, trạng thái bản sao.
 * KHÔNG: sửa sách, quản lý bản sao, tên người mượn, mã phiếu.
 */
$book = $book ?? [];
$copies = $copies ?? [];
$ten_danh_muc = $ten_danh_muc ?? '';
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

<section style="max-width:960px;margin:0 auto;padding:1.5rem 0;">
    <div style="margin-bottom:1rem;">
        <a href="<?= e(url('catalog')) ?>" class="btn btn-outline">← Quay lại tra cứu</a>
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
                <h1 style="margin-top:0;font-size:1.5rem;"><?= e($book['ten_sach'] ?? '') ?></h1>
                <p style="margin:0.4rem 0;"><strong>Danh mục:</strong> <?= e($ten_danh_muc !== '' ? $ten_danh_muc : 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>ISBN:</strong> <?= e($book['isbn'] ?: 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Nhà xuất bản:</strong> <?= e($book['nha_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Năm xuất bản:</strong> <?= e($book['nam_xuat_ban'] ?: 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Ngôn ngữ:</strong> <?= e($book['ngon_ngu'] ?? 'Chưa cập nhật') ?></p>
                <p style="margin:0.4rem 0;"><strong>Tình trạng:</strong>
                    <?= $so_con_trong > 0
                        ? ('Còn ' . $so_con_trong . '/' . $tong_ban_sao . ' bản trống')
                        : ($tong_ban_sao > 0 ? 'Hết sách (không còn bản trống)' : 'Chưa có bản sao') ?>
                </p>
                <p style="margin:1rem 0 0.35rem;"><strong>Mô tả:</strong></p>
                <div style="white-space:pre-line;line-height:1.7;color:var(--text-muted);">
                    <?= e($book['mo_ta'] ?: 'Chưa có mô tả cho sách này.') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="padding:1.5rem;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
        <h2 style="margin:0 0 0.75rem;font-size:1.15rem;">
            Danh sách bản sao
            <span style="font-weight:500;color:var(--text-muted);font-size:0.95rem;">
                (<?= $so_con_trong ?>/<?= $tong_ban_sao ?> bản trống)
            </span>
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
                                    <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:0.8rem;font-weight:600;<?= $style ?>">
                                        <?= e($label) ?>
                                    </span>
                                </td>
                                <td style="padding:0.75rem;color:var(--text-muted);"><?= e($c['vi_tri_ke'] ?: '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

                <?php if (Auth::hasRole('doc_gia')): ?>
            <?php $canRequest = $so_con_trong > 0; ?>
            <div style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--border);">
                <h3 style="margin:0 0 0.75rem;font-size:1.1rem;">Đặt yêu cầu mượn</h3>
                <?php if ($canRequest): ?>
                    <p style="margin:0 0 1rem;font-size:0.9rem;color:var(--text-muted);">
                        Còn <strong><?= (int) $so_con_trong ?></strong> bản trống.
                        Gửi yêu cầu tới thủ thư; khi duyệt, một bản sẽ được giữ chỗ cho bạn.
                    </p>
                    <form method="post" action="<?= e(url('loan-requests/store')) ?>" style="display:flex;flex-direction:column;gap:0.75rem;max-width:420px;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="sach_id" value="<?= (int) ($book['id'] ?? 0) ?>">
                        <label style="font-size:0.9rem;">
                            Ghi chú (không bắt buộc)
                            <input type="text" name="ghi_chu" maxlength="255" placeholder="Ví dụ: cần lấy trong tuần này"
                                   style="display:block;width:100%;margin-top:0.35rem;padding:0.55rem 0.75rem;border:1px solid var(--border);border-radius:8px;">
                        </label>
                        <button type="submit" class="btn" style="align-self:flex-start;">
                            Gửi yêu cầu mượn
                        </button>
                    </form>
                <?php else: ?>
                    <p style="margin:0 0 0.75rem;font-size:0.9rem;color:#92400e;background:#fef3c7;padding:0.75rem 1rem;border-radius:8px;">
                        Hiện <strong>không còn bản trống</strong> (0/<?= (int) $tong_ban_sao ?>).
                        Không thể gửi yêu cầu mượn. Vui lòng quay lại khi có sách trả về kệ.
                    </p>
                    <button type="button" class="btn" disabled
                            style="opacity:0.45;cursor:not-allowed;pointer-events:none;align-self:flex-start;">
                        Gửi yêu cầu mượn
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>