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
        $biKhoa = input('bi_khoa');
        $lyDo   = trim(input('ly_do_khoa'));

        if ($id <= 0 || !in_array($biKhoa, ['0', '1'], true)) {
            flash('error', 'Dữ liệu khóa/mở khóa không hợp lệ.');
            $this->redirect(url('users'));
        }
        $biKhoa = (int) $biKhoa;

        // Không cho phép tự thao tác trên tài khoản của chính mình
        if ($id === Auth::id()) {
            flash('error', 'Bạn không thể khóa tài khoản của chính mình.');
            $this->redirect(url('users'));
        }

        $userModel = $this->model('User');
        $target = $userModel->findById($id);
        if (!$target) {
            flash('error', 'Không tìm thấy tài khoản.');
            $this->redirect(url('users'));
        }

        if ($biKhoa === 1) {
            if ($lyDo === '') {
                flash('error', 'Vui lòng nhập lý do khóa tài khoản.');
                $this->redirect(url('users'));
            }
            if (mb_strlen($lyDo) > 255) {
                flash('error', 'Lý do khóa tối đa 255 ký tự.');
                $this->redirect(url('users'));
            }
        }

        // Đã ở đúng trạng thái yêu cầu thì không coi là lỗi
        if ((int) ($target['bi_khoa'] ?? 0) === $biKhoa) {
            flash('info', $biKhoa === 1 ? 'Tài khoản này đã bị khóa trước đó.' : 'Tài khoản này đang hoạt động.');
            $this->redirect(url('users'));
        }

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