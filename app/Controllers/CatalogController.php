<?php
class CatalogController extends Controller {
    public function index() {
        $bookModel = $this->model('Book');
        
        // Kiểm tra xem người dùng có nhập từ khóa tìm kiếm không
        $keyword = $_GET['keyword'] ?? null;
        
        if (!empty($keyword)) {
            // Nếu có từ khóa, gọi hàm tìm kiếm trong Book Model
            $books = $bookModel->search($keyword);
        } else {
            // Nếu không có, lấy toàn bộ danh sách sách
            $books = $bookModel->all();
        }

        // Truyền dữ liệu ra view
        $this->view('catalog/index', [
            'books' => $books
        ]);
    }
}