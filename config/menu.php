<?php

// Menu điều hướng. Mỗi người thêm / sửa dòng cho chức năng của mình.
//  'roles'      : vai trò được thấy mục này ('*' = tất cả, kể cả khách chưa đăng nhập)
//  'controller' : mục chỉ hiện khi file app/Controllers/<controller>.php đã tồn tại
return [
    ['label' => 'Trang chủ',       'path' => '',                'controller' => 'HomeController',       'roles' => ['*']],
    ['label' => 'Tra cứu sách',    'path' => 'catalog',         'controller' => 'CatalogController',    'roles' => ['doc_gia', 'thu_thu', 'quan_tri']],
    ['label' => 'Lịch sử mượn',    'path' => 'loans/mine',      'controller' => 'LoansController',      'roles' => ['doc_gia']],
    ['label' => 'Mượn – Trả',      'path' => 'loans',           'controller' => 'LoansController',      'roles' => ['thu_thu', 'quan_tri']],
    ['label' => 'Quản lý sách',    'path' => 'books',           'controller' => 'BooksController',      'roles' => ['thu_thu', 'quan_tri']],
    ['label' => 'Danh mục',        'path' => 'categories',      'controller' => 'CategoriesController', 'roles' => ['quan_tri']],
    ['label' => 'Quá hạn',         'path' => 'reports/overdue', 'controller' => 'ReportsController',    'roles' => ['thu_thu', 'quan_tri']],
    ['label' => 'Thống kê',        'path' => 'reports/stats',   'controller' => 'ReportsController',    'roles' => ['thu_thu', 'quan_tri']],
    ['label' => 'Tài khoản',       'path' => 'users',           'controller' => 'UsersController',      'roles' => ['quan_tri']],
    ['label' => 'Cấu hình',        'path' => 'settings',        'controller' => 'SettingsController',   'roles' => ['quan_tri']],
];
