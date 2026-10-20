# Phân công chi tiết nhiệm vụ thành viên & Lộ trình thực hiện

Mỗi thành viên phụ trách một mảng chức năng trọn gói từ **Route $\rightarrow$ Controller $\rightarrow$ Model $\rightarrow$ View**, tự viết Test Case và bảo vệ phần việc của mình.

| Thành viên | Phần việc đảm nhận | Controller / Model chính | Nhánh Git (Branch) |
|---|---|---|---|
| **TV1 – Nguyễn Lê Anh Thư** | Core MVC, Đăng nhập, Phân quyền, CSRF, Quản lý tài khoản, Cấu hình hệ thống, API Độc giả | Auth, Users, Settings, Api / User, Setting | `feature/core-auth` |
| **TV2 – Thiều Thị Ngấn** | Quản lý Kho sách, Danh mục, Bản sao (CRUD, kiểm tra trùng ISBN) | Books, Copies, Categories / Book, Copy, Category | `feature/book-crud` |
| **TV3 – Nguyễn Thị Thanh Thư**| Nghiệp vụ Mượn – Trả (Transaction, hạn mức, quá hạn, lịch sử mượn) | Loans / Loan | `feature/loan-return` |
| **TV4 – Lê Hoài Yến Nhi** | Giao diện Độc giả: Tra cứu sách đa tiêu chí, Chi tiết sách, Autocomplete Fetch API | Catalog, Api::suggestBooks / Catalog | `feature/catalog-search` |
| **TV5 – Nguyễn Thùy Dương** | Báo cáo quá hạn/thống kê, ERD CSDL, Checklist test, Slide & Tài liệu | Reports / Report | `feature/reports` |

---

## Các mốc thời gian hoàn thành (Milestones)

| Mốc thời gian | Sản phẩm cần nộp | Thành viên thực hiện |
|---|---|---|
| **Sau Buổi 3** | Wireframe, Quy tắc Validation, Danh sách Routes, README nhóm | Cả nhóm |
| **Sau Buổi 4** | Thiết kế CSDL (ERD + SQL schema version 1) | TV1 (SQL), TV5 (ERD) |
| **Sau Buổi 5** | Chức năng CRUD cơ bản (PDO Prepared Statement, báo lỗi) | TV2, TV1 |
| **Sau Buổi 6** | Hoàn thiện mô hình MVC (Không viết SQL trực tiếp trong View) | Cả nhóm |
| **Sau Buổi 7** | Tích hợp Đăng nhập, Phân quyền RBAC, Chống CSRF | TV1 |
| **Sau Buổi 8** | Bản chạy thử nghiệm (Smoke test, Fetch API, Nộp bài) | Cả nhóm (TV5 tổng hợp) |

---

## Quy tắc làm việc nhóm trên Git
1. **Không push trực tiếp vào `main`**: Mỗi tính năng làm trên 1 nhánh `feature/<ten-chuc-nang>`.
2. **Pull Request (PR)**: Tạo PR để người khác review trước khi merge vào `main`.
3. **Commit rõ ràng**: Mỗi commit mang một thông điệp cụ thể (`feat:`, `fix:`, `docs:`, `test:`).