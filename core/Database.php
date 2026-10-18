<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'] ?? 3306,
            $config['dbname'],
            $config['charset'] ?? 'utf8mb4'
        );

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            // Cùng múi giờ với PHP (đặt date_default_timezone_set('Asia/Ho_Chi_Minh') trong public/index.php)
            $this->pdo->exec("SET time_zone = '+07:00'");
        } catch (PDOException $e) {
            // Không hiện chi tiết lỗi cho người dùng (có thể lộ host/tài khoản); ghi vào log của PHP
            error_log('[Database] ' . $e->getMessage());
            http_response_code(500);
            $debug = defined('APP_DEBUG') && APP_DEBUG;
            die($debug ? 'Lỗi kết nối database: ' . $e->getMessage() : 'Không thể kết nối cơ sở dữ liệu.');
        }
    }

    // Lấy thể hiện duy nhất (Singleton)
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Lấy đối tượng PDO để dùng
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}