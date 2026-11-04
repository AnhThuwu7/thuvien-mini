# Hệ thống Quản lý Thư viện Mini

Ứng dụng quản lý đầu sách, bản sao, độc giả và quy trình mượn–trả cho thư viện khoa / phòng đọc nhỏ.

**Công nghệ:** PHP 8.x · MySQL · PDO · MVC thuần  
**Học phần:** Lập trình Web — **Đề tài:** Chủ đề 2 (Quản lý thư viện mini)

**Repository:** https://github.com/AnhThuwu7/laptrinhwebb  
**Nhánh demo / tích hợp:** `integrate/all`

---

## 1. Thành viên & Phân công

| STT | Họ và tên | Mã SV | Phần việc chính | Branch Git |
|-----|-----------|-------|-----------------|------------|
| TV1 | Nguyễn Lê Anh Thư | 224001752 | **Nhóm trưởng** – MVC, Đăng nhập/phân quyền, CSRF, Tài khoản, Cấu hình | `feature/core-auth` |
| TV2 | Thiều Thị Ngấn | 224001737 | Quản lý Sách – Danh mục – Bản sao | `feature/book-crud` |
| TV3 | Nguyễn Thị Thanh Thư | 224001753 | Nghiệp vụ Mượn – Trả sách | `feature/loan-return` |
| TV4 | Lê Hoài Yến Nhi | 224001741 | Giao diện độc giả + Tra cứu sách | `feature/catalog-search` |
| TV5 | Nguyễn Thùy Dương | 224001715 | Báo cáo, kiểm thử, tài liệu | `feature/reports` |

Chi tiết: [docs/phan-cong.md](docs/phan-cong.md).

---

## 2. Tài khoản test (dùng khi nộp / demo)

**Mật khẩu chung các tài khoản seed:** `Demo@123`  
(Mật khẩu chỉ lưu dạng **hash** `password_hash` trong cột `mat_khau_bam`, không lưu plain text.)

| Email | Mật khẩu | Vai trò | Ghi chú |
|-------|----------|---------|---------|
| `admin@example.com` | `Demo@123` | Quản trị (`quan_tri`) | Toàn quyền cấu hình + tài khoản |
| `thuthu1@example.com` | `Demo@123` | Thủ thư (`thu_thu`) | Mượn–trả, kho sách, yêu cầu |
| `docgia01@example.com` | `Demo@123` | Độc giả (`doc_gia`) | Tra cứu, yêu cầu mượn, phiếu của tôi |
| `docgia04@example.com` | `Demo@123` | Độc giả (`doc_gia`) | **Bị khóa** – đăng nhập được nhưng không mượn |

> URL mặc định local: `http://localhost/thuvien-mini/public/`

---

## 3. Ma trận phân quyền (Bài 7)

Guard phía **server**: `Auth::check()`, `$this->requireLogin()`, `$this->requireRole(...)`.

| Chức năng / Route | Khách | Độc giả | Thủ thư | Admin |
|-------------------|:-----:|:-------:|:-------:|:-----:|
| `GET /` (trang chủ / catalog công khai) | ✓ | ✓ | ✓* | ✓* |
| `GET\|POST /auth/login` | ✓ | — | — | — |
| `GET\|POST /auth/register` | ✓ | — | — | — |
| `POST /auth/logout` | — | ✓ | ✓ | ✓ |
| `GET /auth/profile` | — | ✓ | ✓ | ✓ |
| `GET /catalog`, `GET /catalog/show/{id}` | ✓ | ✓ | ✓ | ✓ |
| `GET /loans/mine`, `GET /loans/show/{id}` | ✗ | ✓ | ✗ | ✗ |
| `GET\|POST /loan-requests/mine`, gửi yêu cầu | ✗ | ✓ | ✗ | ✗ |
| `GET\|POST /loans` (lập phiếu / trả sách) | ✗ | ✗ | ✓ | ✓ |
| `GET\|POST /loan-requests` (duyệt / fulfill) | ✗ | ✗ | ✓ | ✓ |
| `GET\|POST /books`, `/categories`, `/copies` | ✗ | ✗ | ✓ | ✓ |
| `GET /reports/stats`, `/reports/overdue` | ✗ | ✗ | ✓ | ✓ |
| `GET\|POST /users` (khóa TK, đổi vai trò) | ✗ | ✗ | ✗ | ✓ |
| `GET\|POST /settings` | ✗ | ✗ | ✗ | ✓ |

\* Thủ thư / admin đăng nhập thường được chuyển tới trang thống kê (`/reports/stats`).

**Ví dụ route được guard (Bài 7):**

- Route **admin:** `UsersController` / `SettingsController` → `$this->requireRole('quan_tri')`
- Route **độc giả (student/reader):** `LoansController::mine` → `$this->requireRole('doc_gia')`
- Route **thủ thư + admin:** `LoansController::index/store` → `$this->requireRole('thu_thu', 'quan_tri')`

Chưa đăng nhập mà gọi URL cần quyền → `requireLogin()` ghi nhớ URL → chuyển `/auth/login`.  
Đã đăng nhập nhưng **sai vai trò** → `403` (view lỗi 403).

---

## 4. Test case truy cập khi **chưa đăng nhập** (Bài 7 – cá nhân / nhóm)

