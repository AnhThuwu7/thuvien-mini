# Hệ thống Quản lý Thư viện Mini

**Học phần:** Lập trình Web
**Đề tài:** Chủ đề 2 – Hệ thống Quản lý Thư viện Mini
**Công nghệ:** PHP 8.x, MySQL, PDO, MVC, HTML/CSS/JavaScript (Fetch API), Bootstrap

Ứng dụng web quản lý đầu sách, bản sao, độc giả và quy trình mượn–trả cho thư viện khoa hoặc phòng đọc nhỏ. Có 3 vai trò: **Độc giả**, **Thủ thư**, **Quản trị viên**.

## 1. Thành viên nhóm

| STT | Họ và tên | Mã SV | Vai trò / Phần việc chính |
|-----|-----------|-------|---------------------------|
| TV1 | Nguyễn Lê Anh Thư | 224001752 | Nhóm trưởng – Nền tảng, Auth, Phân quyền |
| TV2 | Thiều Thị Ngấn | 224001737 | Quản lý Sách – Danh mục – Bản sao |
| TV3 | Nguyễn Thị Thanh Thư | 224001753 | Nghiệp vụ Mượn – Trả |
| TV4 | Lê Hoài Yến Nhi | 224001741 | Giao diện Độc giả + Tra cứu |
| TV5 | Nguyễn Thùy Dương | 224001715 | Dữ liệu, Báo cáo, Kiểm thử, Tài liệu |

## 2. Phân công công việc (tổng quan)

| TV | Vai trò chính | Bảng CSDL chính | Màn hình / Endpoint chính |
|----|---------------|-----------------|---------------------------|
| **TV1** | Nền tảng + Auth + Phân quyền | `users`, `settings` | Đăng nhập, QL tài khoản, `/api/users/check` |
| **TV2** | Quản lý Sách – Danh mục – Bản sao | `categories`, `authors`, `books`, `book_copies` | CRUD Sách/Danh mục/Bản sao, `/api/copies/{id}/status` |
| **TV3** | Nghiệp vụ lõi Mượn – Trả | `loans`, `loan_logs` | Giao diện thủ thư mượn–trả, `/api/users/search` |
| **TV4** | Giao diện Độc giả + Tra cứu | dùng chung + `reservations` (MR) | Trang chủ, Tìm kiếm, Chi tiết, Lịch sử mượn, `/api/books/suggest` |
| **TV5** | Dữ liệu + Báo cáo + Kiểm thử + Tài liệu | Sở hữu `database.sql` | Báo cáo quá hạn, Thống kê, `/api/stats/overview` |
