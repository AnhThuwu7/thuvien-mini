
<?php
$statusLabels = [
    'dang_muon' => 'Đang mượn',
    'qua_han' => 'Quá hạn',
    'da_tra' => 'Đã trả',
];

$conditionLabels = [
    'tot' => 'Tốt',
    'hong' => 'Hỏng',
    'mat' => 'Mất',
];

$loans = $loans ?? [];
$totalLoans = (int) ($totalLoans ?? count($loans));
$perPage = max(1, (int) ($perPage ?? 5));
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

    return url('loans') . '?' . http_build_query($params);
};
?>

<style>
.loan-page,
.loan-page * {
    box-sizing: border-box;
}

.loan-page {
    --primary: #745238;
    --primary-dark: #543a25;
    --text: #30271f;
    --muted: #817568;
    --border: #ebe3d9;

    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: -15px auto 0;
    padding: 5px 16px 32px;
    color: var(--text);
}

/* Tiêu đề */

.loan-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
}

.loan-heading-main {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.loan-heading-icon {
    display: grid;
    flex: 0 0 46px;
    width: 46px;
    height: 46px;
    place-items: center;
    border: 1px solid #eadbca;
    border-radius: 13px;
    background: #f3e9dc;
    font-size: 23px;
}

.loan-eyebrow {
    margin-bottom: 4px;
    color: #967957;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.4px;
}

.loan-heading h2 {
    margin: 0 0 6px;
    font-size: clamp(21px, 2vw, 29px);
    font-weight: 800;
    letter-spacing: -.5px;
    line-height: 1.3;
}

.loan-heading p {
    margin: 0;
    color: var(--muted);
    font-size: 12px;
    line-height: 1.6;
}

.loan-create-btn,
.loan-primary-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 39px;
    padding: 9px 13px;
    border: 1px solid var(--primary);
    border-radius: 8px;
    background: var(--primary);
    color: #fff;
    font-size: 12px;
    font-weight: 750;
    text-decoration: none;
    cursor: pointer;
    transition: background .2s;
}

.loan-create-btn {
    flex-shrink: 0;
}

.loan-create-btn:hover,
.loan-primary-btn:hover {
    border-color: var(--primary-dark);
    background: var(--primary-dark);
    color: #fff;
}

/* Thống kê */

.loan-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 13px;
    margin-bottom: 22px;
}

.loan-stat {
    min-width: 0;
    padding: 15px;
    border: 1px solid var(--border);
    border-radius: 11px;
    background: #fff;
    box-shadow: 0 3px 12px #39291a06;
}

.loan-stat-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 7px;
    margin-bottom: 12px;
}

.loan-stat-label {
    color: var(--muted);
    font-size: 12px;
    font-weight: 650;
}

.loan-stat-icon {
    display: grid;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    place-items: center;
    border-radius: 9px;
    font-size: 16px;
}

.loan-stat-number {
    font-size: 28px;
    font-weight: 850;
    line-height: 1.2;
    font-variant-numeric: tabular-nums;
}

.loan-stat-note {
    margin-top: 5px;
    color: #958a7e;
    font-size: 10px;
}

.loan-stat.total .loan-stat-icon,
.loan-stat.total .loan-stat-number {
    color: #795737;
}
.loan-stat.total .loan-stat-icon {
    background: #f3e9dc;
}

.loan-stat.borrowing .loan-stat-icon,
.loan-stat.borrowing .loan-stat-number {
    color: #35689c;
}
.loan-stat.borrowing .loan-stat-icon {
    background: #e9f1fb;
}

.loan-stat.overdue .loan-stat-icon,
.loan-stat.overdue .loan-stat-number {
    color: #b34a3d;
}
.loan-stat.overdue .loan-stat-icon {
    background: #fbece8;
}

.loan-stat.returned .loan-stat-icon,
.loan-stat.returned .loan-stat-number {
    color: #34754c;
}
.loan-stat.returned .loan-stat-icon {
    background: #e8f4eb;
}

/* Khu vực danh sách */

.loan-section {
    width: 100%;
    min-width: 0;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 4px 18px #39291a06;
}

.loan-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 17px 18px;
    border-bottom: 1px solid var(--border);
    background: #fdfbf8;
}

