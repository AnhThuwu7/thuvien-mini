<?php
/**
 * Giữ lại để tương thích URL cũ /stats.
 * Chuyển hướng sang ReportsController (TV5).
 */
require_once __DIR__ . '/../../core/Controller.php';

class StatsController extends Controller
{
    public function index(): void
    {
        // Giữ kiểm tra quyền trước khi redirect
        $this->requireRole('thu_thu', 'quan_tri');
        $this->redirect(url('reports/stats'));
    }
}