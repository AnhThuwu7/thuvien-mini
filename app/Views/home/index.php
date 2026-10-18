<section class="card">
    <h1>Hệ thống Quản lý Thư viện Mini</h1>
    <?php if (Auth::check()): ?>
        <p>Xin chào <strong><?= e(Auth::user()['ho_ten']) ?></strong> – bạn đang đăng nhập với vai trò
           <strong><?= e(Auth::roleLabel()) ?></strong>.</p>
        <p>Chọn chức năng ở thanh menu phía trên. Các mục sẽ xuất hiện dần khi từng thành viên hoàn thành phần việc của mình.</p>
    <?php else: ?>
        <p>Quản lý đầu sách, bản sao, độc giả và quy trình mượn – trả cho thư viện khoa.</p>
        <p><a class="btn" href="<?= e(url('auth/login')) ?>">Đăng nhập để bắt đầu</a></p>
    <?php endif; ?>
</section>
