# Test case – Đăng nhập, phân quyền, CSRF (TV1)

Tài khoản trong `seed.sql`, mật khẩu chung **Demo@123**: `admin@example.com`, `thuthu1@example.com`, `docgia01@example.com`, `docgia04@example.com` (độc giả bị khóa).
Cột "Kết quả" để TV5 ghi Đạt / Không đạt và ngày test.

| Mã | Tình huống | Các bước | Kết quả mong đợi | Kết quả |
|---|---|---|---|---|
| AUTH-01 | Đăng nhập đúng – độc giả | Vào `/auth/login`, nhập `docgia01@example.com` / `Demo@123` | Về trang chủ, menu hiện tên và "Độc giả", có thông báo chào | |
| AUTH-02 | Đăng nhập đúng – thủ thư, admin | Lần lượt `thuthu1@…`, `admin@…` | Vào được, menu hiện đúng vai trò "Thủ thư", "Quản trị viên" | |
| AUTH-03 | Sai mật khẩu | Email đúng, mật khẩu `sai123` | Báo "Email hoặc mật khẩu không đúng.", không đăng nhập, ô email giữ lại, ô mật khẩu trống | |
| AUTH-04 | Email không tồn tại | `khongco@example.com` / `Demo@123` | Cùng thông báo như AUTH-03 (không cho biết email có tồn tại hay không) | |
| AUTH-05 | Để trống | Bấm Đăng nhập khi để trống 2 ô | Báo lỗi từng ô do **server** kiểm tra, không đăng nhập | |
| AUTH-06 | Email sai định dạng | Email `abc`, mật khẩu bất kỳ | Báo "Email không đúng định dạng." | |
| AUTH-07 | SQL injection | Email `' OR 1=1 --`; riêng lần 2: email đúng, mật khẩu `' OR '1'='1` | Không đăng nhập được, không lỗi SQL, dữ liệu không đổi | |
| AUTH-08 | XSS | Email `<script>alert(1)</script>` | Không hiện hộp thoại; nội dung hiển thị dạng chữ, không bị thực thi | |
| AUTH-09 | CSRF sai | F12 → sửa hoặc xóa giá trị ô ẩn `csrf_token` rồi bấm Đăng nhập | Bị từ chối (HTTP 403), không đăng nhập | |
| AUTH-10 | Chưa đăng nhập vào trang riêng tư | Chưa đăng nhập, mở `/auth/profile` | Chuyển về trang đăng nhập kèm cảnh báo; đăng nhập xong quay lại `/auth/profile` | |
| AUTH-11 | Đăng nhập rồi vào lại trang login | Đã đăng nhập, mở `/auth/login` | Tự chuyển về trang chủ | |
| AUTH-12 | Đăng xuất | Bấm "Đăng xuất" | Về trang đăng nhập, có thông báo; mở lại `/auth/profile` bị chuyển về login | |
| AUTH-13 | Back sau đăng xuất | Ở `/auth/profile`, đăng xuất, bấm nút Back của trình duyệt | Không xem lại được nội dung hồ sơ (bị chuyển về login) | |
| AUTH-14 | Đăng xuất bằng link (GET) | Đã đăng nhập, gõ URL `/auth/logout` | Không đăng xuất, báo "chỉ chấp nhận POST" (HTTP 405) | |
| AUTH-15 | URL không tồn tại | Mở `/abc`, `/auth/xyz`, `/auth/login/..` | Trang 404 | |
| AUTH-16 | Gọi hàm tiện ích qua URL | Mở `/auth/redirect/x`, `/auth/view/x`, `/auth/handleLogin` | Trang 404 (không gọi được hàm của lớp cơ sở hoặc hàm private) | |
| AUTH-17 | Đổi session sau đăng nhập | F12 → Application → Cookies: ghi `THUVIEN_SESSID` trước và sau khi đăng nhập | Giá trị cookie thay đổi (chống session fixation) | |
| AUTH-18 | Độc giả vào chức năng của thủ thư | (Làm khi `BooksController`/`LoansController` có `requireRole`) đăng nhập `docgia01`, gõ thẳng URL chức năng của thủ thư | Trang 403, dữ liệu không đổi; menu không hiện mục đó | |
| AUTH-19 | Độc giả bị khóa | Đăng nhập `docgia04@example.com` | Đăng nhập được; hồ sơ hiện "Đang bị khóa" và lý do. (Chặn mượn sách kiểm tra ở test của TV3) | |
