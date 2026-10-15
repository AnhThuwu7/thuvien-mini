# Hệ thống Quản lý Thư viện Mini

Ứng dụng quản lý đầu sách, bản sao, độc giả và quy trình mượn–trả cho thư viện khoa hoặc phòng đọc nhỏ, xây dựng bằng PHP 8.x, MySQL, PDO và MVC.

**Học phần:** Lập trình Web — **Đề tài:** Chủ đề 2

## Thành viên và phân công

| STT | Họ và tên | Mã SV | Vai trò / Phần việc chính |
|-----|-----------|-------|---------------------------|
| TV1 | Nguyễn Lê Anh Thư | 224001752 | Nhóm trưởng – Nền tảng, Auth, Phân quyền |
| TV2 | Thiều Thị Ngấn | 224001737 | Quản lý Sách – Danh mục – Bản sao |
| TV3 | Nguyễn Thị Thanh Thư | 224001753 | Nghiệp vụ Mượn – Trả |
| TV4 | Lê Hoài Yến Nhi | 224001741 | Giao diện Độc giả + Tra cứu |
| TV5 | Nguyễn Thùy Dương | 224001715 | Dữ liệu, Báo cáo, Kiểm thử, Tài liệu |

Phân công chi tiết từng người, mốc thời gian và phân công test: xem [docs/phan-cong.md](docs/phan-cong.md).

## Vai trò người dùng
- Độc giả: tìm sách, xem trạng thái bản sao, xem lịch sử mượn của mình.
- Thủ thư: ghi nhận mượn–trả, cập nhật sách, xử lý quá hạn.
- Admin: quản lý tài khoản, danh mục và cấu hình hạn mức mượn.

## Chức năng cốt lõi
1. Đăng nhập, đăng xuất và phân quyền (độc giả, thủ thư, admin).
2. CRUD sách, danh mục và bản sao sách.
3. Mượn–trả sách có kiểm tra quy tắc (bản sao đang mượn, tài khoản bị khóa, hạn mức mượn).
4. Tìm kiếm đa tiêu chí, lọc và phân trang.
5. Danh sách lượt mượn sắp đến hạn hoặc quá hạn, thống kê.
6. Autocomplete tên sách bằng Fetch API và endpoint JSON.

## Yêu cầu môi trường
- PHP 8.2 trở lên; MySQL/MariaDB; Composer; Git.
- Web root: `C:\wamp64\www` hoặc `C:\xampp\htdocs`.

## Cài đặt
1. Clone repository vào web root.
2. Chạy `composer install` (khi dự án có `composer.json`).
3. Sao chép `config/database.example.php` thành `config/database.php` và sửa cấu hình local.
4. Import `database/schema.sql`, sau đó `database/seed.sql`.
5. Truy cập `http://localhost/thuvien-mini/public/`.

## Quy ước Git
- Không làm trực tiếp trên `main`; tạo nhánh `feature/<ten-chuc-nang>`.
- Mỗi commit chỉ chứa một thay đổi có ý nghĩa.
- Tạo Pull Request để thành viên khác kiểm tra trước khi merge.