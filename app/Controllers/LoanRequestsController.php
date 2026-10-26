
<?php

class LoanRequestsController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu đang chờ duyệt.
     */
    public function index(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');

        $requestModel = $this->model('LoanRequest');
        $requests = $requestModel->getPendingRequests();

        $this->view('loan_requests/index', [
            'title' => 'Yêu cầu mượn sách',
            'requests' => $requests,
        ]);
    }

    /**
     * Duyệt yêu cầu mượn sách.
     */
    public function approve(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $requestId = filter_input(
            INPUT_POST,
            'request_id',
            FILTER_VALIDATE_INT
        );

        if (!$requestId || $requestId <= 0) {
            $this->redirectWithMessage(
                'error',
                'Mã yêu cầu mượn không hợp lệ.'
            );
            return;
        }

        $staffId = (int) Auth::id();

        if ($staffId <= 0) {
            $this->redirectWithMessage(
                'error',
                'Vui lòng đăng nhập để tiếp tục.'
            );
            return;
        }

        $requestModel = $this->model('LoanRequest');

        try {
            $requestModel->approveRequest(
                (int) $requestId,
                $staffId
            );
        } catch (RuntimeException $e) {
            $this->redirectWithMessage(
                'error',
                $e->getMessage()
            );
            return;
        } catch (Throwable $e) {
            // Ghi chi tiết lỗi vào log máy chủ, không hiển thị
            // thông tin database nội bộ cho người dùng.
            error_log(
                '[LoanRequestsController::approve] ' .
                $e->getMessage()
            );

            $this->redirectWithMessage(
                'error',
                'Đã xảy ra lỗi khi duyệt yêu cầu. Vui lòng thử lại.'
            );
            return;
        }

        $this->redirectWithMessage(
            'success',
            'Duyệt yêu cầu thành công! Sách đã được giữ chỗ.'
        );
    }

    /**
     * Từ chối yêu cầu mượn sách.
     */
    public function reject(): void
    {
        $this->requireRole('thu_thu', 'quan_tri');
        $this->requirePost();

        $requestId = filter_input(
            INPUT_POST,
            'request_id',
            FILTER_VALIDATE_INT
        );

        if (!$requestId || $requestId <= 0) {
            $this->redirectWithMessage(
                'error',
                'Mã yêu cầu mượn không hợp lệ.'
            );
            return;
        }

        $staffId = (int) Auth::id();

        if ($staffId <= 0) {
            $this->redirectWithMessage(
                'error',
                'Vui lòng đăng nhập để tiếp tục.'
            );
            return;
        }

        $reason = trim(
            (string) ($_POST['ly_do_tu_choi'] ?? '')
        );

        if ($reason === '') {
            $reason = 'Yêu cầu không được chấp thuận.';
        }

        if (function_exists('mb_substr')) {
            $reason = mb_substr($reason, 0, 255, 'UTF-8');
        } else {
            $reason = substr($reason, 0, 255);
        }

        $requestModel = $this->model('LoanRequest');

        try {
            $requestModel->rejectRequest(
                (int) $requestId,
                $staffId,
                $reason
            );
        } catch (RuntimeException $e) {
            $this->redirectWithMessage(
                'error',
                $e->getMessage()
            );
            return;
        } catch (Throwable $e) {
            error_log(
                '[LoanRequestsController::reject] ' .
                $e->getMessage()
            );

            $this->redirectWithMessage(
                'error',
                'Đã xảy ra lỗi khi từ chối yêu cầu. Vui lòng thử lại.'
            );
            return;
        }

        $this->redirectWithMessage(
            'success',
            'Đã từ chối yêu cầu mượn sách.'
        );
    }

    /**
     * Quay lại trang yêu cầu và gửi thông báo qua URL.
     */
    private function redirectWithMessage(
        string $type,
        string $message
    ): void {
        $allowedTypes = ['success', 'error'];

        if (!in_array($type, $allowedTypes, true)) {
            $type = 'error';
        }

        $this->redirect(
            url('loan-requests') .
            '?' . $type . '=' . rawurlencode($message)
        );
    }
}
