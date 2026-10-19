<?php

// Sao chép file này thành config/database.php rồi sửa theo máy của bạn.
// config/database.php bị .gitignore bỏ qua - KHÔNG commit mật khẩu thật.
return [
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'dbname'   => 'thuvien_mini',
    'username' => 'root',
    'password' => '',          // WampServer mặc định để trống
    'charset'  => 'utf8mb4',
];
