
<?php
$statusLabels = [
    'dang_muon' => 'Đang mượn',
    'qua_han' => 'Quá hạn',
    'da_tra' => 'Đã trả',
];

$loans = $loans ?? [];
$totalLoans = (int) ($totalLoans ?? count($loans));
$perPage = max(1, (int) ($perPage ?? 10));
$currentPage = max(1, (int) ($currentPage ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));
$keyword = (string) ($keyword ?? '');
$selectedStatus = (string) ($selectedStatus ?? '');

$stats = $stats ?? [
    'total' => $totalLoans,
    'borrowing' => 0,
    'overdue' => 0,
    'returned' => 0,
];

$from = $totalLoans > 0
    ? (($currentPage - 1) * $perPage + 1)
    : 0;

$to = min($currentPage * $perPage, $totalLoans);

$queryUrl = static function (int $page) use (
    $keyword,
    $selectedStatus
): string {
    $params = ['page' => $page];

    if ($keyword !== '') {
        $params['q'] = $keyword;
    }

    if ($selectedStatus !== '') {
        $params['status'] = $selectedStatus;
    }

    return url('loans/history') . '?' . http_build_query($params);
};
?>

<style>
/* ===== LỊCH SỬ MƯỢN SÁCH ===== */

.history-page,
.history-page * {
    box-sizing: border-box;
}

.history-page {
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 28px 22px 42px;
    color: #3f2a1e;
}

/* Tiêu đề */

.history-heading {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 25px;
}

.history-heading-icon {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: center;
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: #ead9c7;
    font-size: 27px;
}

.history-eyebrow {
    margin-bottom: 4px;
    color: #8b5e3c;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}

.history-heading h2 {
    margin: 0 0 5px;
    color: #3f2a1e;
    font-size: clamp(23px, 3vw, 30px);
    font-weight: 800;
    line-height: 1.3;
}

.history-heading p {
    margin: 0;
    color: #7a6a5a;
    font-size: 14px;
    line-height: 1.6;
}

/* Thống kê */

.history-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.history-stat {
    position: relative;
    overflow: hidden;
    padding: 20px;
    border: 1px solid #e8dfd6;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 14px rgba(63, 42, 30, 0.045);
}

.history-stat::after {
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: #8b5e3c;
    content: "";
}

.history-stat.borrowing::after {
    background: #4678a8;
}

.history-stat.overdue::after {
    background: #bd5b4c;
}

.history-stat.returned::after {
    background: #43835e;
}

.history-stat-label {
    margin-bottom: 12px;
    color: #7a6a5a;
    font-size: 12px;
    font-weight: 650;
}

.history-stat-number {
    font-size: 30px;
    font-weight: 800;
    line-height: 1.2;
}

.history-stat.total .history-stat-number {
    color: #76502e;
}

.history-stat.borrowing .history-stat-number {
    color: #285c91;
}

.history-stat.overdue .history-stat-number {
    color: #a13e30;
}

.history-stat.returned .history-stat-number {
    color: #276747;
}

/* Khung bảng */

.history-section {
    overflow: hidden;
    border: 1px solid #e8dfd6;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 5px 20px rgba(63, 42, 30, 0.05);
}

.history-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 21px 23px;
    border-bottom: 1px solid #e8dfd6;
    background: #fffdfb;
}

.history-toolbar h3 {
    margin: 0;
    color: #3f2a1e;
    font-size: 17px;
    font-weight: 800;
}

.history-toolbar p {
    margin: 5px 0 0;
    color: #8b7c6c;
    font-size: 12px;
}

.history-count {
    padding: 8px 12px;
    border: 1px solid #e8d8c5;
    border-radius: 20px;
    background: #f5ede6;
    color: #76502e;
    font-size: 12px;
    font-weight: 750;
    white-space: nowrap;
}

/* Tìm kiếm và lọc */

.history-filters {
    display: grid;
    grid-template-columns: minmax(180px, 1fr) 200px auto auto;
    gap: 10px;
    padding: 18px 23px;
    border-bottom: 1px solid #eee7df;
}

.history-filters input,
.history-filters select {
    width: 100%;
    min-width: 0;
    min-height: 42px;
    padding: 10px 12px;
    border: 1px solid #dfd3c6;
    border-radius: 8px;
    background: #fff;
    color: #3f2a1e;
    font-family: inherit;
    font-size: 13px;
}

.history-filters input:focus,
.history-filters select:focus {
    outline: none;
    border-color: #8b5e3c;
    box-shadow: 0 0 0 3px rgba(139, 94, 60, 0.12);
}

.history-filter-btn,
.history-clear-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 42px;
    padding: 10px 15px;
    border-radius: 8px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 750;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: background 0.2s ease;
}

.history-filter-btn {
    border: 1px solid #76502e;
    background: #76502e;
    color: #fff;
}

