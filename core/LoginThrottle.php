<?php

/**
 * Giới hạn số lần đăng nhập sai (chống dò mật khẩu / brute-force).
 * - Mỗi cặp (email + IP): tối đa 5 lần sai trong 15 phút, sau đó bị chặn 15 phút.
 * - Mỗi IP: tối đa 20 lần sai trong 15 phút (chặn dò nhiều email).
 * Dữ liệu lưu dạng file JSON trong storage/login_attempts/ (không cần đổi CSDL).
 * Nếu không ghi được thư mục này thì bỏ qua giới hạn (không làm hỏng đăng nhập).
 */
class LoginThrottle
{
    private const WINDOW_SECONDS = 900;   // 15 phút
    private const LOCK_SECONDS   = 900;   // chặn 15 phút
    private const MAX_PER_EMAIL  = 5;
    private const MAX_PER_IP     = 20;

    /** Số giây còn bị chặn (0 = được phép đăng nhập). */
    public static function blockedSeconds(string $email): int
    {
        $wait = 0;
        foreach (self::keys($email) as [$key]) {
            $data = self::read($key);
            if ($data && ($data['locked_until'] ?? 0) > time()) {
                $wait = max($wait, (int) $data['locked_until'] - time());
            }
        }
        return $wait;
    }

    /** Ghi nhận một lần đăng nhập sai. */
    public static function fail(string $email): void
    {
        foreach (self::keys($email) as [$key, $max]) {
            self::update($key, static function (array $d) use ($max): array {
                $now = time();
                if (($d['first'] ?? 0) + self::WINDOW_SECONDS < $now) {
                    $d = ['count' => 0, 'first' => $now, 'locked_until' => 0];
                }
                $d['count'] = (int) ($d['count'] ?? 0) + 1;
                if ($d['count'] >= $max) {
                    $d['locked_until'] = $now + self::LOCK_SECONDS;
                    $d['count'] = 0;
                    $d['first'] = $now;
                }
                return $d;
            });
        }
    }

    /** Đăng nhập thành công: xóa bộ đếm của cả cặp email+IP và khóa theo IP. */
    public static function clear(string $email): void
    {
        foreach (self::keys($email) as [$key]) {
            $file = self::file($key);
            if ($file !== null && is_file($file)) {
                @unlink($file);
            }
        }
    }

    // ---------------------------------------------------------------

    /** @return array<int, array{0:string,1:int}> [khóa, ngưỡng] */
    private static function keys(string $email): array
    {
        $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $email = strtolower(trim($email));
        return [
            [hash('sha256', 'e|' . $email . '|' . $ip), self::MAX_PER_EMAIL],
            [hash('sha256', 'ip|' . $ip), self::MAX_PER_IP],
        ];
    }

    private static function dir(): ?string
    {
        $dir = ROOT_PATH . '/storage/login_attempts';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            error_log('[LoginThrottle] Không tạo được thư mục ' . $dir);
            return null;
        }
        return is_writable($dir) ? $dir : null;
    }

    private static function file(string $key): ?string
    {
        $dir = self::dir();
        return $dir === null ? null : $dir . '/' . $key . '.json';
    }

    private static function read(string $key): ?array
    {
        $file = self::file($key);
        if ($file === null || !is_file($file)) {
            return null;
        }
        $json = @file_get_contents($file);
        $data = $json !== false ? json_decode($json, true) : null;
        return is_array($data) ? $data : null;
    }

    private static function update(string $key, callable $fn): void
    {
        $file = self::file($key);
        if ($file === null) {
            return;
        }
        $fh = @fopen($file, 'c+');
        if (!$fh) {
            return;
        }
        if (flock($fh, LOCK_EX)) {
            $raw = stream_get_contents($fh);
            $data = $raw ? json_decode($raw, true) : [];
            $data = $fn(is_array($data) ? $data : []);
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, json_encode($data));
            fflush($fh);
            flock($fh, LOCK_UN);
        }
        fclose($fh);
    }
}