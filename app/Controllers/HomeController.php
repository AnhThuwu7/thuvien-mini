<?php
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}

class HomeController extends Controller
{
    public function index(): void
    {
        if (Auth::check() && Auth::hasRole('thu_thu', 'quan_tri')) {
            $this->redirect(url('reports/stats'));
            return;
        }

        $bookModel = $this->model('Book');
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $books = $keyword !== ''
            ? $bookModel->search($keyword)
            : $bookModel->all();

        $activeLoans = [];
        if (Auth::check() && Auth::hasRole('doc_gia')) {
            $activeLoans = $this->model('Loan')->getActiveLoansByReaderId((int) Auth::id());
        }

        $this->view('home/index', [
            'title' => 'Thư viện mini',
            'books' => $books,
            'keyword' => $keyword,
            'activeLoans' => $activeLoans,
            'isReader' => Auth::check() && Auth::hasRole('doc_gia'),
            'isGuest' => !Auth::check(),
        ]);
    }

    public function ping(): void
    {
        if (!APP_DEBUG) {
            $this->abort(404);
        }
        try {
            $pdo = Database::getInstance()->getConnection();
            $soSach = (int) $pdo->query('SELECT COUNT(*) FROM sach')->fetchColumn();
            $this->jsonSuccess(['so_sach' => $soSach]);
        } catch (Throwable $e) {
            error_log('[ping] ' . $e->getMessage());
            $this->jsonError('Không truy vấn được CSDL.', 500);
        }
    }
}