.loan-toolbar h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
}

.loan-toolbar p {
    margin: 5px 0 0;
    color: var(--muted);
    font-size: 11px;
}

.loan-count {
    flex-shrink: 0;
    padding: 6px 9px;
    border: 1px solid #e9dccb;
    border-radius: 7px;
    background: #f5ede3;
    color: #765536;
    font-size: 10px;
    font-weight: 800;
    white-space: nowrap;
}

/* Tìm kiếm và lọc */

.loan-filters {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 170px auto auto;
    align-items: center;
    gap: 9px;
    padding: 14px 18px;
    border-bottom: 1px solid #f0eae3;
}

.loan-search-wrap {
    position: relative;
    min-width: 0;
}

.loan-search-icon {
    position: absolute;
    top: 50%;
    left: 11px;
    color: #988979;
    pointer-events: none;
    transform: translateY(-50%);
}

.loan-filters input,
.loan-filters select {
    width: 100%;
    min-width: 0;
    height: 38px;
    padding: 8px 10px;
    border: 1px solid #e4dbd0;
    border-radius: 7px;
    outline: none;
    background: #fff;
    color: var(--text);
    font: inherit;
    font-size: 11px;
}

.loan-filters input {
    padding-left: 31px;
}

.loan-filters input:focus,
.loan-filters select:focus,
.loan-field input:focus,
.loan-field select:focus {
    border-color: #a88a69;
    box-shadow: 0 0 0 3px #a88a691a;
}

.loan-filter-btn,
.loan-clear-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 38px;
    padding: 8px 11px;
    border: 1px solid;
    border-radius: 7px;
    font-size: 11px;
    font-weight: 750;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
}

.loan-filter-btn {
    border-color: var(--primary);
    background: var(--primary);
    color: #fff;
}

.loan-filter-btn:hover {
    background: var(--primary-dark);
    color: #fff;
}

.loan-clear-btn {
    border-color: #e4dbd0;
    background: #fff;
    color: #6d5741;
}

.loan-clear-btn:hover {
    background: #f8f3ed;
}

/* Bảng vừa khít màn hình */

.loan-table-wrap {
    width: 100%;
    min-width: 0;
    overflow: visible;
}

.loan-table {
    width: 100%;
    min-width: 0;
    table-layout: fixed;
    border-collapse: collapse;
    text-align: left;
}

.loan-table thead th {
    padding: 12px 7px;
    border-bottom: 1px solid #e9e0d6;
    background: #f7f3ee;
    color: #766959;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .1px;
    line-height: 1.45;
    overflow-wrap: anywhere;
}

.loan-table tbody td {
    padding: 13px 7px;
    border-bottom: 1px solid #f0eae3;
    color: #4b4035;
    font-size: 11px;
    vertical-align: middle;
    overflow-wrap: anywhere;
    word-break: normal;
}

.loan-table tbody tr:last-child td {
    border-bottom: 0;
}

.loan-table tbody tr {
    transition: background .15s;
}

.loan-table tbody tr:hover {
    background: #fdfaf6;
}

/* Chia tỷ lệ 9 cột */

.loan-table .col-id {
    width: 8%;
}

.loan-table .col-reader {
    width: 12%;
}

.loan-table .col-book {
    width: 15%;
}

.loan-table .col-date {
    width: 10%;
}

.loan-table .col-status {
    width: 11%;
}

.loan-table .col-money {
    width: 10%;
    text-align: right;
}

.loan-table .col-action {
    width: 14%;
    text-align: center;
}

.loan-id {
    display: block;
    color: #795737;
    font-size: 11px;
    font-weight: 850;
    overflow-wrap: anywhere;
}

