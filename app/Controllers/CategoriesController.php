
<?php

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->requireRole('quan_tri');
    }

    // GET /categories
    
public function index(): void
{
    $keyword = input('q');
    $categoryModel = $this->model('Category');

    $this->view('categories/index', [
        'title' => 'Quản lý danh mục',
        'categories' => $categoryModel->getAll($keyword),
        'keyword' => $keyword,
    ]);
}


    // POST /categories/store
    public function store(): void
    {
        $this->requirePost();

        $form = [
            'ten' => input('ten'),
            'mo_ta' => input('mo_ta'),
        ];

        if ($form['ten'] === '') {
            flash('error', 'Vui lòng nhập tên danh mục.');
            $this->redirect(url('categories'));
        }

        if (mb_strlen($form['ten']) > 100) {
            flash('error', 'Tên danh mục không được vượt quá 100 ký tự.');
            $this->redirect(url('categories'));
        }

        if (mb_strlen($form['mo_ta']) > 255) {
            flash('error', 'Mô tả không được vượt quá 255 ký tự.');
            $this->redirect(url('categories'));
        }

        $categoryModel = $this->model('Category');

        if ($categoryModel->isNameExists($form['ten'])) {
            flash('error', 'Tên danh mục này đã tồn tại.');
            $this->redirect(url('categories'));
        }

        try {
            $categoryModel->create($form);
            flash('success', 'Thêm danh mục thành công.');
        } catch (PDOException $e) {
            flash('error', 'Không thể thêm danh mục. Vui lòng kiểm tra dữ liệu.');
        }

        $this->redirect(url('categories'));
    }

    // GET /categories/edit/{id}
    public function edit(int $id): void
    {
        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy danh mục.');
        }

        $categoryModel = $this->model('Category');
        $category = $categoryModel->findById($id);

        if (!$category) {
            $this->abort(404, 'Không tìm thấy danh mục.');
        }

        $this->view('categories/edit', [
            'title' => 'Sửa danh mục',
            'category' => $category,
        ]);
    }

    // POST /categories/update/{id}
    public function update(int $id): void
    {
        $this->requirePost();

        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy danh mục.');
        }

        $categoryModel = $this->model('Category');
        $category = $categoryModel->findById($id);

        if (!$category) {
            $this->abort(404, 'Không tìm thấy danh mục.');
        }

        $form = [
            'ten' => input('ten'),
            'mo_ta' => input('mo_ta'),
        ];

        $errors = [];

        if ($form['ten'] === '') {
            $errors[] = 'Vui lòng nhập tên danh mục.';
        } elseif (mb_strlen($form['ten']) > 100) {
            $errors[] = 'Tên danh mục không được vượt quá 100 ký tự.';
        }

        if (mb_strlen($form['mo_ta']) > 255) {
            $errors[] = 'Mô tả không được vượt quá 255 ký tự.';
        }

        if (
            $form['ten'] !== ''
            && $categoryModel->isNameExists($form['ten'], $id)
        ) {
            $errors[] = 'Tên danh mục này đã được sử dụng.';
        }

        if (!empty($errors)) {
            $category['ten'] = $form['ten'];
            $category['mo_ta'] = $form['mo_ta'];

            $this->view('categories/edit', [
                'title' => 'Sửa danh mục',
                'category' => $category,
                'errors' => $errors,
            ]);
            return;
        }

        try {
            $categoryModel->update($id, $form);
            flash('success', 'Cập nhật danh mục thành công.');
            $this->redirect(url('categories'));
        } catch (PDOException $e) {
            $category['ten'] = $form['ten'];
            $category['mo_ta'] = $form['mo_ta'];

            $this->view('categories/edit', [
                'title' => 'Sửa danh mục',
                'category' => $category,
                'errors' => [
                    'Không thể cập nhật danh mục. Vui lòng thử lại.'
                ],
            ]);
        }
    }

    // POST /categories/delete/{id}
    public function delete(int $id): void
    {
        $this->requirePost();

        if ($id <= 0) {
            $this->abort(404, 'Không tìm thấy danh mục.');
        }

        $categoryModel = $this->model('Category');
        $category = $categoryModel->findById($id);

        if (!$category) {
            flash('error', 'Danh mục không tồn tại.');
            $this->redirect(url('categories'));
        }

        if ($categoryModel->hasBooks($id)) {
            flash(
                'error',
                'Không thể xóa danh mục đang có sách. Hãy chuyển sách sang danh mục khác trước.'
            );
            $this->redirect(url('categories'));
        }

        try {
            $categoryModel->delete($id);
            flash('success', 'Xóa danh mục thành công.');
        } catch (PDOException $e) {
            flash('error', 'Không thể xóa danh mục này.');
        }

        $this->redirect(url('categories'));
    }
}
