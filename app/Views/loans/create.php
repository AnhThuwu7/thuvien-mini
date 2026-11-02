<?php
$title = $title ?? 'Lập phiếu mượn sách mới';
$defaultDueDate = date('Y-m-d', strtotime('+14 days'));
?>

<style>
    .loan-create-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 14px 20px 20px; /* Tối ưu lề gọn gàng để vừa khít màn hình */
        color: #382719;
        font-family: inherit;
    }

    .loan-create-heading {
        margin-bottom: 16px;
    }

    .loan-create-heading h2 {
        margin: 0 0 5px;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.5px;
        color: #382719;
    }

    .loan-create-heading p {
        margin: 0;
        color: #827466;
        font-size: 13px;
    }

    .loan-create-layout {
        display: grid;
        grid-template-columns: minmax(280px, .78fr) minmax(0, 1.5fr);
        align-items: stretch;
        gap: 22px;
    }

    /* Cột giới thiệu (To, đẹp, giữ nguyên phong cách ban đầu) */
    .loan-create-intro {
        position: relative;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 480px;
        padding: 26px 24px;
        border-radius: 18px;
        background: linear-gradient(155deg, #76543b 0%, #4d3628 100%);
        color: #fffaf4;
        box-shadow: 0 12px 30px rgba(69, 46, 26, .12);
    }

    .loan-create-intro::before,
    .loan-create-intro::after {
        position: absolute;
        content: "";
        border: 1px solid rgba(255, 250, 244, .10);
        border-radius: 50%;
        pointer-events: none;
    }

    .loan-create-intro::before {
        width: 240px;
        height: 240px;
        right: -100px;
        top: 60px;
    }

    .loan-create-intro::after {
        width: 160px;
        height: 160px;
        right: -60px;
        top: 100px;
    }

    .loan-create-intro-content {
        position: relative;
        z-index: 1;
    }

    .loan-create-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 11px;
        border: 1px solid rgba(255, 250, 244, .2);
        border-radius: 20px;
        background: rgba(255, 250, 244, .08);
        color: #f4e2cb;
        font-size: 10px;
        font-weight: 750;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .loan-create-book-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 65px;
        margin: 20px 0 15px;
        border: 1px solid rgba(255, 250, 244, .2);
        border-radius: 18px;
        background: rgba(255, 250, 244, .1);
        font-size: 30px;
    }

    .loan-create-intro h3 {
        max-width: 280px;
        margin: 0 0 10px;
        color: #fffaf4;
        font-size: 23px;
        line-height: 1.35;
        font-weight: 800;
    }

    .loan-create-intro-description {
        margin: 0;
        color: #e6d8ca;
        font-size: 12px;
        line-height: 1.7;
    }

    .loan-create-intro-divider {
        height: 1px;
        margin: 18px 0 16px;
        background: rgba(255, 250, 244, .18);
    }

    .loan-create-step-list {
        display: grid;
        gap: 12px;
    }

    .loan-create-step {
        display: flex;
        align-items: flex-start;
        gap: 11px;
    }

    .loan-create-step-number {
        display: flex;
        flex: 0 0 26px;
        width: 26px;
        height: 26px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255, 250, 244, .13);
        color: #f4e2cb;
        font-size: 11px;
        font-weight: 800;
    }

    .loan-create-step strong {
        display: block;
        margin: 0 0 2px;
        color: #fffaf4;
        font-size: 12px;
    }

    .loan-create-step span {
        display: block;
        color: #e0cfc0;
        font-size: 11px;
        line-height: 1.5;
    }

    .loan-create-intro-footer {
        position: relative;
        z-index: 1;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 250, 244, .15);
        color: #e0cfc0;
        font-size: 11px;
    }

    /* Cột form bên phải (To đẹp chuẩn mực) */
    .loan-create-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e8ddd0;
        border-radius: 18px;
        background: #fffdf9;
        box-shadow: 0 10px 32px rgba(69, 46, 26, .06);
    }

    .loan-create-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid #eee5db;
        background: #fbf6ef;
    }

    .loan-create-card-header-icon {
        display: flex;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #efe2d1;
        font-size: 18px;
    }

    .loan-create-card-header h3 {
        margin: 0 0 3px;
        color: #382719;
        font-size: 15px;
        font-weight: 800;
    }

    .loan-create-card-header p {
        margin: 0;
        color: #918170;
        font-size: 11px;
    }

    .loan-create-form {
        padding: 20px 22px;
    }

    .loan-create-error {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 11px 14px;
        margin-bottom: 16px;
        border: 1px solid #edc9c0;
        border-radius: 9px;
        background: #fff1ed;
        color: #963c2e;
        font-size: 12px;
    }

    .loan-create-section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #eee5db;
    }

    .loan-create-section-number {
        display: flex;
        flex: 0 0 29px;
        width: 29px;
        height: 29px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #efe2d1;
        color: #76543b;
        font-size: 11px;
        font-weight: 800;
    }

    .loan-create-section-heading h4 {
        margin: 0 0 2px;
        color: #493729;
        font-size: 13px;
        font-weight: 800;
    }

    .loan-create-section-heading p {
        margin: 0;
        color: #948575;
        font-size: 10px;
    }

    .loan-create-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 15px;
    }

    .loan-create-field label {
        display: block;
        margin-bottom: 6px;
        color: #493729;
        font-size: 12px;
        font-weight: 750;
    }

    .loan-create-required {
        color: #b34e3b;
    }

    .loan-create-field select,
    .loan-create-field input[type="text"] {
        display: block;
        box-sizing: border-box;
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid #dfd2c3;
        border-radius: 9px;
        outline: none;
        background: #fff;
        color: #382719;
        font-family: inherit;
        font-size: 12px;
        transition: border-color .2s, box-shadow .2s;
    }

    .loan-create-field select:focus,
    .loan-create-field input[type="text"]:focus {
        border-color: #927052;
        box-shadow: 0 0 0 3px rgba(146, 112, 82, .13);
    }

    .loan-create-help {
        display: block;
        margin-top: 5px;
        color: #928373;
        font-size: 10px;
    }

    .loan-create-date-wrap {
        position: relative;
    }

    .loan-create-date-wrap input[type="text"] {
        padding-right: 45px;
    }

    .loan-create-calendar-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        display: flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 7px;
        background: #f5ecdf;
        color: #76543b;
        font-size: 15px;
        cursor: pointer;
    }

    .loan-create-calendar-btn:hover {
        background: #ead9c4;
    }

    .loan-create-hidden-date {
        position: absolute;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        border: 0 !important;
        opacity: 0;
        pointer-events: none;
    }

    .loan-create-notice {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 11px 14px;
        margin-top: 16px;
        border: 1px solid #e9dece;
        border-radius: 9px;
        background: #faf5ed;
        color: #6d5742;
        font-size: 10px;
        line-height: 1.6;
    }

    .loan-create-notice p {
        margin: 0;
    }

    .loan-create-notice strong {
        color: #5a402c;
    }

    .loan-create-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 16px;
        margin-top: 16px;
        border-top: 1px solid #eee5db;
    }

    .loan-create-btn {
        display: inline-flex;
        flex: 1 1 auto;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 16px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 750;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s;
    }

    .loan-create-btn-primary {
        background: #76543b;
        color: #fffaf4;
        box-shadow: 0 4px 10px rgba(77, 50, 30, .12);
    }

    .loan-create-btn-primary:hover {
        background: #5f412d;
    }

    .loan-create-btn-secondary {
        border-color: #e3d7c9;
        background: #fff;
        color: #5d4938;
    }

    .loan-create-btn-secondary:hover {
        background: #f7f0e7;
    }
