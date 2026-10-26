<?php

class BooksController extends Controller
{
    public function __construct()
    {
        $this->requireRole('thu_thu', 'quan_tri');
    }

/**
 * Tải ảnh bìa lên thư mục public/uploads/books.
 * Nếu không chọn ảnh mới, giữ nguyên đường dẫn ảnh hiện tại.
 */
private function uploadCover(?string $currentPath = null): ?string
{
    if (
        !isset($_FILES['anh_bia'])
        || $_FILES['anh_bia']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        return $currentPath;
    }

    $file = $_FILES['anh_bia'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Tải ảnh lên thất bại. Vui lòng thử lại.');
    }

    // Giới hạn dung lượng 2 MB.
    if ($file['size'] > 2 * 1024 * 1024) {
        throw new RuntimeException('Ảnh bìa không được vượt quá 2 MB.');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('File ảnh tải lên không hợp lệ.');
    }

    // Kiểm tra MIME thực tế, không chỉ dựa vào phần mở rộng file.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if ($finfo === false) {
        throw new RuntimeException('Không thể kiểm tra định dạng ảnh.');
    }

    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedTypes[$mime])) {
        throw new RuntimeException(
            'Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.'
        );
    }

    $uploadDir = dirname(__DIR__, 2) . '/public/uploads/books';

    if (
        !is_dir($uploadDir)
        && !mkdir($uploadDir, 0755, true)
        && !is_dir($uploadDir)
    ) {
        throw new RuntimeException('Không thể tạo thư mục lưu ảnh.');
    }

    $fileName = bin2hex(random_bytes(16))
        . '.'
        . $allowedTypes[$mime];

    $destination = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Không thể lưu ảnh bìa lên máy chủ.');
    }

    // Lưu đường dẫn tương đối để hiển thị từ thư mục public.
    return 'uploads/books/' . $fileName;
}

    // GET /books
    public function index(): void
    {
        $keyword = input('q');

        $bookModel = $this->model('Book');

        $this->view('books/index', [
            'title' => 'Quản lý sách',
            'books' => $bookModel->getAll($keyword),
            'keyword' => $keyword,
        ]);
    }

    // GET /books/show/{id}
    public function show($id): void
    {
        $id = (int) $id;

        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy sách.');
            return;
        }

        $bookModel = $this->model('Book');
        $book = $bookModel->findById($id);

        if (!$book) {
            $this->abort(404, 'Không tìm thấy sách.');
            return;
        }

        $this->view('books/show', [
            'title' => 'Chi tiết sách',
            'book' => $book,
        ]);
    }

    // GET /books/create
    public function create(): void
    {
        $bookModel = $this->model('Book');

        $this->view('books/create', [
            'title' => 'Thêm sách mới',
            'categories' => $bookModel->getCategories(),
            'errors' => [],
            'form' => [],
        ]);
    }

    // POST /books/store
    public function store(): void
    {
        $this->requirePost();

        $form = [
            'ten_sach' => input('ten_sach'),
            'isbn' => input('isbn'),
            'danh_muc_id' => input('danh_muc_id'),
            'nha_xuat_ban' => input('nha_xuat_ban'),
            'nam_xuat_ban' => input('nam_xuat_ban'),
            'ngon_ngu' => input('ngon_ngu', 'vi'),
            'mo_ta' => input('mo_ta'),
        ];

        $bookModel = $this->model('Book');
        $categories = $bookModel->getCategories();
        $errors = [];

        // Kiểm tra tên sách.
        if ($form['ten_sach'] === '') {
            $errors[] = 'Vui lòng nhập tên sách.';
        } elseif (mb_strlen($form['ten_sach']) > 255) {
            $errors[] = 'Tên sách không được vượt quá 255 ký tự.';
        }

        // Kiểm tra danh mục.
        $categoryIds = array_map(
            static fn($category) => (string) $category['id'],
            $categories
        );

        if (
            $form['danh_muc_id'] === ''
            || !in_array($form['danh_muc_id'], $categoryIds, true)
        ) {
            $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
        }

        // Kiểm tra ISBN.
        if (mb_strlen($form['isbn']) > 20) {
            $errors[] = 'ISBN không được vượt quá 20 ký tự.';
        } elseif ($form['isbn'] !== '') {
            if ($bookModel->isIsbnExists($form['isbn'])) {
                $errors[] = 'ISBN này đã tồn tại.';
            }
        }

        // Kiểm tra nhà xuất bản.
        if (mb_strlen($form['nha_xuat_ban']) > 150) {
            $errors[] = 'Tên nhà xuất bản không được vượt quá 150 ký tự.';
        }

        // Kiểm tra năm xuất bản.
        if ($form['nam_xuat_ban'] !== '') {
            if (
                !ctype_digit($form['nam_xuat_ban'])
                || (int) $form['nam_xuat_ban'] < 1000
                || (int) $form['nam_xuat_ban'] > 2100
            ) {
                $errors[] = 'Năm xuất bản phải từ 1000 đến 2100.';
            }
        }

        // Kiểm tra ngôn ngữ.
        if (!in_array($form['ngon_ngu'], ['vi', 'en'], true)) {
            $errors[] = 'Ngôn ngữ không hợp lệ.';
        }

        if ($errors) {
            $this->view('books/create', [
                'title' => 'Thêm sách mới',
                'categories' => $categories,
                'errors' => $errors,
                'form' => $form,
            ]);
            return;
        }

        
        try {
            // Tải ảnh bìa nếu người dùng đã chọn ảnh.
            $form['anh_bia'] = $this->uploadCover();

            // Lưu thông tin sách cùng đường dẫn ảnh.
            $bookModel->create($form);

            flash('success', 'Thêm sách mới thành công.');
            $this->redirect(url('books'));
        } catch (RuntimeException $e) {
            $this->view('books/create', [
                'title' => 'Thêm sách mới',
                'categories' => $categories,
                'errors' => [$e->getMessage()],
                'form' => $form,
            ]);
        } catch (PDOException $e) {
            $this->view('books/create', [
                'title' => 'Thêm sách mới',
                'categories' => $categories,
                'errors' => [
                    'Không thể lưu sách. Hãy kiểm tra ISBN và dữ liệu nhập.'
                ],
                'form' => $form,
            ]);
        }

    }
    // GET /books/edit/{id}
