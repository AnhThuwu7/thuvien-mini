<?php 
// Nạp Header chung
if (function_exists('view')) {
    view('layouts/header');
} else {
    $headerPath = __DIR__ . '/../layouts/header.php';
    if (file_exists($headerPath)) require_once $headerPath;
}

// Lấy danh sách sách truyền từ CatalogController
$allBooks = isset($books) && is_array($books) ? $books : [];

// Lấy giá trị lọc từ URL ($_GET)
$keyword = trim($_GET['keyword'] ?? '');
$selectedCategory = $_GET['category'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$selectedPublisher = trim($_GET['publisher'] ?? ''); // Mục 5: Tìm theo NXB

// 1. Xử lý lọc dữ liệu đa tiêu chí
$filteredBooks = array_filter($allBooks, function($book) use ($keyword, $selectedCategory, $selectedStatus, $selectedPublisher) {
    $tenSach = $book['ten_sach'] ?? '';
    $tacGia = $book['tac_gia'] ?? '';
    $nhaXuatBan = $book['nha_xuat_ban'] ?? '';
    $danhMucId = $book['danh_muc_id'] ?? '';

    // Lọc theo từ khóa chung (Tên sách hoặc tác giả)
    if ($keyword !== '') {
        $matchKeyword = mb_stripos($tenSach, $keyword) !== false || mb_stripos($tacGia, $keyword) !== false;
        if (!$matchKeyword) return false;
    }
    // Lọc theo Nhà xuất bản (Mục 5)
    if ($selectedPublisher !== '' && mb_stripos($nhaXuatBan, $selectedPublisher) === false) {
        return false;
    }
    // Lọc theo danh mục
    if ($selectedCategory !== '' && $danhMucId != $selectedCategory) {
        return false;
    }
    return true;
});

// 2. Xử lý phân trang (Mục 4)
$perPage = 9; // Số lượng sách hiển thị trên 1 trang
$totalBooks = count($filteredBooks);
$totalPages = max(1, ceil($totalBooks / $perPage));
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
if ($page > $totalPages) $page = $totalPages;

// Cắt mảng sách theo trang hiện tại
$offset = ($page - 1) * $perPage;
$paginatedBooks = array_slice($filteredBooks, $offset, $perPage);
?>

<div class="container my-4" style="max-width: 1100px; margin: 0 auto; padding: 0 15px;">
    <div style="margin-bottom: 25px;">
        <h2 style="color: var(--text); font-weight: 700; font-size: 24px; margin-bottom: 6px;">Tra cứu danh mục sách</h2>
        <p style="color: var(--text-muted); font-size: 14px; margin: 0;">Tìm kiếm đa tiêu chí, kiểm tra thông tin chi tiết và tình trạng bản sao.</p>
    </div>

    <!-- Form tìm kiếm đa tiêu chí rõ hơn (Mục 3 & 5) -->
    <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 25px; background: #fff; padding: 15px; border-radius: 10px; border: 1px solid var(--border);">
        <!-- Ô nhập từ khóa tên sách / tác giả -->
        <input type="text" name="keyword" placeholder="Nhập tên sách hoặc tác giả..." value="<?= htmlspecialchars($keyword) ?>" style="flex: 1; min-width: 200px; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none;">
        
        <!-- Ô lọc Nhà xuất bản (Mục 5) -->
        <input type="text" name="publisher" placeholder="Nhà xuất bản..." value="<?= htmlspecialchars($selectedPublisher) ?>" style="width: 160px; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; outline: none;">

        <!-- Dropdown lọc danh mục -->
        <select name="category" style="padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; background: #fff;">
            <option value="">Tất cả danh mục</option>
            <option value="1" <?= $selectedCategory == '1' ? 'selected' : '' ?>>Công nghệ thông tin</option>
            <option value="2" <?= $selectedCategory == '2' ? 'selected' : '' ?>>Kinh tế / Kỹ năng</option>
            <option value="3" <?= $selectedCategory == '3' ? 'selected' : '' ?>>Văn học nghệ thuật</option>
        </select>

        <!-- Dropdown lọc Còn / Hết sách (Mục 3) -->
        <select name="status" style="padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 14px; background: #fff;">
            <option value="">Tất cả trạng thái</option>
            <option value="con_sach" <?= $selectedStatus == 'con_sach' ? 'selected' : '' ?>>Còn sách</option>
            <option value="het_sach" <?= $selectedStatus == 'het_sach' ? 'selected' : '' ?>>Hết sách</option>
        </select>

        <button type="submit" style="background: var(--primary, #4f46e5); color: white; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 600; cursor: pointer;">Tìm kiếm</button>
        <a href="index.php" style="background: #e5e7eb; color: #374151; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">Đặt lại</a>
    </form>

    <!-- Danh sách hiển thị kết quả dạng lưới card -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; min-height: 250px;">
        <?php if (!empty($paginatedBooks)): ?>
            <?php foreach ($paginatedBooks as $book): ?>
                <div style="background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                            <span style="font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase;">Mã DM: <?= htmlspecialchars($book['danh_muc_id'] ?? 'N/A') ?></span>
                            <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">Còn trống</span>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 700; color: var(--text); margin: 0 0 6px 0;"><?= htmlspecialchars($book['ten_sach'] ?? '') ?></h3>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 4px 0;">Tác giả: <?= htmlspecialchars($book['tac_gia'] ?? 'Chưa cập nhật') ?></p>
                        <p style="font-size: 13px; color: var(--text-muted); margin: 0 0 12px 0;">NXB: <?= htmlspecialchars($book['nha_xuat_ban'] ?? 'Chưa cập nhật') ?></p>
                    </div>
                    
                    <button onclick='openDetailModal(<?= json_encode($book) ?>)' style="width: 100%; background: #f3f4f6; color: #1f2937; border: 1px solid #d1d5db; padding: 8px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; transition: 0.2s;">Chi tiết & Bản sao</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted); background: #fff; border-radius: 10px; border: 1px solid var(--border);">
                Không tìm thấy cuốn sách nào phù hợp với từ khóa và bộ lọc của bạn.
            </div>
        <?php endif; ?>
    </div>

    <!-- Thanh phân trang (Mục 4) -->
    <?php if ($totalPages > 1): ?>
        <div style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 30px;">
            <?php 
            // Giữ lại các tham số tìm kiếm trên URL khi chuyển trang
            $queryParams = $_GET;
            for ($i = 1; $i <= $totalPages; $i++): 
                $queryParams['page'] = $i;
                $queryString = http_build_query($queryParams);
                $isActive = ($i == $page);
                $btnBg = $isActive ? 'var(--primary, #4f46e5)' : '#fff';
                $btnColor = $isActive ? '#fff' : '#374151';
                $btnBorder = $isActive ? 'var(--primary, #4f46e5)' : 'var(--border)';
            ?>
                <a href="?<?= $queryString ?>" style="padding: 8px 14px; background: <?= $btnBg ?>; color: <?= $btnColor ?>; border: 1px solid <?= $btnBorder ?>; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL CHI TIẾT SÁCH VÀ DANH SÁCH BẢN SAO X/Y -->
