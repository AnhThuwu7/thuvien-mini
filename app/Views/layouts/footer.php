</main> <!-- Đóng page-content container -->

<?php if (Auth::check()): ?>
<!-- CHỈ HIỆN FOOTER KHI ĐÃ ĐĂNG NHẬP VÀO TRANG QUẢN LÝ -->
<footer class="app-footer">
    <p>Hệ thống Quản lý Thư viện Mini – Học phần Lập trình Web</p>
</footer>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const drawerMenu = document.getElementById('drawerMenu');

    function openMenu() {
        if (drawerMenu && drawerOverlay) {
            drawerMenu.classList.add('open');
            drawerOverlay.classList.add('active');
        }
    }

    function closeMenu() {
        if (drawerMenu && drawerOverlay) {
            drawerMenu.classList.remove('open');
            drawerOverlay.classList.remove('active');
        }
    }

    if (hamburgerBtn) hamburgerBtn.addEventListener('click', openMenu);
    if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeMenu);
    if (drawerOverlay) drawerOverlay.addEventListener('click', closeMenu);
});
</script>

</body>
</html>