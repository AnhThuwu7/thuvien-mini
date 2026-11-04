<?php
/**
 * @var string $email
 * @var array $errors
 */
?>
<section class="card narrow">
    <h1>Đăng nhập</h1>

    <?php if (!empty($errors['login'])): ?>
        <div class="alert alert-danger"><?= e($errors['login']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('auth/login')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email ?? '') ?>"
                   autocomplete="username" required autofocus placeholder="Nhập địa chỉ email...">
            <?php if (!empty($errors['email'])): ?>
                <p class="error"><?= e($errors['email']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="password">Mật khẩu</label>
            <div class="pw-wrap">
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required placeholder="Nhập mật khẩu...">
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
            <?php if (!empty($errors['password'])): ?>
                <p class="error"><?= e($errors['password']) ?></p>
            <?php endif; ?>
        </div>

        <p style="margin:0.35rem 0 0.75rem;text-align:right;font-size:0.9rem;">
            <a href="<?= e(url('auth/forgot')) ?>">Quên mật khẩu?</a>
        </p>

        <button type="submit" class="btn btn-block mt-2">Đăng nhập</button>
    </form>

    <p style="margin-top:1.25rem;text-align:center;font-size:0.95rem;">
        Chưa có tài khoản? <a href="<?= e(url('auth/register')) ?>">Đăng ký độc giả</a>
    </p>

    <?php if (defined('APP_DEBUG') && APP_DEBUG): ?>
        <div class="hint">
            <strong>Tài khoản demo</strong> (mật khẩu chung: <code>Demo@123</code>)<br>
            admin@example.com (Quản trị) · thuthu1@example.com (Thủ thư)<br>
            docgia01@example.com (Độc giả) · docgia04@example.com (Độc giả bị khóa)
        </div>
    <?php endif; ?>
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