<?php

class LoansController extends Controller
{
    public function index(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');

        $loanModel = $this->model('Loan');

        $keyword = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));

        if (!in_array(
            $status,
            ['', 'dang_muon', 'qua_han', 'da_tra'],
            true
        )) {
            $status = '';
        }

        $perPage = 5;

        $totalLoans = $loanModel->countFilteredLoans(
            $keyword,
            $status
        );

        $totalPages = max(
            1,
            (int) ceil($totalLoans / $perPage)
        );

        $requestedPage = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT
        );

        $currentPage = (
            $requestedPage !== false
            && $requestedPage !== null
            && $requestedPage > 0
        ) ? $requestedPage : 1;

        $currentPage = min($currentPage, $totalPages);

        $loans = $loanModel->getLoansPaginated(
            $perPage,
            ($currentPage - 1) * $perPage,
            $keyword,
            $status
        );

        $this->view('loans/index', [
            'title' => 'Quản lý mượn - trả sách',
            'loans' => $loans,
            'totalLoans' => $totalLoans,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'stats' => $loanModel->getLoanStats(),
            'keyword' => $keyword,
            'selectedStatus' => $status,
            'finePerDay' => (float) $this->model('Setting')->getValue('tien_phat_moi_ngay', 2000),
        ]);
    }

    public function history(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');

        $loanModel = $this->model('Loan');

        $keyword = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));

        if (!in_array(
            $status,
            ['', 'dang_muon', 'qua_han', 'da_tra'],
            true
        )) {
            $status = '';
        }

        $perPage = 10;

        $totalLoans = $loanModel->countFilteredLoans(
            $keyword,
            $status
        );

        $totalPages = max(
            1,
            (int) ceil($totalLoans / $perPage)
        );

        $requestedPage = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT
        );

        $currentPage = (
            $requestedPage !== false
            && $requestedPage !== null
            && $requestedPage > 0
        ) ? $requestedPage : 1;

        $currentPage = min($currentPage, $totalPages);

        $loans = $loanModel->getLoansPaginated(
            $perPage,
            ($currentPage - 1) * $perPage,
            $keyword,
            $status
        );

        $this->view('loans/history', [
            'title' => 'Lịch sử mượn sách',
            'loans' => $loans,
            'totalLoans' => $totalLoans,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'stats' => $loanModel->getLoanStats(),
            'keyword' => $keyword,
            'selectedStatus' => $status,
        ]);
    }

    public function mine(): void
    {
        $this->requireRole('doc_gia');

        $readerId = (int) Auth::id();
        $loanModel = $this->model('Loan');

        $this->view('loans/mine', [
            'title' => 'Lịch sử mượn sách',
            'loans' => $loanModel->getHistoryByReaderId(
                $readerId
            ),
        ]);
    }

    public function create(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');

        $loanModel = $this->model('Loan');

        $this->view('loans/create', [
            'title' => 'Lập phiếu mượn sách',
            'readers' => $loanModel->getActiveReaders(),
            'availableCopies' => $loanModel->getAvailableCopies(),
        ]);
    }

    public function store(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $userId = filter_input(
            INPUT_POST,
            'nguoi_dung_id',
            FILTER_VALIDATE_INT
        );

        $copyId = filter_input(
            INPUT_POST,
            'ban_sao_id',
            FILTER_VALIDATE_INT
        );

        // Hạn trả do server tính trong Loan::createLoan (so_ngay_muon)
        if (!$userId || !$copyId) {
            flash('error', 'Vui lòng chọn độc giả và bản sao sách.');
            $this->redirect(url('loans/create'));
            return;
        }

        try {
            $this->model('Loan')->createLoan(
                (int) $userId,
                (int) $copyId,
                (int) Auth::id()
            );

            flash('success', 'Lập phiếu mượn thành công!');
            $this->redirect(url('loans'));
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            $this->redirect(url('loans/create'));
        } catch (Throwable $e) {
            error_log(
                '[LoansController::store] ' . $e->getMessage()
            );

            flash('error', 'Không thể lập phiếu mượn do lỗi hệ thống.');
            $this->redirect(url('loans/create'));
        }
    }

    public function returnBook(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $loanId = filter_input(
            INPUT_POST,
            'loan_id',
            FILTER_VALIDATE_INT
        );

        $page = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT
        );
        $page = ($page && $page > 0) ? $page : 1;
        $back = url('loans') . '?page=' . $page;

        if (!$loanId || $loanId <= 0) {
            flash('error', 'Mã phiếu mượn không hợp lệ.');
            $this->redirect($back);
            return;
        }

        $condition = trim(
            (string) ($_POST['tinh_trang_tra'] ?? '')
        );

        $fineInput = trim(
            (string) ($_POST['tien_phat'] ?? '')
        );

        $note = trim(
            (string) ($_POST['ghi_chu'] ?? '')
        );

        if (!in_array(
            $condition,
            ['tot', 'hong', 'mat'],
            true
        )) {
            flash('error', 'Tình trạng sách không hợp lệ.');
            $this->redirect($back);
            return;
        }

        $loanModel = $this->model('Loan');
        try {
            $calc = $loanModel->calculateOverdueFine((int) $loanId);
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            $this->redirect($back);
            return;
        }

        $autoFine = (float) $calc['fine'];

        if ($fineInput !== '' && is_numeric($fineInput) && is_finite((float) $fineInput)) {
            $manual = (float) $fineInput;
            if ($manual < 0) {
                flash('error', 'Tiền phạt không hợp lệ.');
                $this->redirect($back);
                return;
            }
            $fine = max($autoFine, $manual);
        } else {
            $fine = $autoFine;
        }

        if (mb_strlen($note) > 255) {
            flash('error', 'Ghi chú không được vượt quá 255 ký tự.');
            $this->redirect($back);
            return;
        }

        $staffId = (int) Auth::id();

        if ($staffId <= 0) {
            flash('error', 'Vui lòng đăng nhập lại.');
            $this->redirect(url('auth/login'));
            return;
        }

        try {
            $loanModel->returnBook(
                (int) $loanId,
                $staffId,
                $condition,
                (float) $fine,
                $note
            );
            flash('success', 'Đã xác nhận trả sách.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            $this->redirect($back);
            return;
        } catch (Throwable $e) {
            error_log(
                '[LoansController::returnBook] '
                . $e->getMessage()
            );

            flash('error', 'Không thể xác nhận trả sách. Vui lòng thử lại.');
            $this->redirect($back);
            return;
        }

        $this->redirect($back);
    }
}