<?php
class LoansController extends Controller {
    public function mine() {
        // Lấy ID người dùng đang đăng nhập
        $userId = null;
        if (class_exists('Auth') && method_exists('Auth', 'user') && Auth::user()) {
            $userData = Auth::user();
            $userId = $userData['id'] ?? null;
        }

        // Gọi Loan Model để lấy danh sách sách đang mượn của user
        $loans = [];
        if ($userId && method_exists($this, 'model')) {
            $loanModel = $this->model('Loan');
            if ($loanModel && method_exists($loanModel, 'getByUser')) {
                $loans = $loanModel->getByUser($userId);
            }
        }

        // Trả về view kèm dữ liệu
        $this->view('loans/mine', [
            'title' => 'Sách đang mượn - Thư viện mini',
            'loans' => $loans
        ]);
    }
}