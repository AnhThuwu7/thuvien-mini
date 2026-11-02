<?php
// Gán mặc định để IDE không báo Undefined variable + an toàn khi thiếu data
$minDays         = (int) ($minDays ?? 0);
$soNgaySapDenHan = (int) ($soNgaySapDenHan ?? 3);
$soQuaHan        = (int) ($soQuaHan ?? 0);
$soSapDenHan     = (int) ($soSapDenHan ?? 0);
$phiPhatMoiNgay  = (int) ($phiPhatMoiNgay ?? 2000);
$keyword         = (string) ($keyword ?? '');
$type            = (string) ($type ?? 'overdue');
if (!isset($danhSach) || !is_array($danhSach)) {
    $danhSach = [];
}
?>
<div class="container mt-4 mb-5" style="max-width: 1200px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2 style="margin:0;">📋 Báo cáo Quá hạn / Sắp đến hạn</h2>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a href="<?= e(url('reports/stats')) ?>" style="background:#6b4e3d; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px;">← Thống kê</a>
            <a href="<?= e(url('reports/export-overdue' . ($minDays > 0 ? '?min_days=' . $minDays : ''))) ?>"
               style="background:#198754; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px;">
                ⬇ Xuất CSV quá hạn
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:20px;">
        <div style="background:#8b1e1e; color:#fff; padding:14px; border-radius:8px;">
            <div style="font-size:13px;">Phiếu quá hạn</div>
            <div style="font-size:24px; font-weight:700;"><?= $soQuaHan ?></div>
        </div>
        <div style="background:#856404; color:#fff; padding:14px; border-radius:8px;">
            <div style="font-size:13px;">Sắp đến hạn (≤ <?= $soNgaySapDenHan ?> ngày)</div>
            <div style="font-size:24px; font-weight:700;"><?= $soSapDenHan ?></div>
        </div>
        <div style="background:#5c4033; color:#fff; padding:14px; border-radius:8px;">
            <div style="font-size:13px;">Phí phạt / ngày</div>
            <div style="font-size:24px; font-weight:700;"><?= number_format($phiPhatMoiNgay, 0, ',', '.') ?> ₫</div>
        </div>
    </div>

    <form method="GET" action="<?= e(url('reports/overdue')) ?>"
          style="background:#fff; border:1px solid #dee2e6; border-radius:10px; padding:16px; margin-bottom:20px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;">
        <div>
            <label style="display:block; font-size:13px; margin-bottom:4px; color:#555;">Loại phiếu</label>
            <select name="type" style="padding:7px 10px; border:1px solid #ced4da; border-radius:6px; font-size:14px;">
                <option value="overdue" <?= $type === 'overdue' ? 'selected' : '' ?>>Chỉ quá hạn</option>
                <option value="soon"    <?= $type === 'soon'    ? 'selected' : '' ?>>Sắp đến hạn</option>
                <option value="all"     <?= $type === 'all'     ? 'selected' : '' ?>>Tất cả chưa trả</option>
            </select>
        </div>
        <div>
            <label style="display:block; font-size:13px; margin-bottom:4px; color:#555;">Tối thiểu số ngày trễ</label>
            <input type="number" name="min_days" min="0" value="<?= $minDays ?>"
                   style="padding:7px 10px; border:1px solid #ced4da; border-radius:6px; width:100px; font-size:14px;">
        </div>
        <div style="flex:1; min-width:200px;">
            <label style="display:block; font-size:13px; margin-bottom:4px; color:#555;">Tìm (tên / email / mã bản sao / sách)</label>
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Nhập từ khóa..."
                   style="padding:7px 10px; border:1px solid #ced4da; border-radius:6px; width:100%; font-size:14px;">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" style="background:#6b4e3d; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:600;">Lọc</button>
            <a href="<?= e(url('reports/overdue')) ?>" style="background:#6c757d; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px;">Xóa lọc</a>
        </div>
    </form>

    <div style="background:#fff; border:1px solid #dee2e6; border-radius:10px; overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#e9ecef; border-bottom:2px solid #dee2e6;">
                        <th style="padding:10px 12px; text-align:center;">ID</th>
                        <th style="padding:10px 12px; text-align:left;">Độc giả</th>
                        <th style="padding:10px 12px; text-align:left;">Sách / Mã bản sao</th>
                        <th style="padding:10px 12px; text-align:center;">Ngày mượn</th>
                        <th style="padding:10px 12px; text-align:center;">Hạn trả</th>
                        <th style="padding:10px 12px; text-align:center;">Số ngày</th>
                        <th style="padding:10px 12px; text-align:right;">Phí phạt ước tính</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhSach)): ?>
                        <?php foreach ($danhSach as $i => $item): ?>
                            <?php $isOverdue = ((int)($item['so_ngay_qua_han'] ?? 0)) > 0; ?>
                            <tr style="background:<?= $i % 2 === 0 ? '#f8f9fa' : '#fff' ?>; border-bottom:1px solid #eee;">
                                <td style="padding:10px 12px; text-align:center; font-weight:600;">#<?= (int)$item['id'] ?></td>
                                <td style="padding:10px 12px;">
                                    <div style="font-weight:600;"><?= e($item['ho_ten'] ?? '') ?></div>
                                    <div style="font-size:12px; color:#666;"><?= e($item['email'] ?? '') ?></div>
                                    <?php if (!empty($item['bi_khoa'])): ?>
                                        <span style="background:#dc3545; color:#fff; font-size:11px; padding:1px 6px; border-radius:4px;">Đã khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px;">
                                    <div><?= e($item['ten_sach'] ?? '') ?></div>
                                    <code style="background:#e9ecef; padding:1px 6px; border-radius:4px; font-size:12px;"><?= e($item['ma_ban_sao'] ?? '') ?></code>
                                </td>
                                <td style="padding:10px 12px; text-align:center;"><?= e(substr($item['muon_luc'] ?? '', 0, 10)) ?></td>
                                <td style="padding:10px 12px; text-align:center; font-weight:600; color:<?= $isOverdue ? '#dc3545' : '#856404' ?>;">
                                    <?= e($item['han_tra'] ?? '') ?>
                                </td>
                                <td style="padding:10px 12px; text-align:center; font-weight:700; color:<?= $isOverdue ? '#dc3545' : '#856404' ?>;">
                                    <?php if ($isOverdue): ?>
                                        Trễ <?= (int)$item['so_ngay_qua_han'] ?> ngày
                                    <?php else: ?>
                                        Còn <?= abs((int)$item['so_ngay_qua_han']) ?> ngày
                                    <?php endif; ?>
                                </td>
                                <td style="padding:10px 12px; text-align:right; font-weight:600;">
                                    <?= $isOverdue ? number_format((float)($item['phi_phat_uoc_tinh'] ?? 0), 0, ',', '.') . ' ₫' : '—' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:36px; color:#6c757d;">Không có phiếu nào khớp điều kiện lọc.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if (!empty($danhSach)): ?>
        <div style="padding:10px 16px; background:#f8f9fa; border-top:1px solid #dee2e6; font-size:13px; color:#555;">
            Hiển thị <strong><?= count($danhSach) ?></strong> phiếu
        </div>
        <?php endif; ?>
    </div>
</div>