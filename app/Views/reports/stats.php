<?php
$dangMuon     = (int) ($dangMuon ?? 0);
$quaHan       = (int) ($quaHan ?? 0);
$sapDenHan    = (int) ($sapDenHan ?? 0);
$tongDauSach  = (int) ($tongDauSach ?? 0);
$tongBanSao   = (int) ($tongBanSao ?? 0);
$tongDocGia   = (int) ($tongDocGia ?? 0);
$tongLuotMuon = (int) ($tongLuotMuon ?? 0);
if (!isset($topBooks) || !is_array($topBooks)) {
    $topBooks = [];
}
if (!isset($theoThang) || !is_array($theoThang)) {
    $theoThang = [];
}
if (!isset($trangThaiBanSao) || !is_array($trangThaiBanSao)) {
    $trangThaiBanSao = [];
}
?>
<div class="container mt-4 mb-5" style="max-width: 1200px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h2 style="margin:0;">📊 Thống kê Thư viện</h2>
        <a href="<?= e(url('reports/overdue')) ?>"
           style="background:#422b1c; color:#fff; padding:8px 14px; border-radius:6px; text-decoration:none; font-size:14px;">
            Báo cáo quá hạn
        </a>
    </div>

    <!-- Lưới 4 cột – các ô cùng kích thước -->
    <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:24px;">
        <div style="background:#6b4e3d; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">📖 Đang mượn</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $dangMuon ?></div>
        </div>
        <div style="background:#8b1e1e; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">⚠️ Quá hạn</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $quaHan ?></div>
        </div>
        <div style="background:#856404; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">⏰ Sắp đến hạn</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $sapDenHan ?></div>
        </div>
        <div style="background:#2c3e50; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">🔄 Tổng lượt mượn</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $tongLuotMuon ?></div>
        </div>
        <div style="background:#8c6239; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">📚 Đầu sách</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $tongDauSach ?></div>
        </div>
        <div style="background:#5c4033; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">📑 Bản sao</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $tongBanSao ?></div>
        </div>
        <div style="background:#3d5a3d; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">👥 Độc giả</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;"><?= $tongDocGia ?></div>
        </div>
        <div style="background:#4a5568; color:#fff; padding:18px 16px; border-radius:10px; min-height:96px; display:flex; flex-direction:column; justify-content:center;">
            <div style="font-size:13px; opacity:.9;">📈 Tỷ lệ quá hạn</div>
            <div style="font-size:28px; font-weight:700; margin-top:6px; line-height:1.2;">
                <?= $dangMuon > 0 ? round($quaHan * 100 / $dangMuon, 1) : 0 ?>%
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
        <div style="background:#fff; border:1px solid #dee2e6; border-radius:10px; overflow:hidden;">
            <div style="background:#4a3525; color:#fff; padding:12px 18px;">
                <h5 style="margin:0; font-size:15px;">🔥 Top 5 sách mượn nhiều nhất</h5>
            </div>
            <div style="padding:12px 16px;">
                <?php if (!empty($topBooks)): ?>
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="border-bottom:2px solid #dee2e6; background:#f8f9fa;">
                            <th style="padding:8px; text-align:left;">#</th>
                            <th style="padding:8px; text-align:left;">Tên sách</th>
                            <th style="padding:8px; text-align:center;">Lượt mượn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topBooks as $i => $b): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px; color:#888;"><?= $i + 1 ?></td>
                            <td style="padding:8px;"><?= e($b['ten_sach']) ?></td>
                            <td style="padding:8px; text-align:center; font-weight:700; color:#6b4e3d;"><?= (int)$b['so_luot_muon'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="margin:12px 0; color:#6c757d; text-align:center;">Chưa có dữ liệu mượn sách.</p>
                <?php endif; ?>
            </div>
        </div>

        <div style="background:#fff; border:1px solid #dee2e6; border-radius:10px; overflow:hidden;">
            <div style="background:#4a3525; color:#fff; padding:12px 18px;">
                <h5 style="margin:0; font-size:15px;">📅 Lượt mượn theo tháng (năm <?= date('Y') ?>)</h5>
            </div>
            <div style="padding:12px 16px;">
                <?php if (!empty($theoThang)): ?>
                <table style="width:100%; border-collapse:collapse; font-size:14px;">
                    <thead>
                        <tr style="border-bottom:2px solid #dee2e6; background:#f8f9fa;">
                            <th style="padding:8px; text-align:left;">Tháng</th>
                            <th style="padding:8px; text-align:center;">Số lượt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($theoThang as $t): ?>
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px;">Tháng <?= (int)$t['thang'] ?></td>
                            <td style="padding:8px; text-align:center; font-weight:700; color:#6b4e3d;"><?= (int)$t['so_luot'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="margin:12px 0; color:#6c757d; text-align:center;">Chưa có dữ liệu trong năm nay.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #dee2e6; border-radius:10px; overflow:hidden;">
        <div style="background:#4a3525; color:#fff; padding:12px 18px;">
            <h5 style="margin:0; font-size:15px;">📦 Phân bố trạng thái bản sao</h5>
        </div>
        <div style="padding:12px 16px;">
            <?php if (!empty($trangThaiBanSao)): ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr)); gap:12px;">
                <?php
                $labelMap = [
                    'con_trong' => ['Còn trống', '#6c757d'],
                    'san_sang'  => ['Sẵn sàng', '#198754'],
                    'dang_muon' => ['Đang mượn', '#0d6efd'],
                    'hong'      => ['Hỏng', '#dc3545'],
                    'mat'       => ['Mất', '#495057'],
                    'thanh_ly'  => ['Thanh lý', '#adb5bd'],
                ];
                foreach ($trangThaiBanSao as $tt):
                    $key = $tt['trang_thai'];
                    $info = $labelMap[$key] ?? [$key, '#6c757d'];
                ?>
                <div style="background:<?= $info[1] ?>; color:#fff; padding:14px; border-radius:8px; text-align:center; min-height:72px; display:flex; flex-direction:column; justify-content:center;">
                    <div style="font-size:12px; opacity:.9;"><?= e($info[0]) ?></div>
                    <div style="font-size:22px; font-weight:700; margin-top:4px;"><?= (int)$tt['so_luong'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="margin:12px 0; color:#6c757d; text-align:center;">Chưa có bản sao.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .container [style*="grid-template-columns:repeat(4"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
@media (max-width: 560px) {
    .container [style*="grid-template-columns:repeat(4"],
    .container [style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>