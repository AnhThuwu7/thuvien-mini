<?php

class AuthController extends Controller
{
    private const DUMMY_HASH = '$2y$12$aCqRGnzd4CvkDjV6fRHsBuikkFzdU0oPD59KVD7R6PA05x9.2ck.a';

    public function login()
    {
        if (Auth::check()) {
            $this->redirect($this->homeForRole(Auth::user()['vai_tro'] ?? ''));
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

        // Chống dò mật khẩu: sai quá nhiều lần thì tạm chặn
        if (!$errors) {
            $wait = LoginThrottle::blockedSeconds($email);
            if ($wait > 0) {
                $errors['login'] = 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau '
                    . (int) ceil($wait / 60) . ' phút.';
            }
        }

        if (!$errors) {
            $user = $this->model('User')->findByEmail($email);
            $hash = $user['mat_khau_bam'] ?? self::DUMMY_HASH;
            $ok   = password_verify($password, $hash);

            if ($user !== null && $ok) {
                LoginThrottle::clear($email);
                Auth::login($user);

                if (!empty($user['bi_khoa'])) {
                    $lyDo = !empty($user['ly_do_khoa']) ? $user['ly_do_khoa'] : 'Không rõ lý do';
                    flash(
                        'warning',
                        'Xin chào, ' . $user['ho_ten'] . '! Tài khoản đang bị khóa — bạn có thể xem thông tin nhưng không được mượn sách. Lý do: ' . $lyDo
                    );
                } else {
                    flash('success', 'Xin chào, ' . $user['ho_ten'] . '!');
                }

                $intended = Auth::pullIntended();
                if ($intended) {
                    $this->redirect($intended);
                }
                $this->redirect($this->homeForRole($user['vai_tro'] ?? ''));
            } else {
                LoginThrottle::fail($email);
                $errors['login'] = 'Email hoặc mật khẩu không đúng.';
            }
        }

        $this->view('auth/login', [
            'title'  => 'Đăng nhập',
            'errors' => $errors,
            'email'  => $email,
        ]);
    }

    public function register()
    {
        if (Auth::check()) {
            $this->redirect($this->homeForRole(Auth::user()['vai_tro'] ?? ''));
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
            return;
        }

        $this->view('auth/register', [
            'title'  => 'Đăng ký độc giả',
            'errors' => [],
            'old'    => ['ho_ten' => '', 'email' => '', 'so_dien_thoai' => ''],
        ]);
    }

    private function handleRegister(): void
    {
        $hoTen   = trim((string) ($_POST['ho_ten'] ?? ''));
        $email   = trim((string) ($_POST['email'] ?? ''));
        $phone   = trim((string) ($_POST['so_dien_thoai'] ?? ''));
        $pass    = (string) ($_POST['password'] ?? '');
        $pass2   = (string) ($_POST['password_confirmation'] ?? '');
        $errors  = [];
        $old     = ['ho_ten' => $hoTen, 'email' => $email, 'so_dien_thoai' => $phone];

        if ($hoTen === '') {
            $errors['ho_ten'] = 'Vui lòng nhập họ tên.';
        } elseif (mb_strlen($hoTen) > 100) {
            $errors['ho_ten'] = 'Họ tên tối đa 100 ký tự.';
        }

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        }

        if ($phone !== '') {
            // Chỉ chấp nhận đúng 10 chữ số (VD: 0912345678)
            $digits = preg_replace('/\D+/', '', $phone);
            if ($digits !== $phone || strlen($digits) !== 10) {
                $errors['so_dien_thoai'] = 'Số điện thoại phải gồm đúng 10 chữ số (không khoảng trắng, không dấu).';
            } else {
                $phone = $digits;
            }
        }

        if ($pass === '') {
            $errors['password'] = 'Vui lòng nhập mật khẩu.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\s]).{8,}$/u', $pass)) {
            // >= 8 ký tự, có thường, hoa, và ký tự đặc biệt (không phải chữ/số/_)
            $errors['password'] = 'Mật khẩu tối thiểu 8 ký tự, gồm chữ hoa, chữ thường và ký hiệu đặc biệt.';
        }

        if ($pass2 === '') {
            $errors['password_confirmation'] = 'Vui lòng nhập lại mật khẩu.';
        } elseif ($pass !== $pass2) {
            $errors['password_confirmation'] = 'Mật khẩu nhập lại không khớp.';
        }

        if (!$errors) {
            try {
                $this->model('User')->registerReader($hoTen, $email, $pass, $phone !== '' ? $phone : null);
                $user = $this->model('User')->findByEmail($email);
                if ($user) {
                    Auth::login($user);
                    flash('success', 'Đăng ký thành công. Chào mừng bạn đến Thư viện mini!');
                    $intended = Auth::pullIntended();
                    $this->redirect($intended ?: url());
                }
                flash('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
                $this->redirect(url('auth/login'));
            } catch (RuntimeException $e) {
                $errors['register'] = $e->getMessage();
            } catch (Throwable $e) {
                error_log('[register] ' . $e->getMessage());
                $errors['register'] = 'Không đăng ký được. Vui lòng thử lại.';
            }
        }

        $this->view('auth/register', [
            'title'  => 'Đăng ký độc giả',
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    private function homeForRole(string $role): string
    {
        if ($role === 'thu_thu' || $role === 'quan_tri') {
            return url('reports/stats');
        }
        return url();
    }

    public function logout()
    {
        $this->requirePost();
        Auth::logout();
        flash('success', 'Bạn đã đăng xuất.');
        $this->redirect(url());
    }

    public function profile()
    {
        $this->requireLogin();

        $me = $this->model('User')->findById((int) Auth::id());
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