.loan-cell-primary {
    display: block;
    color: #30271f;
    font-weight: 750;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.loan-cell-secondary {
    display: block;
    margin-top: 4px;
    color: #928577;
    font-size: 10px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.loan-date {
    display: block;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.loan-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    max-width: 100%;
    padding: 5px 6px;
    border: 1px solid transparent;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 800;
    line-height: 1.4;
    white-space: normal;
    overflow-wrap: anywhere;
}

.loan-status-dot {
    flex: 0 0 6px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.loan-status.dang_muon {
    border-color: #d9e6f6;
    background: #edf4fc;
    color: #35689c;
}

.loan-status.qua_han {
    border-color: #f3d8d1;
    background: #fdf0ed;
    color: #b34a3d;
}

.loan-status.da_tra {
    border-color: #d5e9d9;
    background: #edf7ef;
    color: #34754c;
}

.loan-money {
    color: #514234;
    font-weight: 800;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.loan-money.is-overdue {
    color: #b34a3d;
}

.loan-money.is-unpaid {
    color: #9a6532;
}

.loan-action-cell {
    text-align: center;
}

.loan-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    max-width: 100%;
    min-height: 31px;
    padding: 6px 7px;
    border: 1px solid #d9c6b0;
    border-radius: 6px;
    background: #f7efe5;
    color: #704d2d;
    font-size: 10px;
    font-weight: 800;
    line-height: 1.4;
    cursor: pointer;
    white-space: normal;
    overflow-wrap: anywhere;
}

.loan-action-btn:hover {
    border-color: #b79b7a;
    background: #eee0ce;
}

.loan-action-btn:disabled {
    border-color: #d5e9d9;
    background: #edf7ef;
    color: #34754c;
    cursor: default;
}

/* Không có dữ liệu */

.loan-empty {
    padding: 42px 18px;
    text-align: center;
}

.loan-empty-icon {
    display: grid;
    width: 50px;
    height: 50px;
    margin: 0 auto 12px;
    place-items: center;
    border-radius: 14px;
    background: #f6eee4;
    color: #795737;
    font-size: 23px;
}

.loan-empty strong {
    display: block;
    margin-bottom: 7px;
    font-size: 13px;
}

.loan-empty p {
    margin: 0;
    color: #918577;
    font-size: 11px;
    line-height: 1.7;
}

/* Phân trang */

.loan-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 14px 18px;
    border-top: 1px solid var(--border);
    background: #fdfbf8;
}

.loan-pagination-info {
    color: var(--muted);
    font-size: 11px;
    line-height: 1.8;
}

.loan-pagination-info strong {
    color: #514234;
}

.loan-pagination-list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}

.loan-page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 31px;
    height: 31px;
    padding: 0 7px;
    border: 1px solid #e7ddd2;
    border-radius: 6px;
    background: #fff;
    color: #6c523a;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
}

.loan-page-link:hover {
    background: #f5ede3;
}

.loan-page-link.active {
    border-color: var(--primary);
    background: var(--primary);
    color: #fff;
}

.loan-pagination-dots {
    padding: 0 2px;
    color: #a39788;
}

/* Hộp thoại trả sách */

.loan-modal[hidden] {
    display: none !important;
}

.loan-modal {
    position: fixed;
    z-index: 9999;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.loan-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgb(35 27 20 / 52%);
    backdrop-filter: blur(3px);
}

.loan-modal-dialog {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 440px;
    max-height: 90vh;
    overflow-y: auto;
    border: 1px solid #e9dfd4;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 22px 70px #0003;
    animation: loan-modal-in .18s ease-out;
}

