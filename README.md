# Hệ thống Quản lý Thư viện Mini

Ứng dụng web quản lý đầu sách, bản sao, độc giả và quy trình mượn–trả cho thư viện khoa / phòng đọc nhỏ.

**Học phần:** Lập trình Web  
**Đề tài:** Chủ đề 2 – Hệ thống Quản lý Thư viện Mini  
**Công nghệ:** PHP 8.x · MySQL · PDO · MVC tự xây · HTML/CSS/JavaScript · Fetch API

---

## 1. Thành viên & phân công

| STT | Họ và tên | Mã SV | Phần việc chính | Nhánh Git gợi ý |
|-----|-----------|-------|-----------------|-----------------|
| TV1 | Nguyễn Lê Anh Thư | 224001752 | Nhóm trưởng – MVC, Auth, phân quyền, CSRF, tài khoản, cấu hình | `feature/core-auth` |
| TV2 | Thiều Thị Ngấn | 224001737 | CRUD Sách – Danh mục – Bản sao | `feature/book-crud` |
| TV3 | Nguyễn Thị Thanh Thư | 224001753 | Nghiệp vụ mượn – trả, yêu cầu mượn | `feature/loan-return` |
| TV4 | Lê Hoài Yến Nhi | 224001741 | Giao diện độc giả, tra cứu sách | `feature/catalog-search` |
| TV5 | Nguyễn Thùy Dương | 224001715 | Dữ liệu, báo cáo, kiểm thử, tài liệu | `feature/reports` |

Chi tiết phân công: [docs/phan-cong.md](docs/phan-cong.md)

**Repository nhóm:** *(điền link GitHub nhóm)*  
**Repository cá nhân:** mỗi thành viên có repo / commit riêng theo yêu cầu nộp.

---

## 2. Chức năng đã làm (cập nhật theo tiến độ)

### Đã hoàn thành

| Module | Chức năng | Phụ trách |
|--------|-----------|-----------|
| **Nền tảng** | Cấu trúc MVC, Router, PDO wrapper, session, flash message | TV1 |
| **Auth** | Đăng nhập / đăng xuất, `password_hash`, chống timing attack | TV1 |
| **Phân quyền** | 3 vai trò: `doc_gia`, `thu_thu`, `quan_tri`; `requireLogin` / `requireRole` | TV1 |
| **Bảo mật** | CSRF mọi POST, XSS (`e()`), PDO prepared statement toàn bộ truy vấn | TV1 |
| **Tài khoản** | Danh sách user, khóa/mở khóa, đổi vai trò | TV1 |
| **Cấu hình** | Hạn mức mượn / số ngày mượn (settings) | TV1 |
| **Danh mục** | CRUD danh mục sách (thêm trên trang list, sửa, xóa có chặn khi còn sách) | TV2 |
| **Sách** | CRUD đầu sách, tìm kiếm, upload ảnh bìa, xóa mềm | TV2 |
| **Bản sao** | CRUD bản sao, lọc theo trạng thái, **sinh mã tự động** `BK{id}-{stt}` | TV2 |
| **Mượn – trả** | Lập phiếu mượn, xác nhận trả, thống kê, tìm/lọc, phân trang | TV3 |
| **Lịch sử mượn** | Xem toàn hệ thống (thủ thư/admin) và `/loans/mine` (độc giả) | TV3 |
| **Yêu cầu mượn** | Duyệt / từ chối yêu cầu của độc giả | TV3 |

### Đang / sẽ làm

| Module | Ghi chú | Phụ trách |
|--------|---------|-----------|
| Tra cứu catalog (độc giả) | Tìm sách, chi tiết, autocomplete JSON | TV4 |
| Báo cáo quá hạn / thống kê | Trang báo cáo, xuất dữ liệu nếu cần | TV5 |
| Checklist test ≥ 15 case | Gồm dữ liệu sai & truy cập trái quyền | TV5 |

---

## 3. Vai trò người dùng

| Vai trò | Quyền chính |
|---------|-------------|
| **Độc giả** | Tra cứu sách, xem lịch sử mượn của mình, gửi yêu cầu mượn |
| **Thủ thư** | Quản lý sách/bản sao, lập phiếu mượn–trả, xử lý yêu cầu, xem quá hạn |
| **Quản trị** | Toàn quyền thủ thư + quản lý tài khoản, cấu hình hệ thống |

