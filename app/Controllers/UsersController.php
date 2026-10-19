<?php

class UsersController extends Controller
{
    public function __construct()
    {
        // Bắt buộc quyền Quản trị viên (quan_tri)
        $this->requireRole('quan_tri');
    }

    // GET /users: Danh sách người dùng
    public function index(): void
    {
        $keyword = input('q');
        $userModel = $this->model('User');

        $total = $userModel->getTotalUsersCount($keyword);
        $pagination = paginate($total, 10);

        $users = $userModel->getPaginatedUsers(
            $keyword,
            $pagination['per_page'],
            $pagination['offset']
        );

        $this->view('users/index', [
            'title'      => 'Quản lý tài khoản',
            'users'      => $users,
            'keyword'    => $keyword,
            'pagination' => $pagination,
        ]);
    }

    // POST /users/toggleLock: Khóa hoặc Mở khóa tài khoản
    public function toggleLock(): void
    {
        $this->requirePost();
        
        $id     = (int) input('id');
        $biKhoa = (int) input('bi_khoa');
        $lyDo   = input('ly_do_khoa');

        // Không cho phép tự thao tác trên tài khoản của chính mình
        if ($id === Auth::id()) {
            flash('error', 'Bạn không thể khóa tài khoản của chính mình.');
            $this->redirect(url('users'));
        }

        $userModel = $this->model('User');
        $ok = $userModel->updateLockStatus($id, $biKhoa, $lyDo);

        if ($ok) {
            flash('success', $biKhoa === 1 ? 'Đã khóa tài khoản thành công.' : 'Đã mở khóa tài khoản thành công.');
        } else {
            flash('error', 'Cập nhật trạng thái tài khoản thất bại.');
        }

        $this->redirect(url('users'));
    }

    // POST /users/changeRole: Đổi vai trò tài khoản
    public function changeRole(): void
    {
        $this->requirePost();

        $id      = (int) input('id');
        $vaiTro  = input('vai_tro');
        $allowed = ['doc_gia', 'thu_thu', 'quan_tri'];

        if ($id === Auth::id()) {
            flash('error', 'Bạn không thể tự thay đổi vai trò của chính mình.');
            $this->redirect(url('users'));
        }

        if (in_array($vaiTro, $allowed, true)) {
            $userModel = $this->model('User');
            $userModel->updateRole($id, $vaiTro);
            flash('success', 'Đã cập nhật vai trò người dùng thành công.');
        } else {
            flash('error', 'Vai trò không hợp lệ.');
        }

        $this->redirect(url('users'));
    }
}