@keyframes loan-modal-in {
    from {
        opacity: 0;
        transform: translateY(8px) scale(.985);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.loan-modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
    padding: 18px;
    border-bottom: 1px solid #eee5da;
    background: #fdfbf8;
}

.loan-modal-title-wrap {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    min-width: 0;
}

.loan-modal-icon {
    display: grid;
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    place-items: center;
    border-radius: 10px;
    background: #f3e9dc;
    color: #795737;
    font-size: 18px;
}

.loan-modal-header h3 {
    margin: 1px 0 5px;
    color: #30271f;
    font-size: 15px;
    font-weight: 850;
}

.loan-modal-header p {
    margin: 0;
    color: #817568;
    font-size: 11px;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.loan-modal-close {
    display: grid;
    flex: 0 0 30px;
    width: 30px;
    height: 30px;
    place-items: center;
    border: 1px solid #e6ddd2;
    border-radius: 7px;
    background: #fff;
    color: #6d5b49;
    font-size: 19px;
    cursor: pointer;
}

.loan-modal-close:hover {
    background: #f4ece2;
}

.loan-modal-form {
    display: grid;
    gap: 14px;
    padding: 18px;
}

.loan-modal-info {
    display: grid;
    gap: 5px;
    padding: 11px;
    border: 1px solid #eadfce;
    border-radius: 8px;
    background: #fbf6ef;
    color: #765536;
    font-size: 11px;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.loan-modal-info strong {
    color: #513b25;
}

.loan-field {
    display: grid;
    gap: 6px;
    min-width: 0;
}

.loan-field label {
    color: #625447;
    font-size: 11px;
    font-weight: 750;
}

.loan-field input,
.loan-field select {
    width: 100%;
    min-width: 0;
    height: 39px;
    padding: 8px 10px;
    border: 1px solid #e2d8cc;
    border-radius: 7px;
    outline: none;
    background: #fff;
    color: #40352b;
    font: inherit;
    font-size: 12px;
}

.loan-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding-top: 3px;
}

.loan-secondary-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 39px;
    padding: 8px 12px;
    border: 1px solid #e4dbd0;
    border-radius: 7px;
    background: #fff;
    color: #6d5741;
    font-size: 11px;
    font-weight: 750;
    cursor: pointer;
}

.loan-secondary-btn:hover {
    background: #f8f3ed;
}

.loan-page button:focus-visible,
.loan-page a:focus-visible,
.loan-modal button:focus-visible {
    outline: 3px solid #c9ae8c;
    outline-offset: 2px;
}

/* Màn hình vừa */

@media (max-width: 1100px) {
    .loan-page {
        padding-right: 12px;
        padding-left: 12px;
    }

    .loan-stats {
        gap: 9px;
    }

    .loan-stat {
        padding: 12px;
    }

    .loan-filters {
        grid-template-columns: minmax(0, 1fr) 145px auto auto;
        gap: 6px;
        padding: 12px;
    }

    .loan-filter-btn,
    .loan-clear-btn {
        padding-right: 8px;
        padding-left: 8px;
    }

    .loan-table thead th,
    .loan-table tbody td {
        padding-right: 4px;
        padding-left: 4px;
    }

    .loan-table thead th {
        font-size: 9px;
    }

    .loan-table tbody td {
        font-size: 10px;
    }

    .loan-status {
        gap: 3px;
        padding: 4px;
        font-size: 8px;
    }

    .loan-action-btn {
        padding: 5px 4px;
        font-size: 9px;
    }
}

/* Máy tính bảng */

@media (max-width: 760px) {
    .loan-page {
        padding: 16px 10px 24px;
    }

    .loan-heading {
        align-items: flex-start;
        flex-direction: column;
    }

    .loan-create-btn {
        width: 100%;
    }

    .loan-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px;
    }

    .loan-stat {
        padding: 12px;
    }

    .loan-stat-number {
        font-size: 25px;
    }

    .loan-stat-note {
        font-size: 9px;
    }

    .loan-filters {
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 8px;
        padding: 12px;
    }

    .loan-search-wrap {
        grid-column: 1 / -1;
    }

    .loan-filter-btn,
    .loan-clear-btn {
        width: 100%;
    }

    .loan-table thead th {
        padding: 7px 3px;
        font-size: 8px;
        letter-spacing: 0;
    }

    .loan-table tbody td {
        padding: 8px 3px;
        font-size: 9px;
    }

    .loan-table .col-id {
        width: 7%;
    }

    .loan-table .col-reader {
        width: 11%;
    }

    .loan-table .col-book {
        width: 14%;
    }

    .loan-table .col-date {
        width: 10%;
    }

    .loan-table .col-status {
        width: 12%;
    }

    .loan-table .col-money {
        width: 10%;
    }

    .loan-table .col-action {
        width: 16%;
    }

    .loan-id {
        font-size: 9px;
    }

    .loan-cell-secondary {
        font-size: 8px;
    }

    .loan-status {
        display: inline-flex;
        gap: 3px;
        padding: 3px;
        font-size: 7px;
    }

    .loan-status-dot {
        flex-basis: 4px;
        width: 4px;
        height: 4px;
    }

    .loan-action-btn {
        padding: 4px 3px;
        font-size: 8px;
    }

    .loan-pagination {
        padding: 12px;
    }
}

