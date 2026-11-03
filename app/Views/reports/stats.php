<?php
$dangMuon     = (int) ($dangMuon ?? 0);
$quaHan       = (int) ($quaHan ?? 0);
$sapDenHan    = (int) ($sapDenHan ?? 0);
$tongDauSach  = (int) ($tongDauSach ?? 0);
$tongBanSao   = (int) ($tongBanSao ?? 0);
$tongDocGia   = (int) ($tongDocGia ?? 0);
$tongLuotMuon = (int) ($tongLuotMuon ?? 0);
$tyLeQuaHan   = $dangMuon > 0 ? round($quaHan * 100 / $dangMuon, 1) : 0;
if (!isset($topBooks) || !is_array($topBooks)) {
    $topBooks = [];
}
if (!isset($theoThang) || !is_array($theoThang)) {
    $theoThang = [];
}
if (!isset($trangThaiBanSao) || !is_array($trangThaiBanSao)) {
    $trangThaiBanSao = [];
}

$statusMeta = [
    'con_trong'    => ['label' => 'Còn trống',  'color' => '#166534', 'bg' => '#dcfce7'],
    'dang_muon'    => ['label' => 'Đang mượn',  'color' => '#1e40af', 'bg' => '#dbeafe'],
    'dang_giu_cho' => ['label' => 'Giữ chỗ',    'color' => '#92400e', 'bg' => '#fef3c7'],
    'hong'         => ['label' => 'Hỏng',       'color' => '#9f1239', 'bg' => '#ffe4e6'],
    'mat'          => ['label' => 'Mất',        'color' => '#374151', 'bg' => '#e5e7eb'],
    'thanh_ly'     => ['label' => 'Thanh lý',   'color' => '#4b5563', 'bg' => '#f3f4f6'],
];
?>
<style>
.stats-page { max-width: 1200px; margin: 0 auto; padding: 0.5rem 0 2.5rem; }
.stats-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:1.5rem; }
.stats-header h1 { margin:0; font-size:1.55rem; color:#3d2b1f; }
.stats-section-title { font-size:0.8rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#9a8778; margin:0 0 0.75rem; }
.kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:1.75rem; }
.kpi-card {
    background:#fff; border:1px solid var(--border,#e5ddd3); border-radius:12px;
    padding:1.1rem 1.15rem; display:flex; flex-direction:column; gap:0.35rem;
    box-shadow:0 1px 4px rgba(60,40,20,.04);
}
.kpi-card .kpi-label { font-size:0.85rem; color:#7a6858; font-weight:500; display:flex; align-items:center; gap:6px; }
.kpi-card .kpi-value { font-size:1.85rem; font-weight:700; line-height:1.15; color:#3d2b1f; }
.kpi-card.accent-red    { border-left:4px solid #dc2626; }
.kpi-card.accent-red .kpi-value { color:#b91c1c; }
.kpi-card.accent-amber  { border-left:4px solid #d97706; }
.kpi-card.accent-amber .kpi-value { color:#b45309; }
.kpi-card.accent-blue   { border-left:4px solid #2563eb; }
.kpi-card.accent-blue .kpi-value { color:#1d4ed8; }
.kpi-card.accent-green  { border-left:4px solid #16a34a; }
.kpi-card.accent-green .kpi-value { color:#15803d; }
.kpi-card.accent-brown  { border-left:4px solid #8c6239; }
.kpi-card.accent-slate  { border-left:4px solid #64748b; }
.stats-two-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:1.75rem; }
.stats-panel { background:#fff; border:1px solid var(--border,#e5ddd3); border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(60,40,20,.04); }
.stats-panel-head { background:#4a3525; color:#fff; padding:0.85rem 1.15rem; font-size:0.95rem; font-weight:600; }
.stats-panel-body { padding:0.75rem 1rem 1rem; }
.stats-table { width:100%; border-collapse:collapse; font-size:0.9rem; }
.stats-table th { text-align:left; padding:0.55rem 0.5rem; border-bottom:2px solid #e5ddd3; color:#7a6858; font-weight:600; font-size:0.8rem; text-transform:uppercase; letter-spacing:.03em; }
.stats-table td { padding:0.6rem 0.5rem; border-bottom:1px solid #f0ebe6; color:#3d2b1f; }
.stats-table tr:last-child td { border-bottom:none; }
.stats-table .num { text-align:center; font-weight:700; color:#6b4e3d; }
.status-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:12px; }
.status-card {
    border-radius:10px; padding:0.9rem 0.85rem; text-align:center;
    border:1px solid transparent;
}
.status-card .st-val { font-size:1.5rem; font-weight:700; line-height:1.2; }
.status-card .st-lbl { font-size:0.8rem; margin-top:0.25rem; font-weight:500; }
@media (max-width:900px) {
    .kpi-grid { grid-template-columns:repeat(2,1fr); }
    .stats-two-col { grid-template-columns:1fr; }
}
@media (max-width:520px) {
    .kpi-grid { grid-template-columns:1fr; }
}
</style>

<div class="stats-page">
    <div class="stats-header">
        <h1>📊 Thống kê thư viện</h1>
        <a href="<?= e(url('reports/overdue')) ?>" class="btn">Báo cáo quá hạn</a>
    </div>

    <!-- Nhóm 1: Nghiệp vụ mượn – trả (quan trọng nhất với thủ thư) -->
    <p class="stats-section-title">Nghiệp vụ mượn – trả</p>
    <div class="kpi-grid">
        <div class="kpi-card accent-blue">
            <div class="kpi-label">📖 Đang mượn</div>
            <div class="kpi-value"><?= $dangMuon ?></div>
        </div>
        <div class="kpi-card accent-red">
            <div class="kpi-label">⚠️ Quá hạn</div>
            <div class="kpi-value"><?= $quaHan ?></div>
        </div>
        <div class="kpi-card accent-amber">
            <div class="kpi-label">⏰ Sắp đến hạn</div>
            <div class="kpi-value"><?= $sapDenHan ?></div>
        </div>
        <div class="kpi-card accent-slate">
            <div class="kpi-label">📈 Tỷ lệ quá hạn</div>
            <div class="kpi-value"><?= $tyLeQuaHan ?>%</div>
        </div>
    </div>

    <!-- Nhóm 2: Kho & độc giả -->
    <p class="stats-section-title">Kho sách & độc giả</p>
    <div class="kpi-grid">
        <div class="kpi-card accent-brown">
            <div class="kpi-label">📚 Đầu sách</div>
            <div class="kpi-value"><?= $tongDauSach ?></div>
        </div>
        <div class="kpi-card accent-brown">
            <div class="kpi-label">📑 Bản sao</div>
            <div class="kpi-value"><?= $tongBanSao ?></div>
        </div>
        <div class="kpi-card accent-green">
            <div class="kpi-label">👥 Độc giả</div>
            <div class="kpi-value"><?= $tongDocGia ?></div>
        </div>
        <div class="kpi-card accent-blue">
            <div class="kpi-label">🔄 Tổng lượt mượn</div>
            <div class="kpi-value"><?= $tongLuotMuon ?></div>
        </div>
    </div>

    <!-- Hai bảng -->
    <div class="stats-two-col">
        <div class="stats-panel">
            <div class="stats-panel-head">🔥 Top 5 sách mượn nhiều nhất</div>
            <div class="stats-panel-body">
                <?php if (!empty($topBooks)): ?>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Tên sách</th>
                            <th class="num">Lượt mượn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topBooks as $i => $b): ?>
                        <tr>
                            <td style="color:#9a8778;"><?= $i + 1 ?></td>
                            <td><?= e($b['ten_sach']) ?></td>
                            <td class="num"><?= (int) $b['so_luot_muon'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="margin:12px 0; color:#9a8778; text-align:center;">Chưa có dữ liệu mượn sách.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-panel">
            <div class="stats-panel-head">📅 Lượt mượn theo tháng (<?= date('Y') ?>)</div>
            <div class="stats-panel-body">
                <?php if (!empty($theoThang)): ?>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Tháng</th>
                            <th class="num">Số lượt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($theoThang as $t): ?>
                        <tr>
                            <td>Tháng <?= (int) $t['thang'] ?></td>
                            <td class="num"><?= (int) $t['so_luot'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p style="margin:12px 0; color:#9a8778; text-align:center;">Chưa có dữ liệu trong năm nay.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Phân bố trạng thái bản sao -->
    <div class="stats-panel">
        <div class="stats-panel-head">📦 Phân bố trạng thái bản sao</div>
        <div class="stats-panel-body">
            <?php if (!empty($trangThaiBanSao)): ?>
            <div class="status-grid">
                <?php foreach ($trangThaiBanSao as $row): ?>
                    <?php
                        $key = $row['trang_thai'] ?? '';
                        $meta = $statusMeta[$key] ?? ['label' => $key, 'color' => '#374151', 'bg' => '#f3f4f6'];
                        $count = (int) ($row['so_luong'] ?? 0);
                    ?>
                    <div class="status-card" style="background:<?= e($meta['bg']) ?>; color:<?= e($meta['color']) ?>; border-color:<?= e($meta['color']) ?>22;">
                        <div class="st-val"><?= $count ?></div>
                        <div class="st-lbl"><?= e($meta['label']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="margin:12px 0; color:#9a8778; text-align:center;">Chưa có dữ liệu bản sao.</p>
            <?php endif; ?>
        </div>
    </div>
</div>