public function edit($id): void
{
    $id = (int) $id;

    if ($id <= 0) {
        $this->abort(404, 'Không tìm thấy sách.');
        return;
    }

    $bookModel = $this->model('Book');
    $book = $bookModel->findById($id);

    if (!$book) {
        $this->abort(404, 'Không tìm thấy sách.');
        return;
    }

    $this->view('books/edit', [
        'title' => 'Sửa thông tin sách',
        'book' => $book,
        'categories' => $bookModel->getCategories(),
        'errors' => [],
        'form' => $book,
    ]);
}

// POST /books/update/{id}
public function update($id): void
{
    $this->requirePost();

    $id = (int) $id;

    if ($id <= 0) {
        $this->abort(404, 'Không tìm thấy sách.');
        return;
    }

    $bookModel = $this->model('Book');
    $book = $bookModel->findById($id);

    if (!$book) {
        $this->abort(404, 'Không tìm thấy sách.');
        return;
    }

    $form = [
        'ten_sach' => input('ten_sach'),
        'isbn' => input('isbn'),
        'danh_muc_id' => input('danh_muc_id'),
        'nha_xuat_ban' => input('nha_xuat_ban'),
        'nam_xuat_ban' => input('nam_xuat_ban'),
        'ngon_ngu' => input('ngon_ngu', 'vi'),
        'mo_ta' => input('mo_ta'),
    ];

    $categories = $bookModel->getCategories();
    $errors = [];

    if ($form['ten_sach'] === '') {
        $errors[] = 'Vui lòng nhập tên sách.';
    } elseif (mb_strlen($form['ten_sach']) > 255) {
        $errors[] = 'Tên sách không được vượt quá 255 ký tự.';
    }

    $categoryIds = array_map(
        static fn($category) => (string) $category['id'],
        $categories
    );

    if (
        $form['danh_muc_id'] === ''
        || !in_array($form['danh_muc_id'], $categoryIds, true)
    ) {
        $errors[] = 'Vui lòng chọn danh mục hợp lệ.';
    }

    if (mb_strlen($form['isbn']) > 20) {
        $errors[] = 'ISBN không được vượt quá 20 ký tự.';
    } elseif ($form['isbn'] !== '') {
        if ($bookModel->isIsbnExists($form['isbn'], $id)) {
            $errors[] = 'ISBN này đã được sử dụng cho sách khác.';
        }
    }

    if (mb_strlen($form['nha_xuat_ban']) > 150) {
        $errors[] = 'Tên nhà xuất bản không được vượt quá 150 ký tự.';
    }

    if ($form['nam_xuat_ban'] !== '') {
        if (
            !ctype_digit($form['nam_xuat_ban'])
            || (int) $form['nam_xuat_ban'] < 1000
            || (int) $form['nam_xuat_ban'] > 2100
        ) {
            $errors[] = 'Năm xuất bản phải từ 1000 đến 2100.';
        }
    }

    if (!in_array($form['ngon_ngu'], ['vi', 'en'], true)) {
        $errors[] = 'Ngôn ngữ không hợp lệ.';
    }

    if ($errors) {
        $this->view('books/edit', [
            'title' => 'Sửa thông tin sách',
            'book' => $book,
            'categories' => $categories,
            'errors' => $errors,
            'form' => $form,
        ]);
        return;
    }

    
    try {
        // Giữ ảnh hiện tại nếu người dùng không chọn ảnh mới.
        $form['anh_bia'] = $this->uploadCover(
            $book['anh_bia'] ?? null
        );

        $bookModel->update($id, $form);

        flash('success', 'Cập nhật thông tin sách thành công.');
        $this->redirect(url('books'));
    } catch (RuntimeException $e) {
        $this->view('books/edit', [
            'title' => 'Sửa thông tin sách',
            'book' => $book,
            'categories' => $categories,
            'errors' => [$e->getMessage()],
            'form' => array_merge($book, $form),
        ]);
    } catch (PDOException $e) {
        $this->view('books/edit', [
            'title' => 'Sửa thông tin sách',
            'book' => $book,
            'categories' => $categories,
            'errors' => [
                'Không thể cập nhật sách. Hãy kiểm tra ISBN và dữ liệu nhập.'
            ],
            'form' => array_merge($book, $form),
        ]);
    }

}
// POST /books/delete/{id}
public function delete(int $id): void
{
    $this->requirePost();

    if ($id <= 0) {
        $this->abort(404, 'Không tìm thấy sách.');
        return;
    }

    $bookModel = $this->model('Book');

    try {
        $deleted = $bookModel->softDelete($id);

        if ($deleted) {
            flash('success', 'Đã xóa sách thành công.');
        } else {
            flash(
                'error',
                'Không thể xóa sách này. Có thể sách không tồn tại hoặc vẫn còn bản sao chưa thanh lý.'
            );
        }
    } catch (PDOException $e) {
        flash('error', 'Không thể xóa sách do dữ liệu đang được sử dụng.');
    }

    $this->redirect(url('books'));
}
}
