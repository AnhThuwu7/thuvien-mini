<section class="card narrow">
    <h1>Đăng nhập</h1>

    <?php if (!empty($errors['login'])): ?>
        <div class="alert alert-danger"><?= e($errors['login']) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('auth/login')) ?>" novalidate>
        <?= csrf_field() ?>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" autocomplete="username" required autofocus>
        <?php if (!empty($errors['email'])): ?>
            <p class="error"><?= e($errors['email']) ?></p>
        <?php endif; ?>

        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>
        <?php if (!empty($errors['password'])): ?>
            <p class="error"><?= e($errors['password']) ?></p>
        <?php endif; ?>

        <button type="submit" class="btn">Đăng nhập</button>
    </form>

    <?php if (APP_DEBUG): ?>
        <div class="hint">
            <strong>Tài khoản demo</strong> (mật khẩu chung: <code>Demo@123</code>) – chỉ hiện khi APP_DEBUG = true<br>
            admin@example.com (Quản trị) · thuthu1@example.com (Thủ thư) · docgia01@example.com (Độc giả)<br>
            docgia04@example.com (Độc giả bị khóa)
        </div>
    <?php endif; ?>
</section>