---

## 4. Danh sách route chính

Quy ước URL: `/controller/method/tham-so` (method dạng `kebab-case` trên URL).

### Công khai / Auth

| Route | Method | Mô tả | Quyền |
|-------|--------|-------|-------|
| `/` | GET | Trang chủ | Tất cả |
| `/auth/login` | GET, POST | Đăng nhập | Khách |
| `/auth/logout` | POST | Đăng xuất (CSRF) | Đã đăng nhập |
| `/auth/profile` | GET | Hồ sơ cá nhân | Đã đăng nhập |

### Kho sách (TV2)

| Route | Method | Mô tả | Quyền |
|-------|--------|-------|-------|
| `/books` | GET | Danh sách đầu sách | Thủ thư, Admin |
| `/books/create` | GET | Form thêm sách | Thủ thư, Admin |
| `/books/store` | POST | Lưu sách mới | Thủ thư, Admin |
| `/books/show/{id}` | GET | Chi tiết sách | Thủ thư, Admin |
| `/books/edit/{id}` | GET | Form sửa sách | Thủ thư, Admin |
| `/books/update/{id}` | POST | Cập nhật sách | Thủ thư, Admin |
| `/books/delete/{id}` | POST | Xóa mềm sách | Thủ thư, Admin |
| `/categories` | GET | Danh sách / thêm danh mục | Thủ thư, Admin |
| `/categories/store` | POST | Lưu danh mục | Thủ thư, Admin |
| `/categories/edit/{id}` | GET | Sửa danh mục | Thủ thư, Admin |
| `/categories/update/{id}` | POST | Cập nhật danh mục | Thủ thư, Admin |
| `/categories/delete/{id}` | POST | Xóa danh mục | Thủ thư, Admin |
| `/copies` | GET | Danh sách bản sao | Thủ thư, Admin |
| `/copies/create` | GET | Form thêm bản sao | Thủ thư, Admin |
| `/copies/store` | POST | Lưu bản sao | Thủ thư, Admin |
| `/copies/suggest-code` | GET | JSON gợi ý mã `BK00xx-yy` | Thủ thư, Admin |
| `/copies/edit/{id}` | GET | Sửa bản sao | Thủ thư, Admin |
| `/copies/update/{id}` | POST | Cập nhật bản sao | Thủ thư, Admin |
| `/copies/delete/{id}` | POST | Xóa mềm bản sao | Thủ thư, Admin |

### Mượn – trả (TV3)

| Route | Method | Mô tả | Quyền |
|-------|--------|-------|-------|
| `/loans` | GET | Quản lý phiếu mượn | Thủ thư, Admin |
| `/loans/create` | GET | Form lập phiếu mượn | Thủ thư, Admin |
| `/loans/store` | POST | Tạo phiếu mượn | Thủ thư, Admin |
| `/loans/return-book/{id}` | POST | Xác nhận trả sách | Thủ thư, Admin |
| `/loans/history` | GET | Lịch sử mượn toàn hệ thống | Thủ thư, Admin |
| `/loans/mine` | GET | Lịch sử mượn của độc giả | Độc giả |
| `/loan-requests` | GET | Danh sách yêu cầu mượn | Thủ thư, Admin |
| `/loan-requests/approve/{id}` | POST | Duyệt yêu cầu | Thủ thư, Admin |
| `/loan-requests/reject/{id}` | POST | Từ chối yêu cầu | Thủ thư, Admin |

### Admin (TV1)

| Route | Method | Mô tả | Quyền |
|-------|--------|-------|-------|
| `/users` | GET | Quản lý tài khoản | Admin |
| `/users/toggle-lock` | POST | Khóa / mở khóa | Admin |
| `/users/change-role` | POST | Đổi vai trò | Admin |
| `/settings` | GET, POST | Cấu hình hạn mức | Admin |
| `/api/users?q=` | GET | JSON tìm user (Fetch API) | Thủ thư, Admin |

Danh sách đầy đủ / trạng thái từng route: [docs/routes.md](docs/routes.md)

---

## 5. Ảnh màn hình (minh họa chức năng)

