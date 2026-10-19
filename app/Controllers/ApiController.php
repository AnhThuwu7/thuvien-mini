<?php

class ApiController extends Controller
{
    // GET /api/users?q=keyword
    public function users(): void
    {
        // Cho phép Thủ thư và Quản trị truy cập API
        $this->requireRole('thu_thu', 'quan_tri');

        $keyword = input('q');
        $userModel = $this->model('User');
        $readers = $userModel->searchReadersForApi($keyword);

        $this->jsonSuccess($readers);
    }
}