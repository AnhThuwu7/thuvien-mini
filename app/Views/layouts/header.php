<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Thư viện mini') ?></title>
    <link rel="stylesheet" href="<?= e(url('css/app.css')) ?>">
</head>
<body>

<!-- TOP NAVBAR CHUẨN OPEN LIBRARY -->
<header class="navbar">
    <div class="nav-container">
        <!-- Logo & Tên hệ thống -->
        <a href="<?= e(url('')) ?>" class="nav-brand">
            <svg class="brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
            <span>Thư viện mini</span>
        </a>

        <?php if (Auth::check()): ?>
            <?php $role = Auth::user()['vai_tro'] ?? ''; ?>
            
            <!-- 2 NÚT TÍNH NĂNG CHÍNH ĐỔI THEO VAI TRÒ -->
            <nav class="nav-main-links">
                <?php if ($role === 'doc_gia'): ?>
                    <a href="<?= e(url('catalog')) ?>" class="nav-link">
                        <span class="icon">🔍</span> Tra cứu sách
                    </a>
                    <a href="<?= e(url('loans/mine')) ?>" class="nav-link">
                        <span class="icon">📖</span> Sách đang mượn
                    </a>

                <?php elseif ($role === 'thu_thu'): ?>
                    <a href="<?= e(url('loans')) ?>" class="nav-link">
                        <span class="icon">🔄</span> Xử lý Mượn - Trả
                    </a>
                    <a href="<?= e(url('books')) ?>" class="nav-link">
                        <span class="icon">📚</span> Quản lý kho sách
                    </a>

                <?php elseif ($role === 'quan_tri'): ?>
                    <a href="<?= e(url('users')) ?>" class="nav-link">
                        <span class="icon">👥</span> Quản lý tài khoản
                    </a>
                    <a href="<?= e(url('settings')) ?>" class="nav-link">
                        <span class="icon">⚙️</span> Cấu hình hệ thống
                    </a>
                <?php endif; ?>
            </nav>

            <!-- GÓC PHẢI: AVATAR & NÚT BA GẠCH (☰) -->
            <div class="nav-actions">
                <div class="user-avatar-small" title="<?= e(Auth::user()['ho_ten']) ?>">
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
<!-- MENU ẨN 3 GẠCH (DRAWER SLIDE-OUT) -->
<div class="drawer-overlay" id="drawerOverlay"></div>
<aside class="drawer-menu" id="drawerMenu">
    <div class="drawer-header">
        <h3>Menu hệ thống</h3>
        <button type="button" class="btn-close-drawer" id="closeDrawerBtn">&times;</button>
    </div>

    <!-- Thông tin người dùng -->
    <div class="drawer-user-card">
        <div class="avatar-circle-large">
            <?= mb_strtoupper(mb_substr(Auth::user()['ho_ten'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="drawer-user-info">
            <strong><?= e(Auth::user()['ho_ten']) ?></strong>
            <small><?= e(Auth::roleLabel(Auth::user()['vai_tro'])) ?></small>
        </div>
    </div>

    <!-- Danh sách liên kết -->
    <div class="drawer-links">
        <a href="<?= e(url('auth/profile')) ?>" class="drawer-item">
            <span class="icon">👤</span> Hồ sơ cá nhân
        </a>

        <?php if (Auth::user()['vai_tro'] === 'quan_tri'): ?>
            <div class="drawer-divider">Quản trị viên</div>
            <a href="<?= e(url('users')) ?>" class="drawer-item">
                <span class="icon">👥</span> Quản lý tài khoản
            </a>
            <a href="<?= e(url('settings')) ?>" class="drawer-item">
                <span class="icon">⚙️</span> Cấu hình hệ thống
            </a>
        <?php endif; ?>

        <div class="drawer-divider"></div>

        <form action="<?= e(url('auth/logout')) ?>" method="POST" style="margin: 0;">
            <?= csrf_field() ?>
            <button type="submit" class="drawer-item logout" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left; font-family: inherit;">
                <span class="icon">↪</span> Đăng xuất
            </button>
        </form>
    </div>
</aside>
<?php endif; ?>
<main class="container page-content">

<?php
$flashes = flash_all();
if ($flashes):
?>
<div class="flash-wrap">
    <?php foreach ($flashes as $item): ?>
        <div class="alert alert-<?= e($item['type']) ?>"><?= e($item['message']) ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>