| STT | Màn hình | File |
|-----|----------|------------|
| 1 | Trang đăng nhập | `docs/screenshots/01-login.png` |
| 2 | Trang chủ sau đăng nhập | `docs/screenshots/02-home.png` |
| 3 | Quản lý sách | `docs/screenshots/03-book.png` |
| 4 | Quản lý bản sao | `docs/screenshots/03-copies.png` |
| 5 | Quản lý danh mục | `docs/screenshots/03-catalogue.png` |
| 6 | Quản lý tài khoản | `docs/screenshots/04-users.png` |

## 6. Cơ sở dữ liệu

- **Schema:** [database/schema.sql](database/schema.sql) — bảng, khóa chính/ngoại, ràng buộc, UTF-8  
- **Dữ liệu mẫu:** [database/seed.sql](database/seed.sql) — đủ demo đăng nhập, sách, bản sao, phiếu mượn  

Mọi truy vấn trong code dùng **PDO prepared statement** (không nối chuỗi SQL từ input).

---

## 7. Yêu cầu môi trường & cài đặt

### Môi trường

- PHP **8.x** (khuyến nghị 8.2+)
- MySQL 8 / MariaDB
- Apache (WAMP / XAMPP) — document root trỏ tới thư mục `public/` (hoặc truy cập qua `/thuvien-mini/public/`)
- Git

### Các bước cài đặt

1. Clone repository vào web root (`C:\wamp64\www` hoặc `C:\xampp\htdocs`):
   ```bash
   git clone <URL-repo-nhóm> thuvien-mini
   cd thuvien-mini
   ```
2. Sao chép cấu hình CSDL:
   ```bash
   copy config\database.example.php config\database.php
   ```
   Sửa host, tên DB, user, password cho máy local.
3. Tạo database và import:
   - Chạy `database/schema.sql`
   - Chạy `database/seed.sql`
4. (Nếu dùng Composer) `composer install`
5. Mở trình duyệt:
   ```
   http://localhost/thuvien-mini/public/
   ```

### Tài khoản demo (mật khẩu chung: `Demo@123`)

| Email | Vai trò |
|-------|---------|
| `admin@example.com` | Quản trị |
| `thuthu1@example.com` | Thủ thư |
| `docgia01@example.com` | Độc giả |
| `docgia04@example.com` | Độc giả (tài khoản bị khóa — test chặn mượn) |

---

## 8. Quy ước Git (nhóm)

- Không commit thẳng lên `main`.
- Mỗi người làm trên nhánh `feature/<ten-viec>`.
- Commit nhỏ, message rõ (vd. `TV2: CRUD ban sao + sinh ma tu dong`).
- Xong phần → Push → mở **Pull Request** vào `main` → được review rồi mới merge.
- Mỗi thành viên phải có commit thể hiện phần việc của mình (theo yêu cầu nộp).

```bash
git checkout main
git pull origin main
git checkout -b feature/ten-viec
# ... code ...
git add -A
git commit -m "TV2: mo ta ngan"
git push -u origin feature/ten-viec
```

---

## 9. Cấu trúc thư mục (rút gọn)

```
thuvien-mini/
├── app/
│   ├── Controllers/     # Auth, Books, Categories, Copies, Loans, Users, ...
│   ├── Models/
│   └── Views/
├── config/              # database.php (local, không commit), menu, app
├── core/                # Router, Controller, Model, Auth, Database, helpers
├── database/
│   ├── schema.sql
│   └── seed.sql
├── docs/                # phân công, routes, screenshots, validation
├── public/              # index.php (front controller), css, uploads
└── README.md
```

---

## 10. Ghi chú bảo mật (đã áp dụng)

1. **Prepared statement** cho mọi truy vấn có tham số.  
2. **password_hash / password_verify** (bcrypt).  
3. **CSRF token** trên mọi form POST.  
4. **XSS:** output qua `e()` (`htmlspecialchars`).  
5. **Phân quyền server-side** (`requireRole`) — không chỉ ẩn nút trên giao diện.  
6. Không commit `config/database.php` (mật khẩu CSDL) lên repo công khai.

---

*Cập nhật README theo tiến độ từng sprint. Khi TV4/TV5 merge, bổ sung dòng chức năng + ảnh màn hình tương ứng.*
