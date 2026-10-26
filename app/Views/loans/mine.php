
<?php
$totalLoans = count($loans);
$returnedLoans = 0;
$borrowingLoans = 0;
$overdueLoans = 0;
$totalFine = 0;

foreach ($loans as $loan) {
    if (($loan['da_tra'] ?? false) == true) {
        $returnedLoans++;
    } elseif (($loan['qua_han'] ?? false) == true) {
        $overdueLoans++;
    } else {
        $borrowingLoans++;
    }

    $totalFine += (float) ($loan['tien_phat'] ?? 0);
}
?>

<style>

.loan-page {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    margin: 0 auto;
    padding: 5px 16px 32px;
}


.loan-page * {
    box-sizing: border-box;
}

/* Tiêu đề */
.loan-heading {
    margin-top: 0;
    padding-top: 0;
    margin-bottom: 22px;
}

.loan-heading h1 {
    margin: 0 0 8px;
    font-size: 29px;
    line-height: 1.35;
    font-weight: 750;
    color: #493629;
}

.loan-heading p {
    margin: 0;
    color: #796b60;
    font-size: 15px;
    line-height: 1.7;
}

/* Bốn ô thống kê */
.loan-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 22px;
}

.loan-stat {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 88px;
    padding: 15px 18px;
    background: #fffdf9;
    border: 1px solid #eadfd1;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(75, 51, 32, .035);
}

.loan-stat-label {
    margin-bottom: 8px;
    color: #78695c;
    font-size: 13px;
    line-height: 1.4;
}

.loan-stat-value {
    color: #513b2b;
    font-size: 25px;
    font-weight: 700;
    line-height: 1.2;
    overflow-wrap: anywhere;
}

.loan-stat-value.money {
    font-size: 20px;
}

/* Khung danh sách */
.loan-panel {
    width: 100%;
    background: #fffdf9;
    border: 1px solid #eadfd1;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 3px 12px rgba(75, 51, 32, .04);
}

.loan-panel-header {
    padding: 20px 24px;
    border-bottom: 1px solid #eee4d8;
}

.loan-panel-header h2 {
    margin: 0 0 6px;
    color: #493629;
    font-size: 21px;
}

.loan-panel-header p {
    margin: 0;
    color: #837366;
    font-size: 14px;
    line-height: 1.6;
}

/* Bảng lịch sử mượn */
.loan-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.loan-table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
    font-size: 14px;
}

.loan-table th {
    padding: 16px 12px;
    background: #f7f0e7;
    color: #5a4738;
    text-align: left;
    font-weight: 700;
    white-space: nowrap;
}

.loan-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #f0e8df;
    vertical-align: middle;
    line-height: 1.5;
}

.loan-table tbody tr:hover {
    background: #fdf8f1;
}

.loan-table tbody tr:last-child td {
    border-bottom: none;
}

.loan-book-title {
    min-width: 130px;
    color: #493629;
    font-weight: 650;
    overflow-wrap: anywhere;
}

.loan-code {
    color: #756557;
    white-space: nowrap;
}

/* Trạng thái */
.loan-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 11px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}

.loan-status.returned {
    color: #246a45;
    background: #e5f5e9;
}

.loan-status.borrowing {
    color: #8a5b12;
    background: #fff0cf;
}

.loan-status.overdue {
    color: #a33333;
    background: #fde7e5;
}

.loan-fine {
    color: #9c4036;
    font-weight: 700;
    white-space: nowrap;
}

/* Chưa có lịch sử */
.loan-empty {
    margin: 24px;
    padding: 32px 20px;
    background: #f8f2e9;
    border-radius: 12px;
    color: #78695c;
    text-align: center;
    font-size: 15px;
    line-height: 1.7;
}

/* Danh sách dạng thẻ trên điện thoại */
.loan-mobile-list {
    display: none;
}

.loan-mobile-card {
    padding: 16px;
    background: #fff;
    border: 1px solid #eadfd1;
    border-radius: 12px;
}

.loan-mobile-card h3 {
    margin: 0 0 12px;
    color: #493629;
    font-size: 16px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.loan-mobile-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    padding: 10px 0;
    border-bottom: 1px solid #f1e9df;
    font-size: 13px;
    line-height: 1.5;
}

.loan-mobile-row:last-child {
    border-bottom: none;
}

.loan-mobile-row span:first-child {
    flex-shrink: 0;
    color: #837366;
}

.loan-mobile-row span:last-child {
    text-align: right;
    overflow-wrap: anywhere;
}

/* Tổng tiền phạt */
.loan-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 18px 24px;
    background: #faf5ed;
    border-top: 1px solid #eee4d8;
    color: #665548;
    font-size: 15px;
}

.loan-total strong {
    color: #9c4036;
    font-size: 19px;
    white-space: nowrap;
}

/* Máy tính bảng và màn hình vừa */
@media (max-width: 1100px) {
    
.loan-page {
    margin-top: -25px;
    padding: 0 12px 28px;
}

    .loan-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .loan-stat {
        min-height: 88px;
        padding: 15px;
    }
}

