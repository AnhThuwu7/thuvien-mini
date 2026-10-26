
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
        ]);
    }
    
    public function history(): void
    {
        // Chỉ thủ thư và quản trị viên được xem
        $this->requireRole('thu_thu', 'quan_tri');

        $loanModel = $this->model('Loan');

        // Lấy từ khóa và trạng thái lọc
        $keyword = trim((string) ($_GET['q'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));

        if (!in_array(
            $status,
            ['', 'dang_muon', 'qua_han', 'da_tra'],
            true
        )) {
            $status = '';
        }

        // Phân trang
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

        // Hiển thị trang lịch sử mượn của toàn bộ độc giả
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

        $dueDate = trim(
            (string) ($_POST['han_tra'] ?? '')
        );

        if (!$userId || !$copyId || $dueDate === '') {
            $_SESSION['error'] =
                'Vui lòng điền đầy đủ thông tin lập phiếu mượn.';

            $this->redirect(url('loans/create'));
            return;
        }

        try {
            $this->model('Loan')->createLoan(
                (int) $userId,
                (int) $copyId,
                $dueDate,
                (int) Auth::id()
            );

            $_SESSION['success'] = 'Lập phiếu mượn thành công!';

            $this->redirect(url('loans'));
        } catch (RuntimeException $e) {
            $_SESSION['error'] = $e->getMessage();

            $this->redirect(url('loans/create'));
        } catch (Throwable $e) {
            error_log(
                '[LoansController::store] ' . $e->getMessage()
            );

            $_SESSION['error'] =
                'Không thể lập phiếu mượn do lỗi hệ thống.';

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

        if (!$loanId || $loanId <= 0) {
            $this->jsonError(
                'Mã phiếu mượn không hợp lệ.',
                400
            );
            return;
        }

        $condition = trim(
            (string) ($_POST['tinh_trang_tra'] ?? '')
        );

        $fineInput = trim(
            (string) ($_POST['tien_phat'] ?? '0')
        );

        $note = trim(
            (string) ($_POST['ghi_chu'] ?? '')
        );

        if (!in_array(
            $condition,
            ['tot', 'hong', 'mat'],
            true
        )) {
            $this->jsonError(
                'Tình trạng sách không hợp lệ.',
                400
            );
            return;
        }

        if (
            $fineInput === ''
            || !is_numeric($fineInput)
            || !is_finite((float) $fineInput)
            || (float) $fineInput < 0
        ) {
            $this->jsonError(
                'Tiền phạt không hợp lệ.',
                400
            );
            return;
        }

        if (mb_strlen($note) > 255) {
            $this->jsonError(
                'Ghi chú không được vượt quá 255 ký tự.',
                400
            );
            return;
        }

        $staffId = (int) Auth::id();

        if ($staffId <= 0) {
            $this->jsonError(
                'Vui lòng đăng nhập lại.',
                401
            );
            return;
        }

        try {
            $this->model('Loan')->returnBook(
                (int) $loanId,
                $staffId,
                $condition,
                (float) $fineInput,
                $note
            );
        } catch (RuntimeException $e) {
            $this->jsonError($e->getMessage(), 400);
            return;
        } catch (Throwable $e) {
            error_log(
                '[LoansController::returnBook] '
                . $e->getMessage()
            );

            $this->jsonError(
                'Không thể xác nhận trả sách. '
                . 'Vui lòng kiểm tra nhật ký lỗi.',
                500
            );
            return;
        }

        $page = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT
        );

        $page = ($page && $page > 0) ? $page : 1;

        $this->redirect(
            url('loans') . '?page=' . $page
        );
    }
}
