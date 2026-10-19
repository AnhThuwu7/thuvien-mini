# Hướng dẫn dùng phần nền (core) – dành cho cả nhóm và AI

> Dán file này (cùng `database/schema.sql`) vào đầu cuộc trò chuyện với AI trước khi nhờ viết code.

## 1. Quy ước cấu trúc

| Thành phần | Vị trí | Ví dụ |
|---|---|---|
| Controller | `app/Controllers/<Ten>Controller.php` (tên 1 từ: Books, Loans...) | `BooksController` |
| Model | `app/Models/<Ten>.php`, `class Ten extends Model` | `Book`, `Loan`, `User` |
| View | `app/Views/<thu-muc>/<ten>.php` (chỉ chứa phần nội dung, header/footer tự thêm) | `books/index.php` |
| URL | `/controller/method/tham-so` → `Controller::method(tham-so)` | `/books/edit/12` → `BooksController::edit('12')` |

- Method của controller muốn truy cập qua URL phải là `public`. Hàm phụ dùng nội bộ để `private`.
- **Không viết SQL trong Controller hay View.** SQL chỉ nằm trong Model, luôn dùng prepared statement (dấu `?`).
- Chỉ lưu đường dẫn ảnh/tệp trong DB, tệp upload lưu ngoài thư mục thực thi web (`storage/uploads`).

## 2. Hàm tiện ích (core/helpers.php)

| Hàm | Công dụng |
|---|---|
| `e($x)` | Mã hóa khi in ra HTML. **Luôn dùng:** `<?= e($sach['ten_sach']) ?>` |
| `url('books/edit/5')` | URL trong dự án. Không viết đường dẫn cứng hay `../` |
| `asset('css/app.css')` | Đường dẫn tệp tĩnh trong `public/` (css, js, img) |
| `csrf_field()` | Ô ẩn CSRF, đặt trong **mọi** form `method="post"` |
| `flash('success', 'Đã lưu')` | Thông báo hiện ở trang kế tiếp (`success`, `danger`, `warning`, `info`) |

## 3. Trong Controller

```php
$this->view('books/index', ['title' => 'Quản lý sách', 'books' => $books]);  // có layout
$this->redirect(url('books'));
$this->json(['ok' => true]);                 // cho Fetch; lỗi: $this->json(['error' => '...'], 404)
$this->requirePost();                        // đầu mọi action thay đổi dữ liệu
$this->requireLogin();                       // cần đăng nhập
$this->requireRole('thu_thu', 'quan_tri');   // cần đúng vai trò (sai quyền -> 403)
$this->abort(404);                           // ID không tồn tại; $this->abort(403) sai quyền
$model = $this->model('Book');               // nạp app/Models/Book.php
```

Vai trò: `doc_gia`, `thu_thu`, `quan_tri`.
Người đang đăng nhập: `Auth::check()`, `Auth::id()`, `Auth::role()`, `Auth::user()`, `Auth::hasRole('thu_thu')`.

## 4. Trong Model (core/Model.php)

```php
$this->fetchAll($sql, [$a, $b]);   // mảng các dòng
$this->fetchOne($sql, [$id]);      // 1 dòng hoặc null
$this->execute($sql, [...]);       // INSERT/UPDATE/DELETE, trả số dòng ảnh hưởng
$this->lastInsertId();
$this->transaction(function () { ... });   // mượn/trả: mọi bước cùng thành công hoặc cùng hủy
```

- `LIMIT`/`OFFSET`: ép `(int)` rồi nối vào câu SQL (đã tắt giả lập prepared statement nên không bind chuỗi được).
- ID lấy từ URL luôn ép `(int)`; không tìm thấy bản ghi → `$this->abort(404)`.

## 5. Mẫu một action thay đổi dữ liệu (POST)

```php
public function store()
{
    $this->requirePost();
    $this->requireRole('thu_thu', 'quan_tri');     // kiểm quyền Ở SERVER

    $ten = trim((string) ($_POST['ten'] ?? ''));
    if ($ten === '') {                             // validation ở server
        flash('danger', 'Vui lòng nhập tên.');
        $this->redirect(url('categories/create'));
    }

    $this->model('Category')->create($ten);        // SQL nằm trong Model
    flash('success', 'Đã thêm danh mục.');
    $this->redirect(url('categories'));
}
```
(CSRF đã được kiểm tập trung trong `public/index.php`, nhưng form vẫn phải có `<?= csrf_field() ?>`.)

## 6. Mẫu endpoint Fetch (JSON)

PHP:
```php
public function suggestBooks()
{
    $this->requireLogin(true);                      // true: trả 401 thay vì chuyển trang
    $q = trim((string) ($_GET['q'] ?? ''));
    $this->json($this->model('Book')->suggest($q)); // trả mảng, rỗng thì []
}
```
JavaScript:
```js
const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
if (!res.ok) { /* xử lý lỗi 401/403/404/500, không giả định luôn thành công */ }
const data = await res.json();
```
Fetch gửi POST: thêm header `'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content`.

## 7. Quy tắc nghiệp vụ nhớ khi viết code (schema v1.2)

- Mượn/trả/giữ chỗ luôn trong **một transaction**: cập nhật `phieu_muon` và `ban_sao.trang_thai` cùng lúc.
- Tài khoản `bi_khoa = 1` **vẫn đăng nhập được nhưng không được mượn**; kiểm tra bằng dữ liệu đọc từ DB lúc mượn, không tin session.
- Xóa bằng `xoa_luc` (xóa mềm) hoặc khóa tài khoản; không `DELETE` cứng bản ghi đã có lịch sử.
- Hạn mức, số ngày mượn, tiền phạt đọc từ bảng `cau_hinh`, không viết cứng trong code.