<div id="bookDetailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: #fff; width: 600px; max-width: 90%; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); position: relative; max-height: 90vh; overflow-y: auto;">
        <h3 id="modalTitle" style="margin-top: 0; color: var(--text); font-size: 20px; border-bottom: 1px solid var(--border); padding-bottom: 12px;"></h3>
        
        <div style="margin: 15px 0; font-size: 14px; color: var(--text); line-height: 1.6;">
            <p><strong>📖 Tên sách:</strong> <span id="modalBookName"></span></p>
            <p><strong>🏷️ Mã danh mục:</strong> <span id="modalCategory"></span></p>
            <p><strong>🔢 Mã ISBN:</strong> <span id="modalIsbn"></span></p>
            <p><strong>✍️ Tác giả:</strong> <span id="modalAuthor"></span></p>
            <p><strong>🏢 Nhà xuất bản:</strong> <span id="modalPublisher"></span></p>
            <p><strong>📅 Năm xuất bản:</strong> <span id="modalYear"></span></p>
            <p><strong>📝 Mô tả chi tiết:</strong><br><span id="modalDesc" style="color: var(--text-muted);">Đang cập nhật...</span></p>
            
            <hr style="border: 0; border-top: 1px solid var(--border); margin: 15px 0;">
            
            <p><strong>📚 Danh sách bản sao (2/3 bản trống):</strong></p>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 1px solid var(--border);">
                        <th style="padding: 8px; text-align: left;">Mã bản sao</th>
                        <th style="padding: 8px; text-align: center;">Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="modalCopiesList">
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px;">BS0101</td>
                        <td style="padding: 8px; text-align: center;"><span style="background: #e1f5fe; color: #0288d1; padding: 3px 8px; border-radius: 4px; font-weight: 500;">con_trong</span></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 8px;">BS0102</td>
                        <td style="padding: 8px; text-align: center;"><span style="background: #ffebee; color: #c62828; padding: 3px 8px; border-radius: 4px; font-weight: 500;">dang_muon</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <button onclick="closeDetailModal()" style="background: var(--primary, #4f46e5); color: white; border: none; padding: 8px 20px; border-radius: 6px; font-weight: 500; cursor: pointer;">Đóng</button>
        </div>
    </div>
</div>

<script>
function openDetailModal(book) {
    document.getElementById('modalTitle').innerText = book.ten_sach || 'Chi tiết sách';
    document.getElementById('modalBookName').innerText = book.ten_sach || 'N/A';
    document.getElementById('modalCategory').innerText = book.danh_muc_id || 'N/A';
    document.getElementById('modalIsbn').innerText = book.isbn || 'Chưa có';
    document.getElementById('modalAuthor').innerText = book.tac_gia || 'Chưa cập nhật';
    document.getElementById('modalPublisher').innerText = book.nha_xuat_ban || 'Chưa cập nhật';
    document.getElementById('modalYear').innerText = book.nam_xuat_ban || 'Chưa cập nhật';
    document.getElementById('modalDesc').innerText = book.mo_ta || 'Chưa có mô tả chi tiết.';

    document.getElementById('bookDetailModal').style.display = 'flex';
}

function closeDetailModal() {
    document.getElementById('bookDetailModal').style.display = 'none';
}
</script>

<?php 
if (function_exists('view')) {
    view('layouts/footer');
} else {
    $footerPath = __DIR__ . '/../layouts/footer.php';
    if (file_exists($footerPath)) require_once $footerPath;
}
?>