<section class="card home-welcome">
    <?php if (Auth::check()): ?>
        <h1>Chào mừng tới Thư viện Mini</h1>
        <p class="home-lead">
            Xin chào <strong><?= e(Auth::user()['ho_ten']) ?></strong>
            – vai trò <strong><?= e(Auth::roleLabel()) ?></strong>.
        </p>
        <p class="home-sub">Chọn chức năng trên thanh menu để bắt đầu làm việc.</p>
    <?php else: ?>
        <h1>Chào mừng tới Thư viện Mini</h1>
        <p class="home-lead">
            Hệ thống hỗ trợ tra cứu sách, mượn – trả và quản lý thư viện.
        </p>
        <p class="home-actions">
            <a class="btn" href="<?= e(url('auth/login')) ?>">Đăng nhập</a>
        </p>
    <?php endif; ?>
</section>