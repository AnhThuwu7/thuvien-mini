<?php

class CatalogController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $bookModel = $this->model('Book');
        $keyword = trim((string) ($_GET['keyword'] ?? ''));
        $publisher = trim((string) ($_GET['publisher'] ?? ''));
        $category = trim((string) ($_GET['category'] ?? ''));
        $status = trim((string) ($_GET['status'] ?? ''));

        $books = $keyword !== ''
            ? $bookModel->search($keyword)
            : $bookModel->all();

        $books = array_values(array_filter($books, function (array $book) use ($publisher, $category, $status) {
            if ($publisher !== '' && mb_stripos((string) ($book['nha_xuat_ban'] ?? ''), $publisher) === false) {
                return false;
            }
            if ($category !== '' && (string) ($book['danh_muc_id'] ?? '') !== $category) {
                return false;
            }
            $available = (int) ($book['so_con_trong'] ?? 0);
            if ($status === 'con_sach' && $available < 1) {
                return false;
            }
            if ($status === 'het_sach' && $available > 0) {
                return false;
            }
            return true;
        }));

        $this->view('catalog/index', [
            'title' => 'Tra cứu sách',
            'books' => $books,
            'categories' => $bookModel->getCategories(),
            'keyword' => $keyword,
            'publisher' => $publisher,
            'category' => $category,
            'status' => $status,
        ]);
    }

    public function show(int $id): void
    {
        $this->requireLogin();

        $book = $this->model('Book')->findById($id);
        if ($book === null) {
            $this->abort(404);
        }

        $tenDanhMuc = '';
        foreach ($this->model('Book')->getCategories() as $c) {
            if ((int) $c['id'] === (int) ($book['danh_muc_id'] ?? 0)) {
                $tenDanhMuc = $c['ten'];
                break;
            }
        }

        $copies = $this->model('Copy')->getByBookId($id);
        $soConTrong = 0;
        foreach ($copies as $c) {
            if (($c['trang_thai'] ?? '') === 'con_trong') {
                $soConTrong++;
            }
        }

        $this->view('catalog/show', [
            'title' => $book['ten_sach'] ?? 'Chi tiết sách',
            'book' => $book,
            'ten_danh_muc' => $tenDanhMuc,
            'copies' => $copies,
            'so_con_trong' => $soConTrong,
            'tong_ban_sao' => count($copies),
        ]);
    }
}