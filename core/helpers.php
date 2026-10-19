<?php
/**
 * Hàm tiện ích dùng chung cho toàn dự án.
 * Được nạp từ public/index.php SAU khi session_start().
 */

// ---------------------------------------------------------------------
// Dữ liệu "dùng một lần": đặt ở request trước, chỉ còn ở request kế tiếp
// ---------------------------------------------------------------------
$_SESSION['_old'] = $_SESSION['_old_next'] ?? [];
unset($_SESSION['_old_next']);

// ---------------------------------------------------------------------
// Output & URL
// ---------------------------------------------------------------------

/** Escape dữ liệu trước khi in ra HTML (chống XSS). LUÔN dùng khi in dữ liệu động trong View. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Đường dẫn gốc của ứng dụng, ví dụ /thuvien-mini/public (không có dấu / cuối). */
function base_url(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    return rtrim($dir, '/');
}

/** Tạo URL trong ứng dụng: url('sach/chitiet/5'), url('css/style.css'). Nhớ bọc e(): e(url(...)) */
function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

/** Đường dẫn tới tệp tĩnh trong thư mục public (css, js, img): asset('css/app.css') */
function asset(string $path): string
{
    return base_url() . '/' . ltrim($path, '/');
}

// ---------------------------------------------------------------------
// Request
// ---------------------------------------------------------------------

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/** Lấy giá trị chuỗi từ POST (ưu tiên) hoặc GET, đã trim. Không phải chuỗi -> trả $default. */
function input(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

/** Yêu cầu này có mong đợi JSON không (Fetch/AJAX hoặc URL bắt đầu bằng api/). */
function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $ajax   = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    $url    = $_GET['url'] ?? '';
    $url    = is_string($url) ? trim($url, '/') : '';
    $isApi  = $url === 'api' || strpos($url, 'api/') === 0;

    return strpos($accept, 'application/json') !== false || $ajax || $isApi;
}

/**
 * Dừng xử lý và hiện trang lỗi (hoặc JSON nếu là yêu cầu API).
 * $message là câu thông báo AN TOÀN cho người dùng, không đưa chi tiết kỹ thuật vào đây.
 */
function abort(int $code, string $message = ''): void
{
    $defaults = [
        403 => 'Bạn không có quyền thực hiện thao tác này.',
        404 => 'Không tìm thấy trang bạn yêu cầu.',
        405 => 'Phương thức yêu cầu không được hỗ trợ.',
        500 => 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại sau.',
    ];
    $message = $message !== '' ? $message : ($defaults[$code] ?? 'Đã xảy ra lỗi.');

    http_response_code($code);

    if (wants_json()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $view = __DIR__ . '/../app/Views/errors/' . $code . '.php';
    if (is_file($view)) {
        require $view;
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo '<h1>' . $code . '</h1><p>' . e($message) . '</p>';
    }
    exit;
}

// ---------------------------------------------------------------------
// Thông báo flash và dữ liệu form cũ
// ---------------------------------------------------------------------

/**
 * flash('success', 'Đã lưu')  -> đặt thông báo (hiện ở request kế tiếp)
 * flash('success')            -> lấy thông báo ra và xóa. Loại: success, error, warning, info
 */
function flash(string $type, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$type] = $message;
        return null;
    }
    $value = $_SESSION['_flash'][$type] ?? null;
    unset($_SESSION['_flash'][$type]);
    return $value;
}

/** Lấy tất cả flash và xóa luôn (dùng trong layout/header) */
function flash_all(): array
{
    $raw = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);

    $result = [];
    foreach ($raw as $type => $message) {
        $result[] = [
            'type'    => $type,
            'message' => $message,
        ];
    }
    return $result;
}

/** Lưu dữ liệu form vừa nhập để điền lại khi báo lỗi (không lưu mật khẩu, CSRF). Dùng trước khi redirect. */
function set_old(array $input): void
{
    foreach (array_keys($input) as $key) {
        $k = (string) $key;
        if ($k === '_csrf' || stripos($k, 'mat_khau') !== false || stripos($k, 'password') !== false) {
            unset($input[$key]);
        }
    }
    $_SESSION['_old_next'] = $input;
}

/** Lấy lại giá trị đã nhập: <input value="<?= e(old('ho_ten')) ?>"> */
function old(string $key, string $default = ''): string
{
    $value = $_SESSION['_old'][$key] ?? $default;
    return is_string($value) ? $value : $default;
}

// ---------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------

function csrf_token(): string
{
    return Csrf::token();
}

/** Đặt trong MỌI form POST: <?= csrf_field() ?> */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(Csrf::token()) . '">';
}

/** Kiểm tra token (dùng khi cần kiểm tra thủ công) */
function csrf_verify(): bool
{
    return Csrf::verify();
}

// ---------------------------------------------------------------------
// Định dạng hiển thị & phân trang
// ---------------------------------------------------------------------

function format_date(?string $value, string $format = 'd/m/Y'): string
{
    if ($value === null || $value === '') {
        return '';
    }
    $time = strtotime($value);
    return $time === false ? '' : date($format, $time);
}

function format_datetime(?string $value): string
{
    return format_date($value, 'd/m/Y H:i');
}

function format_money($amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' đ';
}

/**
 * Tính phân trang từ tổng số dòng và tham số ?page= trên URL.
 * Trả về: page, per_page, total, total_pages, offset (đều là số nguyên -> an toàn để nối vào LIMIT/OFFSET).
 */
function paginate(int $total, int $perPage = 10): array
{
    $perPage    = max(1, $perPage);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page       = (int) ($_GET['page'] ?? 1);
    $page       = min(max(1, $page), $totalPages);

    return [
        'page'        => $page,
        'per_page'    => $perPage,
        'total'       => $total,
        'total_pages' => $totalPages,
        'offset'      => ($page - 1) * $perPage,
    ];
}