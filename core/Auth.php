<?php

class Auth
{
    private const ROLE_LABELS = [
        'doc_gia'  => 'Độc giả',
        'thu_thu'  => 'Thủ thư',
        'quan_tri' => 'Quản trị viên',
    ];

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'         => (int) $user['id'],
            'ho_ten'     => $user['ho_ten'],
            'email'      => $user['email'],
            'vai_tro'    => $user['vai_tro'],
            'bi_khoa'    => (int) ($user['bi_khoa'] ?? 0),
            'ly_do_khoa' => $user['ly_do_khoa'] ?? null,
        ];
        unset($_SESSION['csrf_token']);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

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

    public static function hasRole(string ...$roles): bool
    {
        $role = self::role();
        return $role !== null && in_array($role, $roles, true);
    }

    public static function canSee(array $roles): bool
    {
        return in_array('*', $roles, true) || self::hasRole(...$roles);
    }

    public static function roleLabel(?string $role = null): string
    {
        $role = $role ?? self::role();
        return $role === null ? '' : (self::ROLE_LABELS[$role] ?? '');
    }

    public static function rememberIntended(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
            $_SESSION['intended'] = $_SERVER['REQUEST_URI'] ?? '';
        }
    }

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