/* Điện thoại */

@media (max-width: 480px) {
    .loan-page {
        padding: 13px 7px 20px;
    }

    .loan-heading-icon {
        flex-basis: 40px;
        width: 40px;
        height: 40px;
    }

    .loan-heading h2 {
        font-size: 21px;
    }

    .loan-heading p {
        font-size: 11px;
    }

    .loan-toolbar {
        padding: 13px 10px;
    }

    .loan-toolbar h3 {
        font-size: 13px;
    }

    .loan-toolbar p {
        font-size: 10px;
    }

    .loan-count {
        padding: 5px;
        font-size: 9px;
    }

    .loan-table thead th {
        padding: 6px 2px;
        font-size: 7px;
    }

    .loan-table tbody td {
        padding: 7px 2px;
        font-size: 8px;
    }

    .loan-cell-primary,
    .loan-date,
    .loan-money {
        line-height: 1.4;
    }

    .loan-cell-secondary {
        font-size: 7px;
    }

    .loan-status {
        font-size: 7px;
        padding: 2px;
    }

    .loan-action-btn {
        min-height: 26px;
        padding: 3px 2px;
        font-size: 7px;
    }

    .loan-pagination {
        gap: 8px;
        padding: 10px;
    }

    .loan-pagination-info {
        font-size: 10px;
    }

    .loan-modal {
        padding: 10px;
    }

    .loan-modal-header,
    .loan-modal-form {
        padding: 14px;
    }

    .loan-modal-actions {
        flex-direction: column-reverse;
    }

    .loan-modal-actions button {
        width: 100%;
    }
}
</style>

