<?php

class Auth
{
    private const ROLE_LABELS = [
        'doc_gia'  => 'Độc giả',
        'thu_thu'  => 'Thủ thư',
        'quan_tri' => 'Quản trị viên',
    ];

    // Gọi sau khi đã xác thực mật khẩu thành công
    public static function login(array $user): void
    {
        session_regenerate_id(true);          // chống session fixation
        $_SESSION['user'] = [                 // KHÔNG lưu mật khẩu băm
            'id'      => (int) $user['id'],
            'ho_ten'  => $user['ho_ten'],
            'email'   => $user['email'],
            'vai_tro' => $user['vai_tro'],
        ];
        unset($_SESSION['csrf_token']);       // token mới cho phiên đã đăng nhập
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);          // hủy session cũ, giữ 1 session trống để hiện thông báo
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

    // ['id', 'ho_ten', 'email', 'vai_tro'] hoặc null nếu chưa đăng nhập
    public static function user(): ?array
    {
        return self::check() ? $_SESSION['user'] : null;
    }

    public static function id(): ?int
    {
        return self::check() ? (int) $_SESSION['user']['id'] : null;
    }

    public static function role(): ?string
    {
        return self::check() ? $_SESSION['user']['vai_tro'] : null;
    }

    // Auth::hasRole('thu_thu', 'quan_tri')
    public static function hasRole(string ...$roles): bool
    {
        $role = self::role();
        return $role !== null && in_array($role, $roles, true);
    }

    // Dùng cho menu: '*' = tất cả mọi người (kể cả khách)
    public static function canSee(array $roles): bool
    {
        return in_array('*', $roles, true) || self::hasRole(...$roles);
    }

    public static function roleLabel(?string $role = null): string
    {
        $role = $role ?? self::role();
        return $role === null ? '' : (self::ROLE_LABELS[$role] ?? '');
    }

    // Ghi nhớ trang đang xem (chỉ GET) để quay lại sau khi đăng nhập
    public static function rememberIntended(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '';
        }
    }

    // Lấy URL đã ghi nhớ; chỉ chấp nhận đường dẫn nội bộ (chống open redirect)
    public static function pullIntended(): ?string
    {
        $url = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);

        if (!is_string($url) || $url === '' || $url[0] !== '/') {
            return null;
        }
        $second = $url[1] ?? '';
        if ($second === '/' || $second === '\\' || strpos($url, '/auth/') !== false) {
            return null;
        }
        return $url;
    }
}