.history-filter-btn:hover {
    background: #5e3e25;
}

.history-clear-btn {
    border: 1px solid #e1d6ca;
    background: #fff;
    color: #634327;
}

.history-clear-btn:hover {
    background: #f5ede6;
}

/* Bảng */

.history-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    text-align: left;
    font-size: 13px;
}

.history-table th {
    padding: 14px;
    background: #f8f3ed;
    color: #76502e;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.2px;
    white-space: nowrap;
}

.history-table td {
    padding: 15px 14px;
    border-top: 1px solid #eee7df;
    vertical-align: middle;
}

.history-table tbody tr {
    transition: background 0.15s ease;
}

.history-table tbody tr:hover {
    background: #fdf9f4;
}

.history-book {
    min-width: 145px;
    color: #3f2a1e;
    font-weight: 750;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.history-muted {
    margin-top: 4px;
    color: #8b7c6c;
    font-size: 11px;
    overflow-wrap: anywhere;
}

/* Nhãn trạng thái */

.history-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 750;
    white-space: nowrap;
}

.history-status.dang_muon {
    background: #eaf2fa;
    color: #285c91;
}

.history-status.qua_han {
    background: #faeae6;
    color: #a13e30;
}

.history-status.da_tra {
    background: #e7f3e9;
    color: #276747;
}

.history-money {
    color: #3f2a1e;
    font-weight: 750;
    white-space: nowrap;
}

.history-money.is-overdue {
    color: #a13e30;
}

/* Không có kết quả */

.history-empty {
    padding: 50px 20px;
    text-align: center;
}

.history-empty-icon {
    margin-bottom: 12px;
    font-size: 34px;
}

.history-empty strong {
    display: block;
    margin-bottom: 7px;
    color: #3f2a1e;
}

.history-empty p {
    color: #8b7c6c;
    font-size: 12px;
}

/* Phân trang */

.history-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
    padding: 17px 23px;
    border-top: 1px solid #e8dfd6;
    background: #fffdfb;
}

.history-pagination-info {
    color: #817365;
    font-size: 12px;
}

.history-pagination-info strong {
    color: #3f2a1e;
}

.history-pagination-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}

.history-page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border: 1px solid #e7ddd2;
    border-radius: 8px;
    background: #fff;
    color: #634327;
    font-size: 12px;
    font-weight: 650;
    text-decoration: none;
    transition: all 0.2s ease;
}

.history-page-link:hover {
    border-color: #8b5e3c;
    background: #f5ede6;
}

.history-page-link.active {
    border-color: #76502e;
    background: #76502e;
    color: #fff;
}

/* Responsive */

@media (max-width: 1000px) {
    .history-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .history-filters {
        grid-template-columns: minmax(0, 1fr) 180px;
    }
}

@media (max-width: 650px) {
    .history-page {
        padding: 20px 12px 30px;
    }

    .history-heading {
        align-items: flex-start;
        gap: 12px;
    }

    .history-heading-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        font-size: 22px;
    }

    .history-heading p {
        font-size: 13px;
    }

    .history-toolbar {
        align-items: flex-start;
        padding: 16px;
    }

    .history-toolbar p {
        line-height: 1.6;
    }

    .history-count {
        padding: 7px 9px;
        font-size: 11px;
    }

    .history-filters {
        grid-template-columns: minmax(0, 1fr);
        padding: 14px;
    }

    .history-filter-btn,
    .history-clear-btn {
        width: 100%;
    }

    .history-pagination {
        align-items: flex-start;
        flex-direction: column;
        padding: 15px;
    }

    .history-table {
        min-width: 950px;
    }
}

@media (max-width: 380px) {
    .history-stats {
        grid-template-columns: minmax(0, 1fr);
    }
}
</style>

