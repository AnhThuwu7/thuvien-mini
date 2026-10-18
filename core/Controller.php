<?php

class Controller
{
    // Gọi model trong app/Models. CHỈ truyền tên cố định trong code, KHÔNG truyền dữ liệu người dùng.
    protected function model(string $model)
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $model)) {
            throw new InvalidArgumentException('Tên model không hợp lệ.');
        }
        require_once __DIR__ . '/../app/Models/' . $model . '.php';
        return new $model();
    }

    // Gọi view trong app/Views, ví dụ view('sach/danh_sach', ['sachs' => $sachs])
    protected function view(string $view, array $data = []): void
    {
        if (!preg_match('#^[A-Za-z0-9_/]+$#', $view)) {
            throw new InvalidArgumentException('Tên view không hợp lệ.');
        }

        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            error_log('[View] Không tìm thấy view: ' . $view);
            abort(500, (defined('APP_DEBUG') && APP_DEBUG) ? 'Không tìm thấy view: ' . $view : '');
        }

        // EXTR_SKIP: không cho dữ liệu ghi đè biến có sẵn ($view, $data, $viewFile)
        extract($data, EXTR_SKIP);
        // require (không phải require_once) để cùng một view/partial dùng được nhiều lần
        require $viewFile;
    }

    // Chuyển hướng trong ứng dụng: redirect('sach'), redirect('sach?page=2').
    // Luôn nối với url() nên không thể bị lợi dụng để chuyển sang website khác.
    protected function redirect(string $path = ''): void
    {
        header('Location: ' . url($path));
        exit;
    }

    // ---------------- JSON cho Fetch API ----------------
    // Quy ước chung: thành công {"ok":true,"data":...}, lỗi {"ok":false,"error":"..."}

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function jsonSuccess($data = null, int $status = 200): void
    {
        $this->json(['ok' => true, 'data' => $data], $status);
    }

    protected function jsonError(string $message, int $status = 400): void
    {
        $this->json(['ok' => false, 'error' => $message], $status);
    }

    // ---------------- Bảo vệ thao tác ghi dữ liệu ----------------

    // Chỉ cho phép POST (đặt ở đầu các method thêm/sửa/xóa)
    protected function requirePost(): void
    {
        if (!is_post()) {
            header('Allow: POST');
            abort(405);
        }
    }

    // Từ chối nếu token CSRF sai/thiếu (đặt ngay sau requirePost())
    protected function requireCsrf(): void
    {
        if (!csrf_verify()) {
            abort(403, 'Phiên làm việc đã hết hạn hoặc yêu cầu không hợp lệ. Vui lòng tải lại trang và thử lại.');
        }
    }
}
