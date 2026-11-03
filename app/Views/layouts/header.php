<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Thư viện mini') ?></title>
    <link rel="stylesheet" href="<?= e(url('css/app.css')) ?>?v=4">
    <style>
        /* Khắc phục lỗi thanh cuộn ngang làm che mất dropdown */
        .navbar, .nav-container, .nav-main-links {
            overflow: visible !important;
        }

        .nav-dropdown { 
            position: relative; 
            display: inline-block; 
        }
        .nav-dropdown > .nav-link { 
            cursor: pointer; 
            user-select: none;
        }
        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 200px;
            background: #fff;
            border: 1px solid #e5ddd3;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(60,40,20,.12);
            padding: 0.4rem 0;
            z-index: 1000;
        }
        /* Hiển thị dropdown khi hover, focus hoặc có class active khi click */
        .nav-dropdown:hover > .nav-dropdown-menu,
        .nav-dropdown:focus-within > .nav-dropdown-menu,
        .nav-dropdown.active > .nav-dropdown-menu { 
            display: block; 
        }
        .nav-dropdown-menu a {
            display: block;
            padding: 0.55rem 1rem;
            color: #3d2b1f;
            text-decoration: none;
            font-size: 0.92rem;
            white-space: nowrap;
        }
        .nav-dropdown-menu a:hover { background: #f5f0eb; }
        .nav-dropdown-menu .dd-label {
            padding: 0.35rem 1rem 0.2rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9a8778;
            font-weight: 600;
        }

        /* Style nhóm thu gọn gọn gàng cho Drawer Menu */
        .drawer-group {
            margin-bottom: 0.5rem;
        }
        .drawer-group summary {
            padding: 0.6rem 0.8rem;
            font-weight: 600;
            font-size: 0.82rem;
            color: #7a6858;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: .04em;
            list-style: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
        }
        .drawer-group summary::-webkit-details-marker { display: none; }
        .drawer-group summary::after { content: '▾'; font-size: 0.8rem; }
        .drawer-group[open] summary::after { content: '▴'; }
        .drawer-sublinks {
            padding-left: 0.4rem;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="nav-container">
        <a href="<?= e(url('')) ?>" class="nav-brand">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            <span>Thư viện mini</span>
        </a>

        <?php if (!Auth::check()): ?>
            <div class="nav-actions">
                <a href="<?= e(url('auth/login')) ?>" class="btn btn-outline" style="padding:0.4rem 0.9rem;">Đăng nhập</a>
                <a href="<?= e(url('auth/register')) ?>" class="btn" style="padding:0.4rem 0.9rem;">Đăng ký</a>
            </div>
        <?php else: ?>
            <?php $role = Auth::user()['vai_tro'] ?? ''; ?>

            <nav class="nav-main-links">
                <?php if ($role === 'doc_gia'): ?>
                    <a href="<?= e(url('catalog')) ?>" class="nav-link"><span class="icon">🔍</span> Tra cứu sách</a>
                    <a href="<?= e(url('loans/mine')) ?>" class="nav-link"><span class="icon">📖</span> Sách đang mượn</a>
                    <a href="<?= e(url('loan-requests/mine')) ?>" class="nav-link"><span class="icon">📋</span> Yêu cầu mượn</a>

                <?php elseif ($role === 'thu_thu' || $role === 'quan_tri'): ?>
                    <a href="<?= e(url('reports/stats')) ?>" class="nav-link"><span class="icon">📊</span> Thống kê</a>

                    <div class="nav-dropdown">
                        <span class="nav-link" tabindex="0">📚 Nghiệp vụ ▾</span>
                        <div class="nav-dropdown-menu">
                            <div class="dd-label">Mượn – Trả</div>
                            <a href="<?= e(url('loans')) ?>">Xử lý mượn – trả</a>
                            <a href="<?= e(url('loans/history')) ?>">Lịch sử mượn</a>
                            <a href="<?= e(url('loan-requests')) ?>">Yêu cầu giữ chỗ</a>
                            <a href="<?= e(url('reports/overdue')) ?>">Quá hạn</a>
                            <div class="dd-label">Kho sách</div>
                            <a href="<?= e(url('books')) ?>">Quản lý sách</a>
                            <a href="<?= e(url('categories')) ?>">Danh mục</a>
                            <a href="<?= e(url('copies')) ?>">Bản sao</a>
                        </div>
                    </div>

                    <?php if ($role === 'quan_tri'): ?>
                    <div class="nav-dropdown">
                        <span class="nav-link" tabindex="0">⚙️ Quản trị ▾</span>
                        <div class="nav-dropdown-menu">
                            <a href="<?= e(url('users')) ?>">Tài khoản</a>
                            <a href="<?= e(url('settings')) ?>">Cấu hình</a>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="nav-actions">
                <div class="user-avatar-small" title="<?= e(Auth::user()['ho_ten'] ?? '') ?>">
                    <?= mb_strtoupper(mb_substr(Auth::user()['ho_ten'] ?? 'U', 0, 1)) ?>
                </div>
                <button type="button" class="btn-hamburger" id="hamburgerBtn" aria-label="Menu">
                    <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
</header>

<?php if (Auth::check()): ?>
<div class="drawer-overlay" id="drawerOverlay"></div>
<aside class="drawer-menu" id="drawerMenu">
    <div class="drawer-header">
        <h3>Menu hệ thống</h3>
        <button type="button" class="btn-close-drawer" id="closeDrawerBtn">&times;</button>
    </div>
    <div class="drawer-user-card">
        <div class="avatar-circle-large">
            <?= mb_strtoupper(mb_substr(Auth::user()['ho_ten'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="drawer-user-info">
            <strong><?= e(Auth::user()['ho_ten'] ?? '') ?></strong>
            <small><?= e(Auth::roleLabel(Auth::user()['vai_tro'] ?? '')) ?></small>
        </div>
    </div>
    <div class="drawer-links">
        <a href="<?= e(url('auth/profile')) ?>" class="drawer-item"><span class="icon">👤</span> Hồ sơ cá nhân</a>

        <?php if ((Auth::user()['vai_tro'] ?? '') === 'doc_gia'): ?>
            <div class="drawer-divider">Độc giả</div>
            <a href="<?= e(url('catalog')) ?>" class="drawer-item"><span class="icon">🔍</span> Tra cứu sách</a>
            <a href="<?= e(url('loans/mine')) ?>" class="drawer-item"><span class="icon">📖</span> Sách đang mượn</a>
            <a href="<?= e(url('loan-requests/mine')) ?>" class="drawer-item"><span class="icon">📋</span> Yêu cầu mượn</a>
        <?php endif; ?>

        <?php if (in_array(Auth::user()['vai_tro'] ?? '', ['thu_thu', 'quan_tri'], true)): ?>
            <div class="drawer-divider">Quản lý</div>
            <a href="<?= e(url('reports/stats')) ?>" class="drawer-item"><span class="icon">📊</span> Thống kê</a>

            <details class="drawer-group" open>
                <summary>📚 Nghiệp vụ</summary>
                <div class="drawer-sublinks">
                    <a href="<?= e(url('loans')) ?>" class="drawer-item"><span class="icon">🔄</span> Xử lý mượn – trả</a>
                    <a href="<?= e(url('loans/history')) ?>" class="drawer-item"><span class="icon">📜</span> Lịch sử mượn</a>
                    <a href="<?= e(url('loan-requests')) ?>" class="drawer-item"><span class="icon">📋</span> Yêu cầu giữ chỗ</a>
                    <a href="<?= e(url('reports/overdue')) ?>" class="drawer-item"><span class="icon">⚠️</span> Quá hạn</a>
                    <a href="<?= e(url('books')) ?>" class="drawer-item"><span class="icon">📚</span> Quản lý sách</a>
                    <a href="<?= e(url('categories')) ?>" class="drawer-item"><span class="icon">🗂️</span> Danh mục</a>
                    <a href="<?= e(url('copies')) ?>" class="drawer-item"><span class="icon">📦</span> Bản sao</a>
                </div>
            </details>
        <?php endif; ?>

        <?php if ((Auth::user()['vai_tro'] ?? '') === 'quan_tri'): ?>
            <details class="drawer-group" open>
                <summary>⚙️ Quản trị</summary>
                <div class="drawer-sublinks">
                    <a href="<?= e(url('users')) ?>" class="drawer-item"><span class="icon">👥</span> Tài khoản</a>
                    <a href="<?= e(url('settings')) ?>" class="drawer-item"><span class="icon">⚙️</span> Cấu hình</a>
                </div>
            </details>
        <?php endif; ?>

        <div class="drawer-divider"></div>
        <form action="<?= e(url('auth/logout')) ?>" method="POST" style="margin:0;">
            <?= csrf_field() ?>
            <button type="submit" class="drawer-item logout" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;font-family:inherit;">
                <span class="icon">↪</span> Đăng xuất
            </button>
        </form>
    </div>
</aside>
<script>
(function () {
    // Xử lý Drawer Menu
    var btn = document.getElementById('hamburgerBtn');
    var drawer = document.getElementById('drawerMenu');
    var overlay = document.getElementById('drawerOverlay');
    var closeBtn = document.getElementById('closeDrawerBtn');
    function openDrawer() { if (drawer) drawer.classList.add('open'); if (overlay) overlay.classList.add('active'); }
    function closeDrawer() { if (drawer) drawer.classList.remove('open'); if (overlay) overlay.classList.remove('active'); }
    if (btn) btn.addEventListener('click', openDrawer);
    if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

    // Xử lý click sổ xuống cho Dropdown Header
    var dropdowns = document.querySelectorAll('.nav-dropdown');
    dropdowns.forEach(function (dd) {
        var link = dd.querySelector('.nav-link');
        if (link) {
            link.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdowns.forEach(function (other) {
                    if (other !== dd) other.classList.remove('active');
                });
                dd.classList.toggle('active');
            });
        }
    });

    document.addEventListener('click', function () {
        dropdowns.forEach(function (dd) {
            dd.classList.remove('active');
        });
    });
})();
</script>
<?php endif; ?>

<main class="container page-content">
<?php
$flashes = function_exists('flash_pull') ? flash_pull() : [];
if ($flashes):
?>
<div class="flash-wrap">
    <?php foreach ($flashes as $item): ?>
        <div class="alert alert-<?= e($item['type']) ?>"><?= e($item['message']) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>