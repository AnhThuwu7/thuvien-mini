<?php

return [
    // --- CHUNG ---
    ['label' => 'Trang chủ',       'path' => '',                'controller' => 'HomeController',       'roles' => ['*'], 'group' => 'Chung', 'icon' => '🏠'],
    ['label' => 'Tra cứu sách',    'path' => 'catalog',         'controller' => 'CatalogController',    'roles' => ['*'], 'group' => 'Chung', 'icon' => '🔍'],

    // --- ĐỘC GIẢ (Giữ nguyên) ---
    ['label' => 'Lịch sử mượn',    'path' => 'loans/mine',      'controller' => 'LoansController',      'roles' => ['doc_gia'], 'group' => 'Độc giả', 'icon' => '📖'],
    ['label' => 'Yêu cầu mượn',    'path' => 'loan-requests/mine','controller' => 'LoanRequestsController','roles' => ['doc_gia'], 'group' => 'Độc giả', 'icon' => '📋'],

    // --- BÁO CÁO & THỐNG KÊ (Nghiệp vụ / Quản trị) ---
    ['label' => 'Thống kê',        'path' => 'reports/stats',   'controller' => 'ReportsController',    'roles' => ['thu_thu', 'quan_tri'], 'group' => 'Báo cáo', 'icon' => '📊'],

    // --- NGHIỆP VỤ MƯỢN TRẢ & KHO SÁCH (Thu thư & Admin) ---
    ['label' => 'Mượn – Trả',       'path' => 'loans',           'controller' => 'LoansController',      'roles' => ['thu_thu', 'quan_tri'], 'group' => 'Nghiệp vụ', 'icon' => '🔄'],
    ['label' => 'Yêu cầu giữ chỗ', 'path' => 'loan-requests',   'controller' => 'LoanRequestsController','roles' => ['thu_thu', 'quan_tri'], 'group' => 'Nghiệp vụ', 'icon' => '📋'],
    ['label' => 'Quá hạn',         'path' => 'reports/overdue', 'controller' => 'ReportsController',    'roles' => ['thu_thu', 'quan_tri'], 'group' => 'Nghiệp vụ', 'icon' => '⚠️'],
    ['label' => 'Quản lý sách',    'path' => 'books',           'controller' => 'BooksController',      'roles' => ['thu_thu', 'quan_tri'], 'group' => 'Kho sách', 'icon' => '📚'],
    ['label' => 'Danh mục',        'path' => 'categories',      'controller' => 'CategoriesController', 'roles' => ['thu_thu', 'quan_tri'], 'group' => 'Kho sách', 'icon' => '🗂️'],
    ['label' => 'Bản sao',         'path' => 'copies',          'controller' => 'CopiesController',     'roles' => ['thu_thu', 'quan_tri'], 'group' => 'Kho sách', 'icon' => '📦'],

    // --- QUẢN TRỊ HỆ THỐNG (Chỉ Admin) ---
    ['label' => 'Tài khoản',       'path' => 'users',           'controller' => 'UsersController',      'roles' => ['quan_tri'], 'group' => 'Quản trị', 'icon' => '👥'],
    ['label' => 'Cấu hình',        'path' => 'settings',        'controller' => 'SettingsController',   'roles' => ['quan_tri'], 'group' => 'Quản trị', 'icon' => '⚙️'],
];