<div class="loan-page">
    <header class="loan-heading">
        <div class="loan-heading-main">
            <div class="loan-heading-icon" aria-hidden="true">📚</div>
            <div>
                <h2>Quản lý mượn – trả sách</h2>
                <p>Theo dõi phiếu mượn, hạn trả và tình trạng sách.</p>
            </div>
        </div>

        <a class="loan-create-btn" href="<?= e(url('loans/create')) ?>">
            <span aria-hidden="true">＋</span>
            Lập phiếu mượn mới
        </a>
    </header>

    <section class="loan-stats" aria-label="Thống kê mượn trả">
        <article class="loan-stat total">
            <div class="loan-stat-top">
                <div class="loan-stat-label">Tổng số phiếu</div>
                <div class="loan-stat-icon" aria-hidden="true">▤</div>
            </div>
            <div class="loan-stat-number">
                <?= number_format((int) $stats['total']) ?>
            </div>
            <div class="loan-stat-note">Toàn bộ phiếu mượn</div>
        </article>

        <article class="loan-stat borrowing">
            <div class="loan-stat-top">
                <div class="loan-stat-label">Đang mượn</div>
                <div class="loan-stat-icon" aria-hidden="true">◷</div>
            </div>
            <div class="loan-stat-number">
                <?= number_format((int) $stats['borrowing']) ?>
            </div>
            <div class="loan-stat-note">Sách đang được mượn</div>
        </article>

        <article class="loan-stat overdue">
            <div class="loan-stat-top">
                <div class="loan-stat-label">Quá hạn</div>
                <div class="loan-stat-icon" aria-hidden="true">!</div>
            </div>
            <div class="loan-stat-number">
                <?= number_format((int) $stats['overdue']) ?>
            </div>
            <div class="loan-stat-note">Cần kiểm tra và xử lý</div>
        </article>

        <article class="loan-stat returned">
            <div class="loan-stat-top">
                <div class="loan-stat-label">Đã trả</div>
                <div class="loan-stat-icon" aria-hidden="true">✓</div>
            </div>
            <div class="loan-stat-number">
                <?= number_format((int) $stats['returned']) ?>
            </div>
            <div class="loan-stat-note">Đã hoàn tất trả sách</div>
        </article>
    </section>

    <section class="loan-section">
        <div class="loan-toolbar">
            <div>
                <h3>Danh sách phiếu mượn</h3>
                <p>Tra cứu thông tin và xử lý trả sách</p>
            </div>
            <span class="loan-count">
                <?= number_format($totalLoans) ?> kết quả
            </span>
        </div>

        <form
            class="loan-filters"
            method="get"
            action="<?= e(url('loans')) ?>"
        >
            <div class="loan-search-wrap">
                <span class="loan-search-icon" aria-hidden="true">⌕</span>
                <input
                    type="search"
                    name="q"
                    value="<?= e($keyword) ?>"
                    placeholder="Tìm độc giả, sách, mã phiếu..."
                    aria-label="Tìm kiếm phiếu mượn"
                >
            </div>

            <select name="status" aria-label="Lọc theo trạng thái">
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

            <button class="loan-filter-btn" type="submit">
                Tìm / Lọc
            </button>

            <a class="loan-clear-btn" href="<?= e(url('loans')) ?>">
                Xóa lọc
            </a>
        </form>

        <?php if (empty($loans)): ?>
            <div class="loan-empty">
                <div class="loan-empty-icon" aria-hidden="true">⌕</div>
                <strong>Không tìm thấy phiếu mượn phù hợp</strong>
                <p>Thử thay đổi từ khóa hoặc chọn trạng thái khác.</p>
            </div>
        <?php else: ?>
            <div class="loan-table-wrap">
                <table class="loan-table">
                    <thead>
                        <tr>
                            <th class="col-id">Mã phiếu</th>
                            <th class="col-reader">Độc giả</th>
                            <th class="col-book">Tên sách / Bản sao</th>
                            <th class="col-date">Ngày mượn</th>
                            <th class="col-date">Hạn trả</th>
                            <th class="col-date">Ngày trả</th>
                            <th class="col-status">Trạng thái</th>
                            <th class="col-money">Tiền phạt</th>
                            <th class="col-action">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($loans as $loan): ?>
                            <?php
                            $status = (string) (
                                $loan['trang_thai_hien_thi'] ?? ''
                            );

                            $isReturned = !empty($loan['tra_luc']);
                            $isOverdue = $status === 'qua_han';
                            $loanId = (int) ($loan['phieu_muon_id'] ?? 0);
                            $fine = (float) ($loan['tien_phat'] ?? 0);
                            // Gợi ý phạt quá hạn (chưa trả)
                            $suggestedFine = $fine;
                            if (empty($loan['tra_luc']) && !empty($loan['han_tra'])) {
                                $han = new DateTimeImmutable(substr((string) $loan['han_tra'], 0, 10));
                                $today = new DateTimeImmutable('today');
                                $daysOver = (int) $han->diff($today)->format('%r%a');
                                if ($daysOver > 0) {
                                    $rate = (float) ($finePerDay ?? 2000);
                                    $suggestedFine = round($daysOver * $rate, 2);
                                }
                            }
                            ?>

                            <tr>
                                <td>
                                    <span class="loan-id">
                                        #<?= $loanId ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loan-cell-primary">
                                        <?= e($loan['ten_doc_gia'] ?? '—') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loan-cell-primary">
                                        <?= e($loan['ten_sach'] ?? 'Chưa có tên sách') ?>
                                    </span>
                                    <span class="loan-cell-secondary">
                                        Bản sao:
                                        <?= e($loan['ma_ban_sao'] ?? '—') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loan-date">
                                        <?= e(format_datetime($loan['muon_luc'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loan-date <?= $isOverdue ? 'loan-money is-overdue' : '' ?>">
                                        <?= e(format_date($loan['han_tra'])) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loan-date">
                                        <?= $isReturned
                                            ? e(format_datetime($loan['tra_luc']))
                                            : '—' ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="loan-status <?= e($status) ?>">
                                        <span class="loan-status-dot" aria-hidden="true"></span>
                                        <?= e($statusLabels[$status] ?? $status) ?>
                                    </span>
                                </td>

                                <td class="col-money">
                                    <span class="loan-money <?= $isOverdue ? 'is-overdue' : ($fine > 0 ? 'is-unpaid' : '') ?>">
                                        <?= e(format_money($fine)) ?>
                                    </span>
                                </td>

                                <td class="loan-action-cell">
                                    <?php if ($isReturned): ?>
                                        <button
                                            class="loan-action-btn"
                                            type="button"
                                            disabled
                                        >
                                            ✓ Đã trả
                                        </button>
                                    <?php else: ?>
                                        <button
                                            class="loan-action-btn"
                                            type="button"
                                            data-open-loan-modal
                                            data-loan-id="<?= $loanId ?>"
                                            data-book="<?= e($loan['ten_sach'] ?? 'Chưa có tên sách') ?>"
                                            data-reader="<?= e($loan['ten_doc_gia'] ?? '') ?>"
                                            data-fine="<?= e((string) (int) $suggestedFine) ?>"
                                            data-days-overdue="<?= (int) (empty($loan['tra_luc']) && !empty($loan['han_tra']) ? max(0, (int) (new DateTimeImmutable(substr((string)$loan['han_tra'],0,10)))->diff(new DateTimeImmutable('today'))->format('%r%a')) : 0) ?>"
                                        >
                                            ↩ Trả sách
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav class="loan-pagination" aria-label="Phân trang phiếu mượn">
                <div class="loan-pagination-info">
                    Hiển thị
                    <strong><?= $from ?>–<?= $to ?></strong>
                    trên
                    <strong><?= number_format($totalLoans) ?></strong>
                    phiếu
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="loan-pagination-list">
                        <?php if ($currentPage > 1): ?>
                            <a
                                class="loan-page-link"
                                href="<?= e($queryUrl($currentPage - 1)) ?>"
                                aria-label="Trang trước"
                            >«</a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);

                        if ($startPage > 1) {
                            echo '<a class="loan-page-link" href="'
                                . e($queryUrl(1)) . '">1</a>';

                            if ($startPage > 2) {
                                echo '<span class="loan-pagination-dots">…</span>';
                            }
                        }

                        for ($page = $startPage; $page <= $endPage; $page++):
                        ?>
                            <a
                                class="loan-page-link <?= $page === $currentPage ? 'active' : '' ?>"
                                href="<?= e($queryUrl($page)) ?>"
                                <?= $page === $currentPage
                                    ? 'aria-current="page"'
                                    : '' ?>
                            >
                                <?= $page ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($endPage < $totalPages): ?>
                            <?php
                            if ($endPage < $totalPages - 1) {
                                echo '<span class="loan-pagination-dots">…</span>';
                            }
                            ?>

                            <a
                                class="loan-page-link"
                                href="<?= e($queryUrl($totalPages)) ?>"
                            >
                                <?= $totalPages ?>
                            </a>
                        <?php endif; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a
                                class="loan-page-link"
                                href="<?= e($queryUrl($currentPage + 1)) ?>"
                                aria-label="Trang tiếp"
                            >»</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </section>
</div>

<!-- Hộp thoại trả sách -->
<div
    class="loan-modal"
    id="loanReturnModal"
    hidden
    aria-hidden="true"
>
    <div class="loan-modal-backdrop" data-close-loan-modal></div>

    <section
        class="loan-modal-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="loanModalTitle"
    >
        <header class="loan-modal-header">
            <div class="loan-modal-title-wrap">
                <div class="loan-modal-icon" aria-hidden="true">↩</div>
                <div>
                    <h3 id="loanModalTitle">Xác nhận trả sách</h3>
                    <p id="loanModalSubtitle">
                        Kiểm tra thông tin trước khi xác nhận.
                    </p>
                </div>
            </div>

            <button
                class="loan-modal-close"
                type="button"
                aria-label="Đóng hộp thoại"
                data-close-loan-modal
            >×</button>
        </header>

        <form
            class="loan-modal-form"
            id="loanReturnForm"
            method="post"
            action="<?= e(url('loans/returnBook')) ?>"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(Csrf::token()) ?>"
            >

            <input
                type="hidden"
                name="loan_id"
                id="modalLoanId"
                value=""
            >

            <div class="loan-modal-info">
                <div>
                    <strong>Mã phiếu:</strong>
                    <span id="modalLoanCode">—</span>
                </div>
                <div>
                    <strong>Độc giả:</strong>
                    <span id="modalReader">—</span>
                </div>
                <div>
                    <strong>Sách:</strong>
                    <span id="modalBook">—</span>
                </div>
            </div>

            <div class="loan-field">
                <label for="modalCondition">Tình trạng sách</label>
                <select
                    id="modalCondition"
                    name="tinh_trang_tra"
                    required
                >
                    <?php foreach ($conditionLabels as $value => $label): ?>
                        <option value="<?= e($value) ?>">
                            <?= e($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="loan-field">
                <label for="modalFine">Tiền phạt (VNĐ)</label>
                <small id="modalFineHint" style="display:block;color:#7a6a5a;margin:4px 0 8px;">
                    Tự tính: số ngày quá hạn × mức phạt/ngày (Cấu hình hệ thống).
                </small>
                <input
                    id="modalFine"
                    type="number"
                    name="tien_phat"
                    min="0"
                    step="1000"
                    value="0"
                    required
                >
            </div>

            <div class="loan-field">
                <label for="modalNote">Ghi chú</label>
                <input
                    id="modalNote"
                    type="text"
                    name="ghi_chu"
                    maxlength="255"
                    placeholder="Nhập ghi chú nếu có"
                >
            </div>

            <div class="loan-modal-actions">
                <button
                    class="loan-secondary-btn"
                    type="button"
                    data-close-loan-modal
                >
                    Hủy
                </button>

                <button class="loan-primary-btn" type="submit">
                    ✓ Xác nhận trả sách
                </button>
            </div>
        </form>
    </section>
</div>

<script>
(function () {
    const modal = document.getElementById('loanReturnModal');
    const form = document.getElementById('loanReturnForm');

    if (!modal || !form) {
        return;
    }

    const loanIdInput = document.getElementById('modalLoanId');
    const loanCode = document.getElementById('modalLoanCode');
    const reader = document.getElementById('modalReader');
    const book = document.getElementById('modalBook');
    const fine = document.getElementById('modalFine');
    const subtitle = document.getElementById('loanModalSubtitle');
    const condition = document.getElementById('modalCondition');
    const note = document.getElementById('modalNote');

    let lastFocusedElement = null;

    function openModal(button) {
        lastFocusedElement = button;

        const id = button.dataset.loanId || '';
        const bookName = button.dataset.book || '—';
        const readerName = button.dataset.reader || '—';
        const fineValue = button.dataset.fine || '0';

        loanIdInput.value = id;
        loanCode.textContent = '#' + id;
        reader.textContent = readerName;
        book.textContent = bookName;
        fine.value = fineValue;
        const days = button.dataset.daysOverdue || '0';
        const hint = document.getElementById('modalFineHint');
        if (hint) {
            if (parseInt(days, 10) > 0) {
                hint.textContent = 'Quá hạn ' + days + ' ngày → hệ thống gợi ý ' + Number(fineValue).toLocaleString('vi-VN') + ' đ (có thể tăng thêm, không giảm dưới mức này).';
            } else {
                hint.textContent = 'Chưa quá hạn → phạt mặc định 0 đ (có thể nhập thêm nếu hư hỏng).';
            }
        }
        condition.value = 'tot';
        note.value = '';

        subtitle.textContent =
            'Kiểm tra thông tin phiếu mượn #' + id + '.';

        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        condition.focus();
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    }

    document.querySelectorAll('[data-open-loan-modal]').forEach(
        function (button) {
            button.addEventListener('click', function () {
                openModal(button);
            });
        }
    );

    modal.querySelectorAll('[data-close-loan-modal]').forEach(
        function (button) {
            button.addEventListener('click', closeModal);
        }
    );

    document.addEventListener('keydown', function (event) {
        if (modal.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            closeModal();
        }

        if (event.key === 'Tab') {
            const focusable = modal.querySelectorAll(
                'button:not(:disabled), input:not(:disabled), select:not(:disabled), a[href]'
            );

            if (!focusable.length) {
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (
                event.shiftKey &&
                document.activeElement === first
            ) {
                event.preventDefault();
                last.focus();
            } else if (
                !event.shiftKey &&
                document.activeElement === last
            ) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    form.addEventListener('submit', function (event) {
        const id = loanIdInput.value;

        if (!id) {
            event.preventDefault();
            return;
        }

        if (
            !window.confirm(
                'Xác nhận đã nhận trả sách của phiếu #' + id + '?'
            )
        ) {
            event.preventDefault();
        }
    });
})();
</script>