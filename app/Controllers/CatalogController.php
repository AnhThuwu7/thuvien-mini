<?php

class CatalogController extends Controller
{
    public function index(): void
    {
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
            'isReader' => Auth::check() && Auth::hasRole('doc_gia'),
            'isGuest' => !Auth::check(),
            'isStaff' => Auth::check() && Auth::hasRole('thu_thu', 'quan_tri'),
        ]);
    }

    public function show(int $id): void
    {
        $bookModel = $this->model('Book');
        $book = $bookModel->findById($id);
        if ($book === null) {
            $this->abort(404);
        }

        $tenTacGia = $bookModel->getAuthorsForBook($id);
        if ($tenTacGia === '' && !empty($book['ten_tac_gia'])) {
            $tenTacGia = (string) $book['ten_tac_gia'];
        }

        $tenDanhMuc = '';
        foreach ($bookModel->getCategories() as $c) {
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
            'ten_tac_gia' => $tenTacGia,
            'copies' => $copies,
            'so_con_trong' => $soConTrong,
            'tong_ban_sao' => count($copies),
            'isReader' => Auth::check() && Auth::hasRole('doc_gia'),
            'isGuest' => !Auth::check(),
            'isStaff' => Auth::check() && Auth::hasRole('thu_thu', 'quan_tri'),
        ]);
    }

    public function request(int $id): void
    {
        if (Auth::check() && Auth::hasRole('thu_thu', 'quan_tri')) {
            flash('info', 'Thủ thư/admin lập phiếu mượn tại mục Mượn–Trả.');
            $this->redirect(url('loans'));
            return;
        }

        $this->requireLogin();

        if (!Auth::hasRole('doc_gia')) {
            $this->abort(403);
        }

        $this->redirect(url('catalog/show/' . $id));
    }
}