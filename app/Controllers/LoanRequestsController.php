<?php

class LoanRequestsController extends Controller
{
    public function index(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');

        $m = $this->model('LoanRequest');
        $m->expireOverdueHolds();

        $this->view('loan_requests/index', [
            'title' => 'Yêu cầu mượn sách',
            'pending' => $m->getPendingRequests(),
            'approved' => $m->getApprovedHolds(),
            'closed' => $m->getClosedRequests(40),
            'hold_hours' => $this->readHoldHours(),
        ]);
    }

    public function approve(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $requestId = (int) filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        if ($requestId <= 0) {
            flash('error', 'Mã yêu cầu mượn không hợp lệ.');
            $this->redirect(url('loan-requests'));
            return;
        }

        $staffId = (int) Auth::id();
        try {
            $this->model('LoanRequest')->approveRequest($requestId, $staffId);
            flash(
                'success',
                'Đã duyệt và giữ chỗ. Độc giả phải tới thư viện lập phiếu trước hạn trên phiếu yêu cầu.'
            );
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        } catch (Throwable $e) {
            error_log('[LoanRequestsController::approve] ' . $e->getMessage());
            flash('error', 'Đã xảy ra lỗi khi duyệt yêu cầu. Vui lòng thử lại.');
        }

        $this->redirect(url('loan-requests'));
    }

    public function reject(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $requestId = (int) filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        if ($requestId <= 0) {
            flash('error', 'Mã yêu cầu mượn không hợp lệ.');
            $this->redirect(url('loan-requests'));
            return;
        }

        $reason = trim((string) ($_POST['ly_do_tu_choi'] ?? ''));
        if ($reason === '') {
            $reason = 'Yêu cầu không được chấp thuận.';
        }
        if (function_exists('mb_substr')) {
            $reason = mb_substr($reason, 0, 255, 'UTF-8');
        } else {
            $reason = substr($reason, 0, 255);
        }

        try {
            $this->model('LoanRequest')->rejectRequest($requestId, (int) Auth::id(), $reason);
            flash('success', 'Đã từ chối yêu cầu mượn sách.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        } catch (Throwable $e) {
            error_log('[LoanRequestsController::reject] ' . $e->getMessage());
            flash('error', 'Đã xảy ra lỗi khi từ chối yêu cầu.');
        }

        $this->redirect(url('loan-requests'));
    }

    public function fulfill(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $requestId = (int) filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);
        if ($requestId <= 0) {
            flash('error', 'Mã yêu cầu không hợp lệ.');
            $this->redirect(url('loan-requests'));
            return;
        }

        try {
            $this->model('LoanRequest')->fulfillToLoan($requestId, (int) Auth::id());
            flash('success', 'Đã lập phiếu mượn từ yêu cầu giữ chỗ #' . $requestId . '.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        } catch (Throwable $e) {
            error_log('[LoanRequestsController::fulfill] ' . $e->getMessage());
            flash('error', 'Không lập được phiếu mượn. Vui lòng thử lại.');
        }

        $this->redirect(url('loan-requests'));
    }

    public function mine(): void
    {
        $this->requireRole('doc_gia');

        $rows = $this->model('LoanRequest')->getByReaderId((int) Auth::id());

        $this->view('loan_requests/mine', [
            'title' => 'Yêu cầu mượn của tôi',
            'requests' => $rows,
        ]);
    }

    public function store(): void
    {
        $this->requireRole('doc_gia');
        $this->requirePost();

        $bookId = (int) filter_input(INPUT_POST, 'sach_id', FILTER_VALIDATE_INT);
        $note = trim((string) ($_POST['ghi_chu'] ?? ''));

        if ($bookId <= 0) {
            flash('error', 'Đầu sách không hợp lệ.');
            $this->redirect(url('catalog'));
            return;
        }

        try {
            $this->model('LoanRequest')->createRequest((int) Auth::id(), $bookId, $note);
            flash('success', 'Đã gửi yêu cầu mượn. Vui lòng chờ thủ thư duyệt.');
            $this->redirect(url('loan-requests/mine'));
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
            $this->redirect(url('catalog/show/' . $bookId));
        } catch (Throwable $e) {
            error_log('[LoanRequestsController::store] ' . $e->getMessage());
            flash('error', 'Không gửi được yêu cầu. Vui lòng thử lại.');
            $this->redirect(url('catalog'));
        }
    }

    private function readHoldHours(): int
    {
        try {
            $pdo = Database::getInstance()->getConnection();
            $st = $pdo->query("SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_gio_giu_sach' LIMIT 1");
            $v = $st ? $st->fetchColumn() : false;
            if ($v !== false && is_numeric($v) && (int) $v >= 1) {
                return (int) $v;
            }
            $st = $pdo->query("SELECT gia_tri FROM cau_hinh WHERE khoa = 'so_ngay_giu_sach' LIMIT 1");
            $v = $st ? $st->fetchColumn() : false;
            if ($v !== false && is_numeric($v) && (int) $v >= 1) {
                return (int) $v * 24;
            }
        } catch (Throwable $e) {
            // ignore
        }
        return 12;
    }
}