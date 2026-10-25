<div class="container mt-4 mb-5" style="max-width: 1200px;">
    <h2 class="mb-4">📊 Báo cáo Thống kê & Quá hạn Thư viện</h2>
    
    <!-- Thẻ thống kê tổng quan với tone màu nâu hài hòa -->
    <div class="row my-4" style="display: flex; gap: 20px; margin-bottom: 25px;">
        <div style="flex: 1; background: #6b4e3d; color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 10px 0; font-size: 16px;">📖 Sách đang được mượn</h4>
            <h2 style="margin: 0; font-size: 32px; font-weight: bold;"><?= $dangMuon ?? 0 ?></h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Tổng số lượt sách hiện đang cho độc giả mượn.</p>
        </div>
        <div style="flex: 1; background: #422b1c; color: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 10px 0; font-size: 16px;">⚠️ Lượt mượn quá hạn</h4>
            <h2 style="margin: 0; font-size: 32px; font-weight: bold;"><?= $quaHan ?? 0 ?></h2>
            <p style="margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;">Các phiếu mượn đã vượt quá hạn trả cần thu hồi gấp.</p>
        </div>
    </div>

    <!-- Bảng danh sách chi tiết phiếu quá hạn -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background: #212529; color: white; padding: 15px 20px;">
            <h5 style="margin: 0; font-size: 18px;">📋 Danh sách chi tiết phiếu mượn quá hạn</h5>
        </div>
        <div style="padding: 0;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="background-color: #e9ecef; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 10%; text-align: center;">ID Phiếu</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 22%;">Độc giả</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 33%;">Tên sách</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 15%; text-align: center;">Mã bản sao</th>
                        <th style="padding: 12px 15px; width: 20%; text-align: center;">Hạn trả</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhSachQuaHan)): ?>
                        <?php foreach ($danhSachQuaHan as $index => $item): ?>
                            <tr style="background-color: <?= $index % 2 == 0 ? '#f8f9fa' : '#ffffff' ?>; border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center; font-weight: bold;">#<?= $item['id'] ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6;"><?= htmlspecialchars($item['ho_ten'] ?? '') ?></td>
                                <!-- Tên sách hiển thị màu đen bình thường, không in đậm giống cột Độc giả -->
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; color: #212529;"><?= htmlspecialchars($item['ten_sach'] ?? '') ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center;"><code style="background: #e2e3e5; padding: 2px 6px; border-radius: 4px; color: #333;"><?= htmlspecialchars($item['ma_ban_sao'] ?? '') ?></code></td>
                                <td style="padding: 12px 15px; text-align: center; color: #dc3545; font-weight: bold;"><?= $item['han_tra'] ?? '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #198754; font-weight: bold;">
                                🎉 Tuyệt vời! Hiện tại không có phiếu mượn nào bị quá hạn.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>