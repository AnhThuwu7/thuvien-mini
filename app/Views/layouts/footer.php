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


<script>
/* Nhớ vị trí cuộn khi rời trang + nút Quay lại về đúng chỗ vừa xem */
(function () {
    var key = 'scroll:' + location.pathname + location.search;

    function saveScroll() {
        try { sessionStorage.setItem(key, String(window.scrollY || window.pageYOffset || 0)); } catch (e) {}
    }

    function restoreScroll() {
        try {
            var y = sessionStorage.getItem(key);
            if (y === null || y === '') return;
            var top = parseInt(y, 10);
            if (isNaN(top) || top <= 0) return;
            // Đợi layout xong rồi cuộn
            requestAnimationFrame(function () {
                window.scrollTo(0, top);
                setTimeout(function () { window.scrollTo(0, top); }, 50);
            });
        } catch (e) {}
    }

    window.addEventListener('pagehide', saveScroll);
    window.addEventListener('beforeunload', saveScroll);
    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a[href]');
        if (a && a.href && !a.getAttribute('target')) saveScroll();
    });
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restoreScroll);
    } else {
        restoreScroll();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('a.js-back').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                var fallback = a.getAttribute('data-fallback') || a.getAttribute('href') || '/';
                var canBack = window.history.length > 1 && document.referrer && document.referrer.indexOf(location.host) !== -1;
                if (canBack) {
                    history.back();
                } else {
                    location.href = fallback;
                }
            });
        });
    });
})();
</script>

</body>
</html>