/* Điện thoại */
@media (max-width: 600px) {
    
.loan-page {
    margin-top: -15px;
    padding: 0 14px 24px;
}


    .loan-heading {
        margin-bottom: 18px;
    }

    .loan-heading h1 {
        font-size: 25px;
    }

    .loan-heading p {
        font-size: 14px;
    }

    .loan-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 18px;
    }

    .loan-stat {
        min-height: 82px;
        padding: 12px 14px;
        border-radius: 10px;
    }

    .loan-stat-label {
        margin-bottom: 7px;
        font-size: 12px;
    }

    .loan-stat-value {
        font-size: 23px;
    }

    .loan-stat-value.money {
        font-size: 18px;
    }

    .loan-panel-header {
        padding: 17px;
    }

    .loan-panel-header h2 {
        font-size: 19px;
    }

    .loan-table-wrap {
        display: none;
    }

    .loan-mobile-list {
        display: grid;
        gap: 12px;
        padding: 14px;
    }

    .loan-mobile-card h3 {
        font-size: 15px;
    }

    .loan-total {
        align-items: flex-start;
        flex-direction: column;
        gap: 8px;
        padding: 16px;
    }

    .loan-total strong {
        font-size: 18px;
    }
}
</style>

<div class="loan-page">
    <div class="loan-heading">
        <h1>Lịch sử mượn sách</h1>
        <p>Theo dõi các lượt mượn và tình trạng trả sách của bạn.</p>
    </div>

    <div class="loan-stats">
        <div class="loan-stat">
            <div class="loan-stat-label">Tổng lượt mượn</div>
            <div class="loan-stat-value"><?= $totalLoans ?></div>
        </div>

        <div class="loan-stat">
            <div class="loan-stat-label">Đã trả</div>
            <div class="loan-stat-value"><?= $returnedLoans ?></div>
        </div>

        <div class="loan-stat">
            <div class="loan-stat-label">Đang mượn</div>
            <div class="loan-stat-value"><?= $borrowingLoans ?></div>
        </div>

        <div class="loan-stat">
            <div class="loan-stat-label">Quá hạn</div>
            <div class="loan-stat-value"><?= $overdueLoans ?></div>
        </div>
    </div>

    <div class="loan-panel">
        <div class="loan-panel-header">
            <h2>Danh sách mượn sách</h2>
            <p>Thông tin chi tiết các lượt mượn và trả sách.</p>
        </div>

        <?php if (empty($loans)): ?>
            <div class="loan-empty">
                Bạn chưa có lịch sử mượn sách nào.
            </div>
        <?php else: ?>

            <div class="loan-table-wrap">
                <table class="loan-table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tên sách</th>
                            <th>Mã bản sao</th>
                            <th>Ngày mượn</th>
                            <th>Hạn trả</th>
                            <th>Ngày trả</th>
                            <th>Trạng thái</th>
                            <th>Tiền phạt</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($loans as $index => $loan): ?>
                            <?php
                            if (($loan['da_tra'] ?? false) == true) {
                                $statusClass = 'returned';
                                $statusLabel = 'Đã trả';
                            } elseif (($loan['qua_han'] ?? false) == true) {
                                $statusClass = 'overdue';
                                $statusLabel = 'Quá hạn';
                            } else {
                                $statusClass = 'borrowing';
                                $statusLabel = 'Đang mượn';
                            }
                            ?>

                            <tr>
                                <td><?= $index + 1 ?></td>

                                <td>
                                    <div class="loan-book-title">
                                        <?= e($loan['ten_sach'] ?? '') ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="loan-code">
                                        <?= e($loan['ma_ban_sao'] ?? '') ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e(format_datetime($loan['muon_luc'])) ?>
                                </td>

                                <td>
                                    <?= e(format_date($loan['han_tra'])) ?>
                                </td>

                                <td>
                                    <?= !empty($loan['tra_luc'])
                                        ? e(format_datetime($loan['tra_luc']))
                                        : 'Chưa trả' ?>
                                </td>

                                <td>
                                    <span class="loan-status <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>

                                <td class="loan-fine">
                                    <?= e(format_money($loan['tien_phat'] ?? 0)) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="loan-mobile-list">
                <?php foreach ($loans as $index => $loan): ?>
                    <?php
                    if (($loan['da_tra'] ?? false) == true) {
                        $statusClass = 'returned';
                        $statusLabel = 'Đã trả';
                    } elseif (($loan['qua_han'] ?? false) == true) {
                        $statusClass = 'overdue';
                        $statusLabel = 'Quá hạn';
                    } else {
                        $statusClass = 'borrowing';
                        $statusLabel = 'Đang mượn';
                    }
                    ?>

                    <div class="loan-mobile-card">
                        <h3>
                            <?= $index + 1 ?>.
                            <?= e($loan['ten_sach'] ?? '') ?>
                        </h3>

                        <div class="loan-mobile-row">
                            <span>Mã bản sao</span>
                            <span><?= e($loan['ma_ban_sao'] ?? '') ?></span>
                        </div>

                        <div class="loan-mobile-row">
                            <span>Ngày mượn</span>
                            <span><?= e(format_datetime($loan['muon_luc'])) ?></span>
                        </div>

                        <div class="loan-mobile-row">
                            <span>Hạn trả</span>
                            <span><?= e(format_date($loan['han_tra'])) ?></span>
                        </div>

                        <div class="loan-mobile-row">
                            <span>Ngày trả</span>
                            <span>
                                <?= !empty($loan['tra_luc'])
                                    ? e(format_datetime($loan['tra_luc']))
                                    : 'Chưa trả' ?>
                            </span>
                        </div>

                        <div class="loan-mobile-row">
                            <span>Trạng thái</span>
                            <span class="loan-status <?= $statusClass ?>">
                                <?= $statusLabel ?>
                            </span>
                        </div>

                        <div class="loan-mobile-row">
                            <span>Tiền phạt</span>
                            <span class="loan-fine">
                                <?= e(format_money($loan['tien_phat'] ?? 0)) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="loan-total">
                <span>Tổng tiền phạt trong lịch sử</span>
                <strong><?= e(format_money($totalFine)) ?></strong>
            </div>

        <?php endif; ?>
    </div>
</div>
