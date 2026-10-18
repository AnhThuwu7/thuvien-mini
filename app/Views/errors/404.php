<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Thư viện Mini</title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7fa; color: #173b57; }
        .box { max-width: 520px; margin: 80px auto; padding: 32px; background: #fff; border: 1px solid #d5dde4; border-radius: 14px; text-align: center; }
        .code { font-size: 64px; font-weight: 700; margin: 0; color: #b26a00; }
        a { color: #1a5fb4; }
    </style>
</head>
<body>
<main class="box">
    <p class="code">404</p>
    <h1>Không tìm thấy trang</h1>
    <p><?= e($message ?? '') ?></p>
    <p><a href="<?= e(url('')) ?>">Về trang chủ</a></p>
</main>
</body>
</html>
