<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Thư viện Mini') ?></title>
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7fa; color: #173b57; }
        .box { max-width: 640px; margin: 60px auto; padding: 28px; background: #fff; border: 1px solid #d5dde4; border-radius: 14px; }
        a { color: #1a5fb4; }
    </style>
</head>
<body>
<main class="box">
    <h1>Thư viện Mini</h1>
    <p>Khung MVC đã chạy. Đây là trang tạm thời, TV4 sẽ thay bằng layout chung.</p>
    <ul>
        <li><a href="<?= e(url('home/ping')) ?>">Kiểm tra kết nối CSDL (trả JSON)</a></li>
        <li><a href="<?= e(url('trang-khong-ton-tai')) ?>">Thử trang 404</a></li>
    </ul>
</main>
</body>
</html>
