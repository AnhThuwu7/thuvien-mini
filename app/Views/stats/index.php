<div class="container mt-4 mb-5" style="max-width: 1200px;">
    <h2 class="mb-4">📊 Báo cáo Thống kê & Quá hạn Thư viện</h2>
    
    <!-- Các thẻ tổng quan -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 25px;">
        <div style="background: #6b4e3d; color: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 8px 0; font-size: 14px;">📖 Sách đang mượn</h4>
            <h2 style="margin: 0; font-size: 26px; font-weight: bold;"><?= $dangMuon ?? 0 ?></h2>
        </div>
        <div style="background: #422b1c; color: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 8px 0; font-size: 14px;">⚠️ Lượt quá hạn</h4>
            <h2 style="margin: 0; font-size: 26px; font-weight: bold;"><?= $quaHan ?? 0 ?></h2>
        </div>
        <div style="background: #8c6239; color: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 8px 0; font-size: 14px;">📚 Tổng đầu sách</h4>
            <h2 style="margin: 0; font-size: 26px; font-weight: bold;"><?= $tongDauSach ?? 0 ?></h2>
        </div>
        <div style="background: #5c4033; color: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h4 style="margin: 0 0 8px 0; font-size: 14px;">👥 Tổng độc giả</h4>
            <h2 style="margin: 0; font-size: 26px; font-weight: bold;"><?= $tongDocGia ?? 0 ?></h2>
        </div>
    </div>

    <!-- Top 5 sách mượn nhiều nhất -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden;">
        <div style="background: #4a3525; color: white; padding: 12px 20px;">
            <h5 style="margin: 0; font-size: 16px;">🔥 Top 5 Sách mượn nhiều nhất</h5>
        </div>
        <div style="padding: 15px;">
            <?php if (!empty($topBooks)): ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #dee2e6; background: #f8f9fa;">
                            <th style="padding: 8px 12px; text-align: left;">Tên sách</th>
                            <th style="padding: 8px 12px; text-align: center; width: 150px;">Số lượt mượn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topBooks as $b): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 8px 12px; color: #212529;"><?= htmlspecialchars($b['ten_sach']) ?></td>
                                <td style="padding: 8px 12px; text-align: center; font-weight: bold; color: #6b4e3d;"><?= $b['so_luot_muon'] ?> lượt</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="margin: 0; color: #6c757d; text-align: center;">Chưa có dữ liệu mượn sách.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bảng danh sách chi tiết phiếu mượn quá hạn kèm ô Tìm kiếm -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; overflow: hidden;">
        <div style="background: #212529; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <h5 style="margin: 0; font-size: 18px;">📋 Danh sách chi tiết phiếu mượn quá hạn</h5>
            
            <!-- Form Tìm kiếm theo tên độc giả -->
            <form action="index.php" method="GET" style="display: flex; gap: 8px; margin: 0;">
                <input type="hidden" name="url" value="stats">
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" placeholder="Tìm tên độc giả..." style="padding: 6px 12px; border-radius: 4px; border: 1px solid #ced4da; font-size: 14px; outline: none; width: 220px;">
                <button type="submit" style="background: #6b4e3d; color: white; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold;">Tìm</button>
                <?php if (!empty($keyword)): ?>
                    <a href="index.php?url=stats" style="background: #6c757d; color: white; padding: 6px 10px; border-radius: 4px; text-decoration: none; font-size: 14px; display: flex; align-items: center;">Xóa</a>
                <?php endif; ?>
            </form>
        </div>
        <div style="padding: 0;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 15px;">
                <thead>
                    <tr style="background-color: #e9ecef; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 8%; text-align: center;">ID Phiếu</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 20%;">Độc giả</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 30%;">Tên sách</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 14%; text-align: center;">Mã bản sao</th>
                        <th style="padding: 12px 15px; border-right: 1px solid #dee2e6; width: 14%; text-align: center;">Hạn trả</th>
                        <th style="padding: 12px 15px; width: 14%; text-align: center;">Số ngày quá hạn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($danhSachQuaHan)): ?>
                        <?php foreach ($danhSachQuaHan as $index => $item): ?>
                            <tr style="background-color: <?= $index % 2 == 0 ? '#f8f9fa' : '#ffffff' ?>; border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center; font-weight: bold;">#<?= $item['id'] ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6;"><?= htmlspecialchars($item['ho_ten'] ?? '') ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; color: #212529;"><?= htmlspecialchars($item['ten_sach'] ?? '') ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center;"><code style="background: #e2e3e5; padding: 2px 6px; border-radius: 4px; color: #333;"><?= htmlspecialchars($item['ma_ban_sao'] ?? '') ?></code></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center; color: #dc3545; font-weight: bold;"><?= $item['han_tra'] ?? '' ?></td>
                                <td style="padding: 12px 15px; text-align: center; color: #dc3545; font-weight: bold;"><?= $item['so_ngay_qua_han'] ?> ngày</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: #6c757d; font-weight: bold;">
                                <?= !empty($keyword) ? 'Không tìm thấy phiếu quá hạn nào phù hợp với từ khóa "'.htmlspecialchars($keyword).'"' : '🎉 Tuyệt vời! Hiện tại không có phiếu mượn nào bị quá hạn.' ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bảng danh sách sắp đến hạn (trong vòng 3 ngày tới) -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background: #856404; color: white; padding: 15px 20px;">
            <h5 style="margin: 0; font-size: 18px;">⏰ Sách sắp đến hạn trả (Trong 3 ngày tới)</h5>
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
                    <?php if (!empty($danhSachSapDenHan)): ?>
                        <?php foreach ($danhSachSapDenHan as $index => $item): ?>
                            <tr style="background-color: <?= $index % 2 == 0 ? '#f8f9fa' : '#ffffff' ?>; border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center; font-weight: bold;">#<?= $item['id'] ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6;"><?= htmlspecialchars($item['ho_ten'] ?? '') ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; color: #212529;"><?= htmlspecialchars($item['ten_sach'] ?? '') ?></td>
                                <td style="padding: 12px 15px; border-right: 1px solid #dee2e6; text-align: center;"><code style="background: #e2e3e5; padding: 2px 6px; border-radius: 4px; color: #333;"><?= htmlspecialchars($item['ma_ban_sao'] ?? '') ?></code></td>
                                <td style="padding: 12px 15px; text-align: center; color: #856404; font-weight: bold;"><?= $item['han_tra'] ?> (Còn <?= $item['so_ngay_con_lai'] ?> ngày)</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #6c757d; font-weight: bold;">
                                Không có phiếu mượn nào sắp đến hạn trong 3 ngày tới.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>