</style>

<div class="loan-create-page">

    <div class="loan-create-heading">
        <h2>Lập phiếu mượn sách mới</h2>
        <p>Tạo phiếu mượn và ghi nhận thông tin độc giả, bản sao sách cùng thời hạn trả.</p>
    </div>

    <div class="loan-create-layout">

        <!-- CỘT TRÁI: GIỚI THIỆU -->
        <aside class="loan-create-intro">
            <div class="loan-create-intro-content">

                <span class="loan-create-eyebrow">
                    <span>✦</span> HỆ THỐNG THƯ VIỆN
                </span>

                <div class="loan-create-book-icon" aria-hidden="true">
                    📚
                </div>

                <h3>Một phiếu mượn, một trải nghiệm tốt.</h3>

                <p class="loan-create-intro-description">
                    Ghi nhận thông tin mượn sách rõ ràng, chính xác để việc quản lý và theo dõi sách trở nên thuận tiện hơn.
                </p>

                <div class="loan-create-intro-divider"></div>

                <div class="loan-create-step-list">

                    <div class="loan-create-step">
                        <div class="loan-create-step-number">01</div>
                        <div>
                            <strong>Chọn độc giả</strong>
                            <span>Xác định người nhận sách.</span>
                        </div>
                    </div>

                    <div class="loan-create-step">
                        <div class="loan-create-step-number">02</div>
                        <div>
                            <strong>Chọn bản sao</strong>
                            <span>Kiểm tra mã sách cần cho mượn.</span>
                        </div>
                    </div>

                    <div class="loan-create-step">
                        <div class="loan-create-step-number">03</div>
                        <div>
                            <strong>Đặt hạn trả</strong>
                            <span>Kiểm tra ngày trước khi xác nhận.</span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="loan-create-intro-footer">
                Vui lòng kiểm tra thông tin trước khi tạo phiếu.
            </div>
        </aside>

        <!-- CỘT PHẢI: FORM -->
        <section class="loan-create-card">

            <div class="loan-create-card-header">
                <div class="loan-create-card-header-icon" aria-hidden="true">
                    📝
                </div>
                <div>
                    <h3>Thông tin phiếu mượn</h3>
                    <p>Nhập đầy đủ các trường bắt buộc bên dưới.</p>
                </div>
            </div>

            <form
                method="post"
                action="<?= e(url('loans/store')) ?>"
                class="loan-create-form"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(Csrf::token()) ?>"
                >

                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="loan-create-error" role="alert">
                        <span aria-hidden="true">⚠️</span>
                        <div>
                            <strong>Không thể tạo phiếu mượn</strong>
                            <?= e($_SESSION['error']); unset($_SESSION['error']); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="loan-create-section-heading">
                    <span class="loan-create-section-number">01</span>
                    <div>
                        <h4>Chi tiết mượn sách</h4>
                        <p>Các trường có dấu * là bắt buộc.</p>
                    </div>
                </div>

                <div class="loan-create-grid">

                    <div class="loan-create-field">
                        <label for="nguoi_dung_id">
                            Độc giả <span class="loan-create-required">*</span>
                        </label>

                        <select
                            id="nguoi_dung_id"
                            name="nguoi_dung_id"
                            required
                        >
                            <option value="">-- Chọn độc giả --</option>
                            <?php foreach ($readers as $reader): ?>
                                <option value="<?= (int) $reader['id'] ?>">
                                    <?= e($reader['ho_ten']) ?> (<?= e($reader['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="loan-create-help">Chọn độc giả sẽ nhận sách mượn.</small>
                    </div>

                    <div class="loan-create-field">
                        <label for="ban_sao_id">
                            Bản sao sách <span class="loan-create-required">*</span>
                        </label>

                        <select
                            id="ban_sao_id"
                            name="ban_sao_id"
                            required
                        >
                            <option value="">-- Chọn bản sao sách --</option>
                            <?php foreach ($availableCopies as $copy): ?>
                                <option value="<?= (int) $copy['id'] ?>">
                                    [<?= e($copy['ma_ban_sao']) ?>] <?= e($copy['ten_sach']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="loan-create-help">Chọn bản sao trong danh sách sẵn sàng cho mượn.</small>
                    </div>

                    <div class="loan-create-field">
                        <label for="han_tra_hien_thi">
                            Hạn trả sách <span class="loan-create-required">*</span>
                        </label>

                        <div class="loan-create-date-wrap">
                            <input
                                type="text"
                                id="han_tra_hien_thi"
                                value="<?= e(date('d/m/Y', strtotime($defaultDueDate))) ?>"
                                placeholder="dd/mm/yyyy"
                                autocomplete="off"
                                inputmode="numeric"
                                maxlength="10"
                                aria-describedby="han_tra_help"
                                required
                            >

                            <button
                                type="button"
                                class="loan-create-calendar-btn"
                                id="chon_lich_han_tra"
                                aria-label="Mở lịch chọn hạn trả"
                                title="Chọn ngày trên lịch"
                            >📅</button>

                            <input
                                type="date"
                                id="han_tra_lich"
                                class="loan-create-hidden-date"
                                value="<?= e($defaultDueDate) ?>"
                                tabindex="-1"
                                aria-hidden="true"
                            >

                            <input
                                type="hidden"
                                name="han_tra"
                                id="han_tra"
                                value="<?= e($defaultDueDate) ?>"
                            >
                        </div>
                        <small class="loan-create-help" id="han_tra_help">Nhập trực tiếp định dạng dd/mm/yyyy hoặc bấm biểu tượng lịch.</small>
                    </div>

                </div>

                <div class="loan-create-notice">
                    <span aria-hidden="true">ℹ️</span>
                    <p>
                        <strong>Lưu ý:</strong> Kiểm tra đúng độc giả, mã bản sao và hạn trả trước khi xác nhận.
                    </p>
                </div>

                <div class="loan-create-actions">
                    <a
                        href="<?= e(url('loans')) ?>"
                        class="loan-create-btn loan-create-btn-secondary"
                    >
                        ← Quay lại
                    </a>

                    <button
                        type="submit"
                        class="loan-create-btn loan-create-btn-primary"
                    >
                        ✓ Xác nhận tạo phiếu
                    </button>
                </div>

            </form>
        </section>
    </div>
</div>

<script>
(function () {
    const displayInput = document.getElementById('han_tra_hien_thi');
    const datePicker = document.getElementById('han_tra_lich');
    const hiddenInput = document.getElementById('han_tra');
    const calendarButton = document.getElementById('chon_lich_han_tra');

    if (!displayInput || !datePicker || !hiddenInput || !calendarButton) {
        return;
    }

    function toVietnameseDate(isoDate) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(isoDate)) {
            return '';
        }
        const parts = isoDate.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function isValidVietnameseDate(value) {
        const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if (!match) {
            return false;
        }
        const day = Number(match[1]);
        const month = Number(match[2]);
        const year = Number(match[3]);
        const date = new Date(Date.UTC(year, month - 1, day));
        return date.getUTCFullYear() === year
            && date.getUTCMonth() === month - 1
            && date.getUTCDate() === day;
    }

    function syncDate() {
        const value = displayInput.value.trim();
        if (!isValidVietnameseDate(value)) {
            displayInput.setCustomValidity('Hãy nhập ngày hợp lệ theo định dạng dd/mm/yyyy.');
            hiddenInput.value = '';
            return false;
        }
        const parts = value.split('/');
        const isoDate = parts[2] + '-' + parts[1] + '-' + parts[0];
        displayInput.setCustomValidity('');
        hiddenInput.value = isoDate;
        datePicker.value = isoDate;
        return true;
    }

    displayInput.addEventListener('input', syncDate);

    displayInput.addEventListener('blur', function () {
        const digits = displayInput.value.replace(/\D/g, '');
        if (/^\d{8}$/.test(digits)) {
            displayInput.value =
                digits.substring(0, 2) + '/' +
                digits.substring(2, 4) + '/' +
                digits.substring(4, 8);
        }
        syncDate();
    });

    calendarButton.addEventListener('click', function () {
        if (typeof datePicker.showPicker === 'function') {
            datePicker.showPicker();
        } else {
            datePicker.focus();
            datePicker.click();
        }
    });

    datePicker.addEventListener('change', function () {
        if (!datePicker.value) {
            return;
        }
        displayInput.value = toVietnameseDate(datePicker.value);
        syncDate();
    });

    const form = displayInput.closest('form');
    if (form) {
        form.addEventListener('submit', function (event) {
            if (!syncDate()) {
                event.preventDefault();
                displayInput.reportValidity();
                displayInput.focus();
            }
        });
    }

    syncDate();
})();
</script>