<div class="history-page">
    <header class="history-heading">
        <div class="history-heading-icon" aria-hidden="true">📚</div>
        <div>
            <h2>Lịch sử mượn sách</h2>
            <p>Theo dõi phiếu mượn, hạn trả và tình trạng mượn sách của độc giả.</p>
        </div>
    </header>

    <div class="history-stats">
        <div class="history-stat total">
            <div class="history-stat-label">Tổng số phiếu</div>
            <div class="history-stat-number">
                <?= number_format((int) ($stats['total'] ?? 0)) ?>
            </div>
        </div>

        <div class="history-stat borrowing">
            <div class="history-stat-label">Đang mượn</div>
            <div class="history-stat-number">
                <?= number_format((int) ($stats['borrowing'] ?? 0)) ?>
            </div>
        </div>

        <div class="history-stat overdue">
            <div class="history-stat-label">Quá hạn</div>
            <div class="history-stat-number">
                <?= number_format((int) ($stats['overdue'] ?? 0)) ?>
            </div>
        </div>

        <div class="history-stat returned">
            <div class="history-stat-label">Đã trả</div>
            <div class="history-stat-number">
                <?= number_format((int) ($stats['returned'] ?? 0)) ?>
            </div>
        </div>
    </div>

    <section class="history-section">
        <div class="history-toolbar">
            <div>
                <h3>Lịch sử của tất cả độc giả</h3>
                <p>Tìm kiếm và lọc các phiếu mượn trong hệ thống.</p>
            </div>
            <span class="history-count">
                <?= number_format($totalLoans) ?> kết quả
            </span>
        </div>

        <form
            class="history-filters"
            method="get"
            action="<?= e(url('loans/history')) ?>"
        >
            <input
                type="search"
                name="q"
                value="<?= e($keyword) ?>"
                placeholder="Tên độc giả, tên sách, mã bản sao..."
                aria-label="Tìm lịch sử mượn"
            >

            <select name="status" aria-label="Lọc trạng thái">
                <option value="">Tất cả trạng thái</option>
                <?php foreach ($statusLabels as $value => $label): ?>
                    <option
                        value="<?= e($value) ?>"
                        <?= $selectedStatus === $value ? 'selected' : '' ?>
                    >
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="history-filter-btn" type="submit">
                Tìm kiếm
            </button>

            <a
                class="history-clear-btn"
                href="<?= e(url('loans/history')) ?>"
            >
                Xóa lọc
            </a>
        </form>

        <?php if (empty($loans)): ?>
            <div class="history-empty">
                <div class="history-empty-icon" aria-hidden="true">📖</div>
                <strong>Chưa tìm thấy lịch sử mượn phù hợp</strong>
                <p>Hãy thử từ khóa khác hoặc chọn một trạng thái khác.</p>
            </div>
        <?php else: ?>
            <div class="history-table-wrap">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Mã phiếu</th>
                            <th>Độc giả</th>
                            <th>Sách / Bản sao</th>
                            <th>Ngày mượn</th>
                            <th>Hạn trả</th>
                            <th>Ngày trả</th>
                            <th>Trạng thái</th>
                            <th>Tiền phạt</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <?php
                            $status = (string) ($loan['trang_thai_hien_thi'] ?? '');
                            $isOverdue = $status === 'qua_han';
                            $isReturned = !empty($loan['tra_luc']);
                            ?>

                            <tr>
                                <td>
                                    <strong>
                                        #<?= (int) ($loan['phieu_muon_id'] ?? 0) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e($loan['ten_doc_gia'] ?? 'Chưa có tên') ?>
                                    <div class="history-muted">
                                        Mã độc giả:
                                        <?= (int) ($loan['nguoi_dung_id'] ?? 0) ?>
                                    </div>
                                </td>

                                <td>
                                    <div class="history-book">
                                        <?= e($loan['ten_sach'] ?? 'Chưa có tên sách') ?>
                                    </div>
                                    <div class="history-muted">
                                        Bản sao: <?= e($loan['ma_ban_sao'] ?? '') ?>
                                    </div>
                                </td>

                                <td>
                                    <?= e(format_datetime($loan['muon_luc'] ?? null)) ?>
                                </td>

                                <td>
                                    <?= e(format_date($loan['han_tra'] ?? null)) ?>
                                </td>

                                <td>
                                    <?= $isReturned
                                        ? e(format_datetime($loan['tra_luc']))
                                        : 'Chưa trả' ?>
                                </td>

                                <td>
                                    <span class="history-status <?= e($status) ?>">
                                        <?= e($statusLabels[$status] ?? $status) ?>
                                    </span>
                                </td>

                                <td class="history-money <?= $isOverdue ? 'is-overdue' : '' ?>">
                                    <?= e(format_money($loan['tien_phat'] ?? 0)) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav class="history-pagination" aria-label="Phân trang lịch sử mượn">
                <div class="history-pagination-info">
                    Hiển thị <strong><?= $from ?>–<?= $to ?></strong>
                    trên <strong><?= number_format($totalLoans) ?></strong> phiếu
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="history-pagination-list">
                        <?php if ($currentPage > 1): ?>
                            <a
                                class="history-page-link"
                                href="<?= e($queryUrl($currentPage - 1)) ?>"
                                aria-label="Trang trước"
                            >«</a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

                        for ($page = $startPage; $page <= $endPage; $page++):
                        ?>
                            <a
                                class="history-page-link <?= $page === $currentPage ? 'active' : '' ?>"
                                href="<?= e($queryUrl($page)) ?>"
                                <?= $page === $currentPage ? 'aria-current="page"' : '' ?>
                            ><?= $page ?></a>
                        <?php endfor; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a
                                class="history-page-link"
                                href="<?= e($queryUrl($currentPage + 1)) ?>"
                                aria-label="Trang sau"
                            >»</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </section>
</div>
