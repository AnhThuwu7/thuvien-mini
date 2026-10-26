
<?php

$requests = $requests ?? [];

$totalRequests = count($requests);
$requestsWithNotes = 0;
$requestsWithoutNotes = 0;

foreach ($requests as $request) {
    if (trim((string) ($request['ghi_chu'] ?? '')) !== '') {
        $requestsWithNotes++;
    } else {
        $requestsWithoutNotes++;
    }
}

$errorMessage = trim((string) ($_GET['error'] ?? ''));
$successMessage = trim((string) ($_GET['success'] ?? ''));

?>

<style>
.request-page,
.request-page * {
    box-sizing: border-box;
}

.request-page {
    width: 100%;
    max-width: 1440px;
    min-width: 0;
    margin: 0 auto;
    padding: 16px 20px 24px;
    color: #3d2b1e;
    font-family: inherit;
}

.request-page button,
.request-page input,
.request-page textarea {
    font: inherit;
}

.request-alert {
    margin-bottom: 12px;
    padding: 10px 14px;
    border-radius: 9px;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.request-alert.error {
    border: 1px solid #f0c9c5;
    background: #fff0ee;
    color: #96372f;
}

.request-alert.success {
    border: 1px solid #cce6d2;
    background: #edf8f0;
    color: #286b40;
}

.request-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 15px;
}

.request-heading h2 {
    margin: 0 0 4px;
    color: #392719;
    font-size: clamp(22px, 2.4vw, 26px);
    font-weight: 800;
    line-height: 1.35;
}

.request-heading p {
    margin: 0;
    color: #89796b;
    font-size: 13px;
    line-height: 1.5;
}

.request-count {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 13px;
    border: 1px solid #eadbca;
    border-radius: 11px;
    background: #fffdf9;
}

.request-count-icon {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: #f4e8d9;
    font-size: 18px;
}

.request-count-label {
    color: #887564;
    font-size: 11px;
}

.request-count-number {
    margin-top: 2px;
    color: #52371f;
    font-size: 21px;
    font-weight: 800;
}

.request-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 11px;
    margin-bottom: 16px;
}

.summary-card {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
    min-height: 72px;
    padding: 12px 15px;
    border: 1px solid #eee3d7;
    border-radius: 12px;
    background: #fff;
}

.summary-icon {
    display: grid;
    place-items: center;
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #f5eadc;
    color: #79532f;
    font-size: 18px;
}

.summary-icon.pending {
    background: #fff2d9;
    color: #94620b;
}

.summary-icon.note {
    background: #e9f0fb;
    color: #41679d;
}

.summary-label {
    margin-bottom: 2px;
    color: #8a7a6c;
    font-size: 11px;
    line-height: 1.45;
}

.summary-value {
    color: #422e1f;
    font-size: 21px;
    font-weight: 800;
}

.request-panel {
    min-width: 0;
    overflow: hidden;
    border: 1px solid #eee3d7;
    border-radius: 13px;
    background: #fff;
}

.request-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 9px;
    padding: 13px 17px;
    border-bottom: 1px solid #f0e8df;
}

.request-panel-header h3 {
    margin: 0 0 3px;
    color: #422e1f;
    font-size: 16px;
    font-weight: 800;
}

.request-panel-header p {
    margin: 0;
    color: #918173;
    font-size: 12px;
    line-height: 1.5;
}

.pending-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border: 1px solid #f2dfb7;
    border-radius: 30px;
    background: #fff8e9;
    color: #8a6016;
    font-size: 11px;
    font-weight: 750;
}

.pending-label::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #d99b27;
}

.request-list {
    display: grid;
    gap: 10px;
    padding: 12px;
}

.request-card {
    min-width: 0;
    padding: 14px 15px;
    border: 1px solid #eee5db;
    border-radius: 11px;
    background: #fffefa;
    transition: border-color .2s, box-shadow .2s;
}

.request-card:hover {
    border-color: #dfc7ad;
    box-shadow: 0 3px 10px rgba(65, 42, 25, .04);
}

.request-card-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}

.request-number {
    color: #98704d;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .6px;
}

.request-book {
    margin-top: 3px;
    color: #3f2a1b;
    font-size: 15px;
    font-weight: 800;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

.request-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
    padding: 5px 8px;
    border-radius: 30px;
    background: #fff1d4;
    color: #8e600d;
    font-size: 10px;
    font-weight: 800;
}

