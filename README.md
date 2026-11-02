# Hệ thống Quản lý Thư viện Mini

Ứng dụng quản lý đầu sách, bản sao, độc giả và quy trình mượn–trả cho thư viện khoa hoặc phòng đọc nhỏ, xây dựng bằng PHP 8.x, MySQL, PDO và MVC.

**Học phần:** Lập trình Web — **Đề tài:** Chủ đề 2

---

## 1. Thành viên & Phân công nhiệm vụ

| STT | Họ và tên | Mã SV | Vai trò / Phần việc chính | Branch Git |
|-----|-----------|-------|---------------------------|------------|
| TV1 | Nguyễn Lê Anh Thư | 224001752 | **Nhóm trưởng** – Nền tảng MVC, Đăng nhập, Phân quyền, CSRF, Quản lý tài khoản, Cấu hình | `feature/core-auth` |
| TV2 | Thiều Thị Ngấn | 224001737 | Quản lý Sách – Danh mục – Bản sao | `feature/book-crud` |
| TV3 | Nguyễn Thị Thanh Thư | 224001753 | Nghiệp vụ Mượn – Trả sách | `feature/loan-return` |
| TV4 | Lê Hoài Yến Nhi | 224001741 | Giao diện Độc giả + Tra cứu sách | `feature/catalog-search` |
| TV5 | Nguyễn Thùy Dương | 224001715 | Dữ liệu, Báo cáo, Kiểm thử, Slide & Tài liệu | `feature/reports` |

 Chi tiết lộ trình, phân công test & mốc thời gian: xem tại [docs/phan-cong.md](docs/phan-cong.md).

---

## 2. Form an toàn & Quy tắc Validation đã tích hợp

Project nhóm đã tích hợp các **Form xử lý an toàn** đạt chuẩn bảo mật theo yêu cầu Buổi 3:
- **Xác thực Đăng nhập (`/auth/login`)**: Kiểm tra bắt buộc, chuẩn hóa email, chống Brute-Force/Timing Attack.
- **Quản lý Tài khoản (`/users/toggle-lock`)**: Kiểm tra lý do khóa, phân quyền Admin, chống tự khóa chính mình.
- **Cấu hình Hệ thống (`/settings`)**: Kiểm tra khoảng giá trị hợp lệ của hạn mức mượn.

### Các biện pháp bảo mật áp dụng:
1. **Server-side Validation**: Kiểm tra dữ liệu bắt buộc, đúng định dạng, giới hạn độ dài/khoảng giá trị phía Server trước khi lưu vào CSDL.
2. **Chống XSS**: Mã hóa toàn bộ dữ liệu đầu ra bằng `htmlspecialchars()` qua hàm `e()`.
3. **Chống SQL Injection**: 100% truy vấn CSDL sử dụng **PDO Prepared Statements** truyền tham số.
4. **Chống CSRF**: Mọi form gửi dữ liệu `POST` đều bắt buộc chứa `csrf_token` hợp lệ.
5. **Giữ lại dữ liệu cũ (Old input)**: Tự động giữ lại giá trị hợp lệ đã nhập khi form báo lỗi.

👉 Chi tiết bảng quy tắc Validation từng trường: xem tại [docs/validation-rules.md](docs/validation-rules.md).

---

## 3. Danh sách Route chính của Hệ thống

Hệ thống hoạt động theo quy ước MVC routing: `/controller/method/tham-so`.

| Route | Phương thức | Chức năng chính | Quyền truy cập |
|-------|------------|-----------------|----------------|
| `/` | GET | Trang chủ hệ thống | Tất cả |
| `/auth/login` | GET, POST | Đăng nhập hệ thống | Khách |
| `/auth/logout` | POST | Đăng xuất (Có CSRF) | Đã đăng nhập |
| `/auth/profile` | GET | Xem hồ sơ cá nhân | Đã đăng nhập |
| `/catalog` | GET | Tra cứu sách đa tiêu chí | Đã đăng nhập |
| `/books` | GET, POST | Quản lý kho sách & Bản sao | Thủ thư, Admin |
| `/loans` | GET, POST | Xử lý mượn – trả sách | Thủ thư, Admin |
| `/loans/mine` | GET | Lịch sử mượn cá nhân | Độc giả |
| `/users` | GET, POST | Quản lý tài khoản & Khóa/Mở khóa | Admin |
| `/settings` | GET, POST | Cấu hình hạn mức mượn / phạt | Admin |

 Xem đầy đủ danh sách 25+ routes dự kiến: [docs/routes.md](docs/routes.md).

---

## 4. Cài đặt & Chạy ứng dụng

1. Clone repository về thư mục web (`htdocs` hoặc `www`).
2. Copy `config/database.example.php` thành `config/database.php` và điền cấu hình MySQL.
3. Import cơ sở dữ liệu: `database/schema.sql` rồi đến `database/seed.sql`.
4. Truy cập hệ thống: `http://localhost/thuvien-mini/public/`.
5. **Tài khoản test mặc định (mật khẩu: `Demo@123`):**
   - Admin: `admin@example.com`
   - Thủ thư: `thuthu1@example.com`
   - Độc giả: `docgia01@example.com`