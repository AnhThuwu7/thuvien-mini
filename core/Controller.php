<?php

class Controller
{
    // Nạp model: $this->model('User')  ->  app/Models/User.php (class User)
    protected function model(string $model)
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $model)) {
            throw new InvalidArgumentException('Tên model không hợp lệ.');
        }
        require_once ROOT_PATH . '/app/Models/' . $model . '.php';
        return new $model();
    }

    // Hiển thị view trong layout chung (header + footer).
    // $this->view('books/index', ['title' => 'Quản lý sách', 'books' => $books]);
    // $withLayout = false: view tự lo toàn bộ trang (ví dụ trang lỗi).
    // Đừng đặt khóa dữ liệu tên: view, data, viewFile, withLayout.
    protected function view(string $view, array $data = [], bool $withLayout = true): void
    {
        $viewFile = ROOT_PATH . '/app/Views/' . $view . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            die(APP_DEBUG ? "Không tìm thấy view: $view" : 'Lỗi hệ thống.');
        }

        // Trang của người đã đăng nhập không được lưu cache (bấm Back sau khi đăng xuất không thấy lại)
        if (Auth::check() && !headers_sent()) {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }

        extract($data, EXTR_SKIP);

        if ($withLayout) {
            require ROOT_PATH . '/app/Views/layouts/header.php';
        }
        require $viewFile;
        if ($withLayout) {
            require ROOT_PATH . '/app/Views/layouts/footer.php';
        }
    }

    // Chuyển hướng: $this->redirect(url('books'))
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    // Trả JSON (cho Fetch API): $this->json(['ok' => true]); $this->json(['error' => '...'], 404);
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Đầu mỗi action thay đổi dữ liệu: chỉ chấp nhận POST
    protected function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            die('Phương thức không được hỗ trợ. Thao tác này chỉ chấp nhận POST.');
        }
    }

    // Bắt buộc đăng nhập. $json = true cho endpoint Fetch: trả 401 thay vì chuyển trang.
    protected function requireLogin(bool $json = false): void
    {
        if (Auth::check()) {
            return;
        }
        if ($json) {
            $this->json(['error' => 'Chưa đăng nhập.'], 401);
        }
        Auth::rememberIntended();
        flash('warning', 'Vui lòng đăng nhập để tiếp tục.');
        $this->redirect(url('auth/login'));
    }

    // Bắt buộc đúng vai trò: $this->requireRole('thu_thu', 'quan_tri');  (sai quyền -> 403)
    protected function requireRole(string ...$roles): void
    {
        $this->requireLogin();
        if (!Auth::hasRole(...$roles)) {
            $this->abort(403);
        }
    }

    // $this->abort(404) khi không tìm thấy dữ liệu (ví dụ ID không tồn tại); $this->abort(403) khi sai quyền
    protected function abort(int $code = 404, string $message = ''): void
    {
        http_response_code($code === 403 ? 403 : 404);
        $this->view($code === 403 ? 'errors/403' : 'errors/404', ['message' => $message], false);
        exit;
    }
    /** Trả về JSON thành công */
    protected function jsonSuccess(array $data = [], int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** Trả về JSON lỗi */
    protected function jsonError(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
   