.request-status::before {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #d49a28;
}

.request-info-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 9px;
    margin-bottom: 10px;
}

.request-info {
    min-width: 0;
    padding: 9px 10px;
    border-radius: 8px;
    background: #f8f3ed;
}

.request-info-label {
    margin-bottom: 3px;
    color: #927f6e;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .2px;
}

.request-info-value {
    color: #4b3525;
    font-size: 12px;
    font-weight: 650;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.request-reader-code {
    display: block;
    margin-top: 2px;
    color: #9b8877;
    font-size: 10px;
    font-weight: 500;
}

.request-note {
    margin-bottom: 10px;
    padding: 8px 11px;
    border-left: 3px solid #ddc4a7;
    border-radius: 0 8px 8px 0;
    background: #fcf8f3;
}

.request-note-label {
    margin-bottom: 3px;
    color: #8b725b;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
}

.request-note-content {
    color: #6d5948;
    font-size: 12px;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.request-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.request-action-form {
    margin: 0;
}

.request-btn {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    min-width: 112px;
    min-height: 34px;
    padding: 7px 12px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: background .2s, transform .2s;
}

.request-btn:hover {
    transform: translateY(-1px);
}

.request-btn:focus-visible,
.request-inline-reject textarea:focus-visible {
    outline: 3px solid #c49a70;
    outline-offset: 2px;
}

.request-btn-approve {
    background: #e5f3e8;
    border-color: #d2e9d8;
    color: #286b40;
}

.request-btn-approve:hover {
    background: #d6eddd;
}

.request-btn-reject {
    background: #fff0ee;
    border-color: #f1d9d5;
    color: #a43d36;
}

.request-btn-reject:hover {
    background: #fbe1dd;
}

.request-inline-reject {
    margin-top: 10px;
    padding: 12px;
    border: 1px solid #eadbca;
    border-radius: 10px;
    background: #fcf8f3;
}

.request-inline-reject[hidden] {
    display: none !important;
}

.request-inline-reject label {
    display: block;
    margin-bottom: 6px;
    color: #594332;
    font-size: 12px;
    font-weight: 800;
}

.request-inline-reject textarea {
    display: block;
    width: 100%;
    min-height: 65px;
    padding: 9px 10px;
    border: 1px solid #e4d7c8;
    border-radius: 8px;
    background: #fff;
    color: #453124;
    font-size: 12px;
    line-height: 1.5;
    resize: vertical;
}

.request-inline-reject textarea:focus {
    border-color: #bd9874;
    outline: none;
}

.request-inline-help {
    margin-top: 5px;
    color: #958474;
    font-size: 10px;
    line-height: 1.5;
}

.request-inline-reject-actions {
    display: flex;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 9px;
}

.request-inline-cancel {
    border-color: #e9dfd4;
    background: #fff;
    color: #725c49;
}

.request-inline-confirm {
    background: #a83d35;
    color: #fff;
}

.request-inline-confirm:hover {
    background: #8f302a;
}

.request-panel-footer {
    padding: 10px 16px;
    border-top: 1px solid #f0e8df;
    background: #fdfaf6;
    color: #8b7b6d;
    font-size: 11px;
    line-height: 1.5;
}

.empty-requests {
    padding: 32px 16px;
    text-align: center;
}

.empty-icon {
    display: grid;
    place-items: center;
    width: 52px;
    height: 52px;
    margin: 0 auto 12px;
    border-radius: 16px;
    background: #f5eadc;
    font-size: 25px;
}

.empty-requests h4 {
    margin: 0 0 7px;
    color: #493323;
    font-size: 16px;
    font-weight: 800;
}

.empty-requests p {
    margin: 0;
    color: #918173;
    font-size: 12px;
    line-height: 1.6;
}

@media (max-width: 900px) {
    .request-page {
        padding: 14px 12px 20px;
    }

    .request-summary {
        gap: 8px;
    }

    .summary-card {
        padding: 10px;
    }

    .request-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 560px) {
    .request-page {
        padding: 12px 9px 18px;
    }

    .request-heading {
        align-items: flex-start;
    }

    .request-heading h2 {
        font-size: 21px;
    }

    .request-count {
        width: 100%;
    }

    .request-summary {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
    }

    .summary-card {
        min-height: 76px;
        align-items: flex-start;
        flex-direction: column;
        gap: 5px;
        padding: 8px 6px;
    }

    .summary-icon {
        width: 27px;
        height: 27px;
        border-radius: 8px;
        font-size: 14px;
    }

    .summary-label {
        font-size: 10px;
    }

    .summary-value {
        font-size: 18px;
    }

    .request-panel-header {
        padding: 12px;
    }

    .request-list {
        padding: 8px;
        gap: 8px;
    }

    .request-card {
        padding: 11px;
    }

    .request-card-top {
        gap: 7px;
    }

    .request-book {
        font-size: 14px;
    }

    .request-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .request-info {
        padding: 8px;
    }

    .request-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .request-action-form {
        min-width: 0;
    }

    .request-btn {
        width: 100%;
        min-width: 0;
        padding: 8px 5px;
        font-size: 11px;
    }

    .request-inline-reject {
        padding: 10px;
    }

    .request-inline-reject-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .request-inline-reject-actions button {
        width: 100%;
        min-width: 0;
    }

    .request-panel-footer {
        padding: 10px 12px;
    }
}
</style>

<div class="request-page">

    <?php if ($errorMessage !== ''): ?>
        <div class="request-alert error" role="alert">
            <strong>Không thể xử lý yêu cầu.</strong>
            <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($successMessage !== ''): ?>
        <div class="request-alert success" role="status">
            <strong>Thành công!</strong>
            <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <header class="request-heading">
        <div>
            <h2>Yêu cầu mượn sách</h2>
            <p>Kiểm tra thông tin và xử lý yêu cầu mượn sách của độc giả.</p>
        </div>

        <div class="request-count">
            <div class="request-count-icon">📚</div>
            <div>
                <div class="request-count-label">Yêu cầu đang chờ</div>
                <div class="request-count-number"><?= $totalRequests ?></div>
            </div>
        </div>
    </header>

    <section class="request-summary">
        <div class="summary-card">
            <div class="summary-icon pending">◷</div>
            <div>
                <div class="summary-label">Đang chờ xử lý</div>
                <div class="summary-value"><?= $totalRequests ?></div>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon note">▤</div>
            <div>
                <div class="summary-label">Có ghi chú</div>
                <div class="summary-value"><?= $requestsWithNotes ?></div>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon">☷</div>
            <div>
                <div class="summary-label">Không có ghi chú</div>
                <div class="summary-value"><?= $requestsWithoutNotes ?></div>
            </div>
        </div>
    </section>

    <section class="request-panel">
        <div class="request-panel-header">
            <div>
                <h3>Danh sách yêu cầu</h3>
                <p>Các yêu cầu đang ở trạng thái chờ duyệt.</p>
            </div>

            <span class="pending-label">
                <?= $totalRequests ?> đang chờ
            </span>
        </div>

        <?php if (empty($requests)): ?>

            <div class="empty-requests">
                <div class="empty-icon">📚</div>
                <h4>Chưa có yêu cầu mượn sách</h4>
                <p>
                    Hiện tại không có yêu cầu nào đang chờ duyệt.<br>
                    Các yêu cầu mới sẽ xuất hiện tại đây.
                </p>
            </div>

        <?php else: ?>

            <div class="request-list">

                <?php foreach ($requests as $request): ?>

                    <?php
                    $requestId = (int) ($request['id'] ?? 0);
                    $readerName = $request['ho_ten'] ?? 'Chưa có thông tin';
                    $readerCode = $request['ma_doc_gia'] ?? '';
                    $bookName = $request['ten_sach'] ?? 'Chưa có tên sách';
                    $requestDate = $request['yeu_cau_luc'] ?? '';
                    $requestNote = trim((string) ($request['ghi_chu'] ?? ''));
                    ?>

                    <article class="request-card">

                        <div class="request-card-top">
                            <div>
                                <div class="request-number">
                                    YÊU CẦU #<?= $requestId ?>
                                </div>

                                <div class="request-book">
                                    <?= e($bookName) ?>
                                </div>
                            </div>

                            <span class="request-status">Chờ duyệt</span>
                        </div>

                        <div class="request-info-grid">

                            <div class="request-info">
                                <div class="request-info-label">Độc giả</div>

                                <div class="request-info-value">
                                    <?= e($readerName) ?>

                                    <?php if ($readerCode !== ''): ?>
                                        <span class="request-reader-code">
                                            Mã độc giả: <?= e($readerCode) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="request-info">
                                <div class="request-info-label">Ngày yêu cầu</div>

                                <div class="request-info-value">
                                    <?= e(
                                        $requestDate !== ''
                                            ? $requestDate
                                            : 'Chưa có thông tin'
                                    ) ?>
                                </div>
                            </div>

                            <div class="request-info">
                                <div class="request-info-label">Mã yêu cầu</div>
                                <div class="request-info-value">
                                    #<?= $requestId ?>
                                </div>
                            </div>

                        </div>

                        <div class="request-note">
                            <div class="request-note-label">
                                Ghi chú của độc giả
                            </div>

                            <div class="request-note-content">
                                <?= e(
                                    $requestNote !== ''
                                        ? $requestNote
                                        : 'Độc giả không để lại ghi chú.'
                                ) ?>
                            </div>
                        </div>

                        <div class="request-actions">

                            <form
                                method="POST"
                                action="<?= e(BASE_URL) ?>/loan-requests/approve"
                                class="request-action-form"
                            >
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= e(Csrf::token()) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="request_id"
                                    value="<?= $requestId ?>"
                                >

                                <button
                                    type="submit"
                                    class="request-btn request-btn-approve"
                                >
                                    ✓ Duyệt yêu cầu
                                </button>
                            </form>

                            <button
                                type="button"
                                class="request-btn request-btn-reject"
                                aria-expanded="false"
                                aria-controls="rejectForm-<?= $requestId ?>"
                                onclick="toggleInlineReject(<?= $requestId ?>, this)"
                            >
                                ✕ Từ chối
                            </button>

                        </div>

                        <div
                            class="request-inline-reject"
                            id="rejectForm-<?= $requestId ?>"
                            hidden
                        >
                            <form
                                method="POST"
                                action="<?= e(BASE_URL) ?>/loan-requests/reject"
                            >
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= e(Csrf::token()) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="request_id"
                                    value="<?= $requestId ?>"
                                >

                                <label for="rejectReason-<?= $requestId ?>">
                                    Lý do từ chối yêu cầu
                                </label>

                                <textarea
                                    name="ly_do_tu_choi"
                                    id="rejectReason-<?= $requestId ?>"
                                    maxlength="255"
                                    placeholder="Ví dụ: Hiện không còn bản sao sách phù hợp..."
                                ></textarea>

                                <div class="request-inline-help">
                                    Tối đa 255 ký tự. Nếu để trống, hệ thống
                                    có thể sử dụng lý do mặc định.
                                </div>

                                <div class="request-inline-reject-actions">
                                    <button
                                        type="button"
                                        class="request-btn request-inline-cancel"
                                        onclick="toggleInlineReject(<?= $requestId ?>, null, false)"
                                    >
                                        Hủy
                                    </button>

                                    <button
                                        type="submit"
                                        class="request-btn request-inline-confirm"
                                    >
                                        Xác nhận từ chối
                                    </button>
                                </div>
                            </form>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

            <div class="request-panel-footer">
                Đang hiển thị <?= $totalRequests ?> yêu cầu chờ duyệt.
                Sau khi xử lý, danh sách sẽ được cập nhật.
            </div>

        <?php endif; ?>

    </section>

</div>

<script>
function toggleInlineReject(requestId, button = null, forceShow = null) {
    const form = document.getElementById('rejectForm-' + requestId);

    if (!form) {
        return;
    }

    const shouldShow = forceShow === null
        ? form.hidden
        : forceShow;

    form.hidden = !shouldShow;

    const rejectButton = button || document.querySelector(
        '[aria-controls="rejectForm-' + requestId + '"]'
    );

    if (rejectButton) {
        rejectButton.setAttribute(
            'aria-expanded',
            shouldShow ? 'true' : 'false'
        );
    }

    if (shouldShow) {
        const textarea = form.querySelector('textarea');

        if (textarea) {
            textarea.focus();
        }
    }
}
</script>
