<?php
$errors = $errors ?? [];
$old = $old ?? ['ho_ten' => '', 'email' => '', 'so_dien_thoai' => ''];
?>
<section class="card narrow" style="max-width:420px;margin:1.5rem auto;">
    <h1 style="margin-top:0;">Đăng ký độc giả</h1>
    <p style="color:var(--text-muted);font-size:0.95rem;">
        Chỉ đăng ký tài khoản <strong>độc giả</strong>. Tài khoản thủ thư / quản trị do admin cấp.
    </p>

    <?php if (!empty($errors['register'])): ?>
        <div class="alert alert-danger"><?= e($errors['register']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('auth/register')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="form-group mb-3">
            <label for="ho_ten">Họ và tên</label>
            <input type="text" id="ho_ten" name="ho_ten" value="<?= e($old['ho_ten'] ?? '') ?>" required maxlength="100" autocomplete="name">
            <?php if (!empty($errors['ho_ten'])): ?>
                <p class="error"><?= e($errors['ho_ten']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required maxlength="190" autocomplete="email">
            <?php if (!empty($errors['email'])): ?>
                <p class="error"><?= e($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="so_dien_thoai">Số điện thoại (tuỳ chọn, đúng 10 số)</label>
            <input
                type="text"
                id="so_dien_thoai"
                name="so_dien_thoai"
                value="<?= e($old['so_dien_thoai'] ?? '') ?>"
                maxlength="10"
                inputmode="numeric"
                pattern="[0-9]{10}"
                autocomplete="tel"
                placeholder="0912345678"
            >
            <p style="margin:0.25rem 0 0;font-size:0.85rem;color:var(--text-muted);">Chỉ nhập 10 chữ số, không khoảng trắng.</p>
            <?php if (!empty($errors['so_dien_thoai'])): ?>
                <p class="error"><?= e($errors['so_dien_thoai']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="password">Mật khẩu</label>
            <div class="pw-wrap">
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                <button type="button" class="pw-toggle" data-target="password" aria-label="Hiện mật khẩu" title="Hiện/ẩn mật khẩu">
                    <svg class="icon-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="icon-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"></path>
                        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <p style="margin:0.25rem 0 0;font-size:0.85rem;color:var(--text-muted);">Tối thiểu 8 ký tự; có chữ hoa, chữ thường và ký hiệu đặc biệt (ví dụ @ # !).</p>
            <?php if (!empty($errors['password'])): ?>
                <p class="error"><?= e($errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="password_confirmation">Nhập lại mật khẩu</label>
            <div class="pw-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password">
                <button type="button" class="pw-toggle" data-target="password_confirmation" aria-label="Hiện mật khẩu" title="Hiện/ẩn mật khẩu">
                    <svg class="icon-eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="icon-eye-off" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"></path>
                        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            <?php if (!empty($errors['password_confirmation'])): ?>
                <p class="error"><?= e($errors['password_confirmation']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-block mt-2">Tạo tài khoản</button>
    </form>

    <p style="margin-top:1.25rem;text-align:center;font-size:0.95rem;">
        Đã có tài khoản? <a href="<?= e(url('auth/login')) ?>">Đăng nhập</a>
    </p>
</section>

<style>
.pw-wrap { position: relative; display: block; }
.pw-wrap input {
    width: 100%;
    padding-right: 2.75rem;
    box-sizing: border-box;
}
.pw-toggle {
    position: absolute;
    right: 0.35rem;
    top: 50%;
    transform: translateY(-50%);
    width: 2rem;
    height: 2rem;
    border: none;
    background: transparent;
    color: #7a6555;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}
.pw-toggle:hover { color: var(--primary, #5c4033); background: rgba(0,0,0,.05); }
.pw-toggle svg { display: none; flex-shrink: 0; }
.pw-toggle .icon-eye { display: block; }
.pw-toggle.is-shown .icon-eye { display: none; }
.pw-toggle.is-shown .icon-eye-off { display: block; }
</style>
<script>
document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(btn.getAttribute('data-target'));
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.classList.toggle('is-shown', show);
        btn.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
    });
});
</script>