<?php
$title   = $title ?? 'Thư viện mini';
$menu    = require ROOT_PATH . '/config/menu.php';
$flashes = flash_pull();
function flash_pull(): array
{
    return flash_all();
}
$me      = Auth::user();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= e(Csrf::token()) ?>">
    <title><?= e($title) ?> | Thư viện mini</title>
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body>
<header class="topbar">
    <a class="brand" href="<?= e(url()) ?>">📚 Thư viện mini</a>
    <nav class="menu">
        <?php foreach ($menu as $item): ?>
            <?php if (Auth::canSee($item['roles']) && is_file(ROOT_PATH . '/app/Controllers/' . $item['controller'] . '.php')): ?>
                <a href="<?= e(url($item['path'])) ?>"><?= e($item['label']) ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <div class="user">
        <?php if ($me): ?>
            <a href="<?= e(url('auth/profile')) ?>"><?= e($me['ho_ten']) ?> (<?= e(Auth::roleLabel()) ?>)</a>
            <form method="post" action="<?= e(url('auth/logout')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-small">Đăng xuất</button>
            </form>
        <?php else: ?>
            <a href="<?= e(url('auth/login')) ?>">Đăng nhập</a>
        <?php endif; ?>
    </div>
</header>
<main class="container">
    <?php foreach ($flashes as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