| # | Thao tác | Kết quả kỳ vọng |
|---|----------|-----------------|
| 1 | Mở `.../public/loans` (chưa login) | Redirect `/auth/login` (không vào được trang mượn–trả) |
| 2 | Mở `.../public/users` (chưa login) | Redirect `/auth/login` |
| 3 | Mở `.../public/loans/mine` (chưa login) | Redirect `/auth/login` |
| 4 | Mở `.../public/settings` (chưa login) | Redirect `/auth/login` |
| 5 | Mở `.../public/catalog` (chưa login) | **Được xem** (catalog công khai) |
| 6 | Login độc giả → gõ `.../public/users` | **403** Không có quyền |
| 7 | Login độc giả → gõ `.../public/loans` | **403** (chỉ thủ thư/admin) |
| 8 | Login admin → `/users` | Vào được quản lý tài khoản |

**Ảnh nộp cô (gợi ý chụp):**

1. Chưa login, thanh địa chỉ `/loans` hoặc `/users` → màn hình đăng nhập.  
2. Login `docgia01@...` → vào `/users` → trang 403.  
3. (Tuỳ chọn) Form POST có input ẩn `csrf_token`.

---

## 5. Bảo mật đã tích hợp

| Biện pháp | Cách làm trong project |
|-----------|------------------------|
| Mật khẩu | `password_hash()` khi tạo/đổi; `password_verify()` khi login; cột `mat_khau_bam` |
| CSRF | Mọi form POST: `csrf_field()`; server kiểm tra token (`Csrf`) |
| Session | `session_regenerate_id` khi login; không lưu mật khẩu trong session |
| SQL Injection | PDO prepared statements |
| XSS | Hàm `e()` (`htmlspecialchars`) khi in ra view |
| Phân quyền | Guard trong Controller (server-side), không chỉ ẩn menu |
| Brute-force login | `LoginThrottle` (chặn tạm khi sai nhiều lần) |
| Tài khoản khóa | Vẫn login được + cảnh báo; **không** mượn / gửi yêu cầu |

Form đăng ký / quên mật khẩu (mở rộng): mật khẩu ≥ 8 ký tự, có hoa + thường + ký hiệu; SĐT đúng 10 số nếu nhập.

Chi tiết validation: [docs/validation-rules.md](docs/validation-rules.md).

---

## 6. Route chính (tóm tắt)

| Route | Method | Chức năng | Quyền |
|-------|--------|-----------|--------|
| `/` | GET | Trang chủ | Tất cả |
| `/auth/login` | GET, POST | Đăng nhập | Khách |
| `/auth/register` | GET, POST | Đăng ký độc giả | Khách |
| `/auth/forgot` | GET, POST | Quên mật khẩu (họ tên + SĐT) | Khách |
| `/auth/logout` | POST | Đăng xuất (+ CSRF) | Đã login |
| `/auth/profile` | GET | Hồ sơ | Đã login |
| `/catalog` | GET | Tra cứu sách | Tất cả |
| `/loans` | GET, POST | Mượn – trả | Thủ thư, Admin |
| `/loans/mine` | GET | Phiếu của tôi | Độc giả |
| `/loan-requests` | GET, POST | Duyệt / giữ chỗ | Thủ thư, Admin |
| `/books`, `/categories`, `/copies` | * | Kho sách | Thủ thư, Admin |
| `/users` | * | Tài khoản | Admin |
| `/settings` | * | Cấu hình | Admin |
| `/reports/stats` | GET | Thống kê | Thủ thư, Admin |

Đầy đủ hơn: [docs/routes.md](docs/routes.md).

---

## 7. Cài đặt & chạy

1. Clone repo vào thư mục web (`www` / `htdocs`).  
2. Copy `config/database.example.php` → `config/database.php`, điền MySQL.  
3. Import `database/schema.sql`, sau đó `database/seed.sql`.  
4. (Nếu có ảnh bìa đồng bộ nhóm) chạy thêm `database/fix-anh-bia.sql` nếu file này có trên nhánh.  
5. Mở: `http://localhost/thuvien-mini/public/`  
6. Đăng nhập bằng tài khoản mục **2**.

---

## 8. Checklist nộp cô (Bài 7)

| Nộp | Nội dung |
|-----|----------|
| Link repository | https://github.com/AnhThuwu7/laptrinhwebb (nhánh `integrate/all`) |
| Danh sách tài khoản test | Mục **2** trong README này |
| Ma trận quyền | Mục **3** |
| Ảnh bị chặn quyền | Chụp theo mục **4** (test case 1, 2, 6…) |
| Commit từng thành viên | Lịch sử GitHub theo branch/feature từng TV |

---

## 9. Tài liệu thêm trong repo

| File | Mô tả |
|------|--------|
| [docs/phan-cong.md](docs/phan-cong.md) | Phân công chi tiết |
| [docs/routes.md](docs/routes.md) | Danh sách route |
| [docs/test-auth.md](docs/test-auth.md) | Kiểm thử đăng nhập / quyền |
| [docs/validation-rules.md](docs/validation-rules.md) | Quy tắc validate |
| [docs/core-api.md](docs/core-api.md) | Ghi chú API/core |
