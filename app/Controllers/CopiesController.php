
<?php

class CopiesController extends Controller
{
    private const STATUSES = [
        'con_trong',
        'dang_giu_cho',
        'dang_muon',
        'hong',
        'mat',
        'thanh_ly',
    ];

    public function __construct()
    {
        $this->requireRole('thu_thu', 'quan_tri');
    }

    // GET /copies
    public function index(): void
    {
        $keyword = input('q');
        $status = input('status');

        if (!in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        $copyModel = $this->model('Copy');

        $this->view('copies/index', [
            'title' => 'Quản lý bản sao sách',
            'copies' => $copyModel->getAll($keyword, $status),
            'keyword' => $keyword,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    // GET /copies/create
    public function create(): void
    {
        $copyModel = $this->model('Copy');

        $this->view('copies/create', [
            'title' => 'Thêm bản sao',
            'books' => $copyModel->getBooks(),
            'statuses' => self::STATUSES,
            'errors' => [],
            'form' => [
                'sach_id' => '',
                'ma_ban_sao' => '',
                'trang_thai' => 'con_trong',
                'vi_tri_ke' => '',
                'ngay_nhap' => '',
                'ghi_chu' => '',
            ],
        ]);
    }

    // POST /copies/store
    public function store(): void
    {
        $this->requirePost();

        $form = $this->readForm();
        $copyModel = $this->model('Copy');
        $books = $copyModel->getBooks();
        $errors = $this->validateForm($form, $copyModel, $books);

        if ($errors) {
            $this->view('copies/create', [
                'title' => 'Thêm bản sao',
                'books' => $books,
                'statuses' => self::STATUSES,
                'errors' => $errors,
                'form' => $form,
            ]);
            return;
        }

        try {
            $copyModel->create($form);
            flash('success', 'Thêm bản sao sách thành công.');
            $this->redirect(url('copies'));
        } catch (PDOException $e) {
            $this->view('copies/create', [
                'title' => 'Thêm bản sao',
                'books' => $books,
                'statuses' => self::STATUSES,
                'errors' => [
                    'Không thể thêm bản sao. Hãy kiểm tra mã bản sao và dữ liệu nhập.'
                ],
                'form' => $form,
            ]);
        }
    }

    // GET /copies/edit/{id}
    public function edit(int $id): void
    {
        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy bản sao.');
            return;
        }

        $copyModel = $this->model('Copy');
        $copy = $copyModel->findById($id);

        if (!$copy) {
            $this->abort(404, 'Không tìm thấy bản sao.');
            return;
        }

        $this->view('copies/edit', [
            'title' => 'Sửa bản sao',
            'copy' => $copy,
            'books' => $copyModel->getBooks(),
            'statuses' => self::STATUSES,
            'errors' => [],
            'form' => $copy,
        ]);
    }

    // POST /copies/update/{id}
    public function update(int $id): void
    {
        $this->requirePost();

        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy bản sao.');
            return;
        }

        $copyModel = $this->model('Copy');
        $copy = $copyModel->findById($id);

        if (!$copy) {
            $this->abort(404, 'Không tìm thấy bản sao.');
            return;
        }

        $form = $this->readForm();
        $books = $copyModel->getBooks();
        $errors = $this->validateForm($form, $copyModel, $books, $id);

        if ($errors) {
            $this->view('copies/edit', [
                'title' => 'Sửa bản sao',
                'copy' => $copy,
                'books' => $books,
                'statuses' => self::STATUSES,
                'errors' => $errors,
                'form' => $form,
            ]);
            return;
        }

        try {
            $copyModel->update($id, $form);
            flash('success', 'Cập nhật bản sao thành công.');
            $this->redirect(url('copies'));
        } catch (PDOException $e) {
            $this->view('copies/edit', [
                'title' => 'Sửa bản sao',
                'copy' => $copy,
                'books' => $books,
                'statuses' => self::STATUSES,
                'errors' => [
                    'Không thể cập nhật bản sao. Hãy kiểm tra mã bản sao và dữ liệu nhập.'
                ],
                'form' => $form,
            ]);
        }
    }

    // POST /copies/delete/{id}
    public function delete(int $id): void
    {
        $this->requirePost();

        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy bản sao.');
            return;
        }

        $copyModel = $this->model('Copy');
        $copy = $copyModel->findById($id);

        if (!$copy) {
            flash('error', 'Bản sao không tồn tại.');
            $this->redirect(url('copies'));
        }

        if (in_array($copy['trang_thai'], ['dang_muon', 'dang_giu_cho'], true)) {
            flash(
                'error',
                'Không thể xóa bản sao đang được mượn hoặc giữ chỗ.'
            );
            $this->redirect(url('copies'));
        }

        try {
            $deleted = $copyModel->softDelete($id);

            if ($deleted > 0) {
                flash('success', 'Xóa bản sao thành công.');
            } else {
                flash('error', 'Không thể xóa bản sao này.');
            }
        } catch (PDOException $e) {
            flash('error', 'Không thể xóa bản sao do dữ liệu đang được sử dụng.');
        }

        $this->redirect(url('copies'));
    }

    private function readForm(): array
    {
        return [
            'sach_id' => input('sach_id'),
            'ma_ban_sao' => input('ma_ban_sao'),
            'trang_thai' => input('trang_thai', 'con_trong'),
            'vi_tri_ke' => input('vi_tri_ke'),
            'ngay_nhap' => input('ngay_nhap'),
            'ghi_chu' => input('ghi_chu'),
        ];
    }

    private function validateForm(
        array $form,
        Copy $copyModel,
        array $books,
        ?int $excludeId = null
    ): array {
        $errors = [];

        $bookIds = array_map(
            static fn($book) => (string) $book['id'],
            $books
        );

        if (
            $form['sach_id'] === ''
            || !in_array($form['sach_id'], $bookIds, true)
        ) {
            $errors[] = 'Vui lòng chọn đầu sách hợp lệ.';
        }

        if ($form['ma_ban_sao'] === '') {
            $errors[] = 'Vui lòng nhập mã bản sao.';
        } elseif (mb_strlen($form['ma_ban_sao']) > 30) {
            $errors[] = 'Mã bản sao không được vượt quá 30 ký tự.';
        } elseif ($copyModel->isCodeExists($form['ma_ban_sao'], $excludeId)) {
            $errors[] = 'Mã bản sao này đã tồn tại.';
        }

        if (!in_array($form['trang_thai'], self::STATUSES, true)) {
            $errors[] = 'Trạng thái bản sao không hợp lệ.';
        }

        if (mb_strlen($form['vi_tri_ke']) > 50) {
            $errors[] = 'Vị trí kệ không được vượt quá 50 ký tự.';
        }

        if (mb_strlen($form['ghi_chu']) > 255) {
            $errors[] = 'Ghi chú không được vượt quá 255 ký tự.';
        }

        if ($form['ngay_nhap'] !== '') {
            $date = DateTime::createFromFormat('!Y-m-d', $form['ngay_nhap']);

            if (
                !$date
                || $date->format('Y-m-d') !== $form['ngay_nhap']
            ) {
                $errors[] = 'Ngày nhập không hợp lệ.';
            }
        }

        return $errors;
    }
}
