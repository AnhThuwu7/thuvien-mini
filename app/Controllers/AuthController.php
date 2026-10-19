<?php

class AuthController extends Controller
{
    // Hash giả: email không tồn tại vẫn gọi password_verify (chống đoán email)
    private const DUMMY_HASH = '$2y$12$aCqRGnzd4CvkDjV6fRHsBuikkFzdU0oPD59KVD7R6PA05x9.2ck.a';

    // GET auth/login | POST auth/login
    public function login()
    {
        if (Auth::check()) {
            $this->redirect(url());
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }

        $this->view('auth/login', [
            'title'  => 'Đăng nhập',
            'errors' => [],
            'email'  => '',
        ]);
    }

    private function handleLogin(): void
    {
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors   = [];

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        }
        if ($password === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        }

        if (!$errors) {
            $user = $this->model('User')->findByEmail($email);
            $hash = $user['mat_khau_bam'] ?? self::DUMMY_HASH;
            $ok   = password_verify($password, $hash);

            if ($user !== null && $ok) {
                if (!empty($user['bi_khoa'])) {
                    $lyDo = !empty($user['ly_do_khoa']) ? $user['ly_do_khoa'] : 'Không rõ lý do';
                    $errors['login'] = 'Tài khoản của bạn đã bị khóa. Lý do: ' . $lyDo . '. Vui lòng liên hệ thủ thư.';
                } else {
                    Auth::login($user);
                    flash('success', 'Xin chào, ' . $user['ho_ten'] . '!');
                    $this->redirect(Auth::pullIntended() ?? url());
                }
            } else {
                $errors['login'] = 'Email hoặc mật khẩu không đúng.';
            }
        }

        $this->view('auth/login', [
            'title'  => 'Đăng nhập',
            'errors' => $errors,
            'email'  => $email,
        ]);
    }

    // Chỉ nhận POST + CSRF
    public function logout()
    {
        $this->requirePost();
        Auth::logout();
        flash('success', 'Bạn đã đăng xuất.');
        $this->redirect(url('auth/login'));
    }

    public function profile()
    {
        $this->requireLogin();

        $me = $this->model('User')->findById(Auth::id());
        if ($me === null) {
            Auth::logout();
            $this->redirect(url('auth/login'));
        }

        $this->view('auth/profile', [
            'title' => 'Hồ sơ của tôi',
            'me'    => $me,
        ]);
    }
}