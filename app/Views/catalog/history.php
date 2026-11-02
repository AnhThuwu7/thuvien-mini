<?php 
// Nạp Header chung
if (function_exists('view')) {
    view('layouts/header');
} else {
    $headerPath = __DIR__ . '/../layouts/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
}

$loansList = isset($loans) && is_array($loans) ? $loans : [];
?>

<div class="container my-4" style="max-width: 1100px; margin: 0 auto; padding: 0 15px;">
    <!-- Tiêu đề trang -->
    <div style="margin-bottom: 25px;">
        <h2 style="color: var(--text); font-weight: 700; font-size: 24px; margin-bottom: 6px;">Quản lý Sách đang mượn & Lịch sử</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Theo dõi danh sách các cuốn sách bạn đang mượn, hạn trả và lịch sử mượn trả trước đó.</p>
    </div>

    <!-- Bảng danh sách -->
    <div style="background: #fff; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: left;">
            <thead>
                <tr style="background-color: #fcfcfc; border-bottom: 1px solid var(--border); color: var(--text);">
                    <th style="padding: 14px 18px; font-weight: 600;">Tên sách</th>
                    <th style="padding: 14px 18px; font-weight: 600; text-align: center;">Ngày mượn</th>
                    <th style="padding: 14px 18px; font-weight: 600; text-align: center;">Hạn trả</th>
                    <th style="padding: 14px 18px; font-weight: 600; text-align: center;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($loansList)): ?>
                    <?php foreach ($loansList as $item): ?>
                        <?php 
                            // Thử tìm tất cả các tên cột có thể chứa ngày mượn
                            $ngayMuon = $item['muon_luc'] ?? $item['ngay_muon'] ?? $item['created_at'] ?? $item['ngay_tao'] ?? '';
                            $hanTra = $item['han_tra'] ?? $item['due_date'] ?? 'N/A';
                            $trangThai = $item['trang_thai'] ?? $item['tinh_trang'] ?? 'Đang mượn';
                        ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 14px 18px; font-weight: 600; color: var(--text);">
                                <?= htmlspecialchars($item['ten_sach'] ?? 'N/A') ?>
                            </td>
                            <td style="padding: 14px 18px; text-align: center; color: var(--text-muted);">
                                <?= !empty($ngayMuon) ? htmlspecialchars($ngayMuon) : '<span style="color: red; font-size: 12px;">(Chưa có dữ liệu)</span>' ?>
                            </td>
                            <td style="padding: 14px 18px; text-align: center; color: var(--text-muted);">
                                <?= htmlspecialchars($hanTra) ?>
                            </td>
                            <td style="padding: 14px 18px; text-align: center;">
                                <span style="background: #e0f2fe; color: #0369a1; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                                    <?= htmlspecialchars($trangThai) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            Bạn chưa mượn cuốn sách nào hoặc chưa có dữ liệu phiếu mượn.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Nạp Footer chung
if (function_exists('view')) {
    view('layouts/footer');
} else {
    $footerPath = __DIR__ . '/../layouts/footer.php';
    if (file_exists($footerPath)) require_once $footerPath;
}
?>