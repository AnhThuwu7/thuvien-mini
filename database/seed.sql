-- =====================================================================
-- HỆ THỐNG QUẢN LÝ THƯ VIỆN MINI - seed.sql (dữ liệu mẫu)
-- Chạy SAU schema.sql, trên CSDL thuvien_mini còn trống.
-- Muốn làm lại từ đầu: chạy lại schema.sql rồi seed.sql.
--
-- TÀI KHOẢN DEMO (mật khẩu chung: Demo@123 - đã băm bằng bcrypt, tương thích password_verify)
--   admin@example.com     Quản trị viên
--   thuthu1@example.com   Thủ thư   |  thuthu2@example.com Thủ thư
--   docgia01..08@example.com  Độc giả (docgia04 đang bị khóa)
--
-- Ngày giờ trong phiếu mượn/yêu cầu được tính TƯƠNG ĐỐI so với lúc import (NOW(), CURDATE()),
-- nên phiếu 'quá hạn', 'sắp đến hạn' luôn đúng bất kể ngày chạy.
-- ISBN sách tiếng Anh là mã thật; ISBN sách tiếng Việt là MÃ MẪU (checksum hợp lệ), không phải mã thật.
-- =====================================================================

USE thuvien_mini;
SET NAMES utf8mb4;

-- 1. Người dùng
INSERT INTO nguoi_dung (id, ma_doc_gia, ho_ten, email, mat_khau_bam, vai_tro, so_dien_thoai, bi_khoa, ly_do_khoa) VALUES
  (1, NULL, 'Quản trị hệ thống', 'admin@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'quan_tri', '0900000001', 0, NULL),
  (2, NULL, 'Nguyễn Thị Lan', 'thuthu1@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'thu_thu', '0900000002', 0, NULL),
  (3, NULL, 'Trần Văn Hùng', 'thuthu2@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'thu_thu', '0900000003', 0, NULL),
  (4, 'DG001', 'Lê Minh Anh', 'docgia01@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000004', 0, NULL),
  (5, 'DG002', 'Phạm Quốc Bảo', 'docgia02@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000005', 0, NULL),
  (6, 'DG003', 'Hoàng Thị Cẩm', 'docgia03@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000006', 0, NULL),
  (7, 'DG004', 'Đỗ Thanh Dũng', 'docgia04@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000007', 1, 'Làm mất sách, chưa bồi thường'),
  (8, 'DG005', 'Vũ Ngọc Hà', 'docgia05@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000008', 0, NULL),
  (9, 'DG006', 'Bùi Tuấn Kiệt', 'docgia06@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000009', 0, NULL),
  (10, 'DG007', 'Ngô Phương Linh', 'docgia07@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000010', 0, NULL),
  (11, 'DG008', 'Đặng Hải Nam', 'docgia08@example.com', '$2y$12$uZoqm92tEl69NAr0X6BP9OQiy/KlkmXxyuNmR6nYdRHu87S2l4O5C', 'doc_gia', '0900000011', 0, NULL);

-- 2. Danh mục
INSERT INTO danh_muc (id, ten, mo_ta) VALUES
  (1, 'Văn học Việt Nam', 'Tiểu thuyết, truyện ngắn, thơ của các tác giả Việt Nam'),
  (2, 'Văn học nước ngoài', 'Tác phẩm văn học thế giới, bản dịch và bản gốc'),
  (3, 'Thiếu nhi', 'Truyện và sách dành cho thiếu nhi, thiếu niên'),
  (4, 'Kỹ năng sống', 'Sách phát triển bản thân, tư duy và thói quen'),
  (5, 'Khoa học - Lịch sử', 'Sách khoa học và lịch sử phổ thông'),
  (6, 'Công nghệ thông tin', 'Lập trình, thuật toán, kỹ thuật phần mềm'),
  (7, 'Giáo trình đại học', 'Giáo trình các môn đại cương và chuyên ngành');

-- 3. Tác giả
INSERT INTO tac_gia (id, ten, tieu_su) VALUES
  (1, 'Tô Hoài', 'Nhà văn Việt Nam (1920–2014), tác giả “Dế Mèn phiêu lưu ký”.'),
  (2, 'Nguyễn Nhật Ánh', 'Nhà văn Việt Nam (sinh năm 1955), nổi tiếng với các tác phẩm viết cho tuổi thiếu niên và tuổi học trò.'),
  (3, 'Vũ Trọng Phụng', 'Nhà văn, nhà báo Việt Nam (1912–1939), tác giả “Số đỏ”.'),
  (4, 'Ngô Tất Tố', 'Nhà văn, nhà báo Việt Nam (1893–1954), tác giả “Tắt đèn”.'),
  (5, 'Nam Cao', 'Nhà văn hiện thực Việt Nam (1915–1951), tác giả “Chí Phèo”.'),
  (6, 'Nguyễn Du', 'Đại thi hào dân tộc Việt Nam (1765–1820), tác giả “Truyện Kiều”.'),
  (7, 'Nguyễn Ngọc Tư', 'Nhà văn Việt Nam (sinh năm 1976), quê Cà Mau, viết nhiều về miền sông nước Nam Bộ.'),
  (8, 'Bảo Ninh', 'Nhà văn Việt Nam (sinh năm 1952), tác giả “Nỗi buồn chiến tranh”.'),
  (9, 'Phùng Quán', 'Nhà văn, nhà thơ Việt Nam (1932–1995), tác giả “Tuổi thơ dữ dội”.'),
  (10, 'Đoàn Giỏi', 'Nhà văn Việt Nam (1925–1989), tác giả “Đất rừng phương Nam”.'),
  (11, 'Paulo Coelho', 'Nhà văn người Brazil (sinh năm 1947), tác giả “Nhà giả kim”.'),
  (12, 'J. K. Rowling', 'Nhà văn người Anh (sinh năm 1965), tác giả loạt truyện Harry Potter.'),
  (13, 'Dale Carnegie', 'Nhà văn, nhà diễn thuyết người Mỹ (1888–1955), tác giả “Đắc nhân tâm”.'),
  (14, 'James Clear', 'Nhà văn người Mỹ, viết về thói quen và phát triển bản thân.'),
  (15, 'Tony Buổi Sáng', 'Tác giả viết về nghề nghiệp và kỹ năng sống dành cho người trẻ.'),
  (16, 'Yuval Noah Harari', 'Nhà sử học người Israel (sinh năm 1976), tác giả “Sapiens”.'),
  (17, 'Stephen Hawking', 'Nhà vật lý lý thuyết người Anh (1942–2018), tác giả “Lược sử thời gian”.'),
  (18, 'Robert C. Martin', 'Kỹ sư phần mềm người Mỹ, thường được biết đến với biệt danh “Uncle Bob”.'),
  (19, 'Erich Gamma', 'Đồng tác giả cuốn “Design Patterns” (nhóm “Gang of Four”).'),
  (20, 'Richard Helm', 'Đồng tác giả cuốn “Design Patterns” (nhóm “Gang of Four”).'),
  (21, 'Ralph Johnson', 'Đồng tác giả cuốn “Design Patterns” (nhóm “Gang of Four”).'),
  (22, 'John Vlissides', 'Đồng tác giả cuốn “Design Patterns” (nhóm “Gang of Four”).'),
  (23, 'Thomas H. Cormen', 'Đồng tác giả cuốn “Introduction to Algorithms”.'),
  (24, 'Charles E. Leiserson', 'Đồng tác giả cuốn “Introduction to Algorithms”.'),
  (25, 'Ronald L. Rivest', 'Đồng tác giả cuốn “Introduction to Algorithms”.'),
  (26, 'Clifford Stein', 'Đồng tác giả cuốn “Introduction to Algorithms”.'),
  (27, 'Đỗ Xuân Lôi', 'Tác giả giáo trình “Cấu trúc dữ liệu và giải thuật”.'),
  (28, 'Nguyễn Đình Trí', 'Chủ biên bộ giáo trình “Toán học cao cấp”.'),
  (29, 'Bộ Giáo dục và Đào tạo', 'Cơ quan tổ chức biên soạn các giáo trình lý luận chính trị dùng chung cho sinh viên đại học.');

-- 4. Đầu sách
INSERT INTO sach (id, danh_muc_id, isbn, ten_sach, nha_xuat_ban, nam_xuat_ban, ngon_ngu, mo_ta) VALUES
  (1, 3, '9786041000070', 'Dế Mèn phiêu lưu ký', 'NXB Kim Đồng', 2019, 'vi', 'Tác phẩm kinh điển của Tô Hoài kể về những cuộc phiêu lưu của chú Dế Mèn.'),
  (2, 3, '9786041000148', 'Cho tôi xin một vé đi tuổi thơ', 'NXB Trẻ', 2018, 'vi', 'Truyện của Nguyễn Nhật Ánh về thế giới tuổi thơ trong trẻo.'),
  (3, 3, '9786041000216', 'Tuổi thơ dữ dội', 'NXB Kim Đồng', 2018, 'vi', 'Tiểu thuyết của Phùng Quán về những thiếu niên làm nhiệm vụ trinh sát trong kháng chiến chống Pháp.'),
  (4, 3, '9786041000285', 'Đất rừng phương Nam', 'NXB Kim Đồng', 2019, 'vi', 'Tiểu thuyết của Đoàn Giỏi về vùng đất Nam Bộ qua câu chuyện của cậu bé An.'),
  (5, 1, '9786041000353', 'Số đỏ', 'NXB Văn học', 2019, 'vi', 'Tiểu thuyết trào phúng tiêu biểu của văn học hiện thực Việt Nam.'),
  (6, 1, '9786041000421', 'Tắt đèn', 'NXB Văn học', 2018, 'vi', 'Tiểu thuyết hiện thực phản ánh đời sống người nông dân dưới thời Pháp thuộc.'),
  (7, 1, '9786041000490', 'Chí Phèo', 'NXB Văn học', 2018, 'vi', 'Truyện ngắn tiêu biểu của Nam Cao về bi kịch người nông dân bị tha hóa.'),
  (8, 1, '9786041000568', 'Truyện Kiều', 'NXB Văn học', 2015, 'vi', 'Truyện thơ chữ Nôm kinh điển của Nguyễn Du.'),
  (9, 1, '9786041000636', 'Mắt biếc', 'NXB Trẻ', 2019, 'vi', 'Tiểu thuyết của Nguyễn Nhật Ánh, câu chuyện tình đơn phương của Ngạn dành cho Hà Lan.'),
  (10, 1, '9786041000704', 'Tôi thấy hoa vàng trên cỏ xanh', 'NXB Trẻ', 2018, 'vi', 'Câu chuyện tuổi thơ ở miền quê của hai anh em Thiều và Tường.'),
  (11, 1, '9786041000773', 'Cánh đồng bất tận', 'NXB Trẻ', 2012, 'vi', 'Truyện nổi tiếng của Nguyễn Ngọc Tư về cuộc sống miền sông nước Nam Bộ.'),
  (12, 1, '9786041000841', 'Nỗi buồn chiến tranh', 'NXB Hội Nhà văn', 2016, 'vi', 'Tiểu thuyết của Bảo Ninh về ký ức chiến tranh của người lính.'),
  (13, 2, '9786041000919', 'Nhà giả kim', 'NXB Văn học', 2013, 'vi', 'Tiểu thuyết của Paulo Coelho về hành trình của cậu bé chăn cừu Santiago; bản dịch của Lê Chu Cầu.'),
  (14, 2, '9780747532743', 'Harry Potter and the Philosopher''s Stone', 'Bloomsbury', 1997, 'en', 'Tập đầu tiên trong loạt truyện Harry Potter, bản tiếng Anh.'),
  (15, 4, '9786041000988', 'Đắc nhân tâm', 'NXB Tổng hợp TP.HCM', 2016, 'vi', 'Cuốn sách kinh điển về nghệ thuật giao tiếp và ứng xử; bản dịch của Nguyễn Hiến Lê.'),
  (16, 4, '9780735211292', 'Atomic Habits', 'Avery', 2018, 'en', 'Sách về cách xây dựng thói quen tốt và loại bỏ thói quen xấu, bản tiếng Anh.'),
  (17, 4, '9786041001053', 'Cà phê cùng Tony', 'NXB Trẻ', 2016, 'vi', 'Tập hợp các bài viết chia sẻ về nghề nghiệp và cuộc sống dành cho người trẻ.'),
  (18, 5, '9786041001121', 'Sapiens: Lược sử loài người', 'NXB Tri thức', 2017, 'vi', 'Lược sử loài người từ thời nguyên thủy đến hiện đại.'),
  (19, 5, '9786041001190', 'Lược sử thời gian', 'NXB Trẻ', 2018, 'vi', 'Sách khoa học phổ thông về vũ trụ, hố đen và bản chất của thời gian.'),
  (20, 6, '9780132350884', 'Clean Code', 'Prentice Hall', 2008, 'en', 'A Handbook of Agile Software Craftsmanship - sách về viết mã sạch, bản tiếng Anh.'),
  (21, 6, '9780201633610', 'Design Patterns: Elements of Reusable Object-Oriented Software', 'Addison-Wesley', 1994, 'en', 'Danh mục 23 mẫu thiết kế hướng đối tượng, bản tiếng Anh.'),
  (22, 6, '9780262033848', 'Introduction to Algorithms', 'MIT Press', 2009, 'en', 'Giáo trình thuật toán kinh điển (CLRS), ấn bản lần 3, bản tiếng Anh.'),
  (23, 6, '9786041001268', 'Cấu trúc dữ liệu và giải thuật', 'NXB Khoa học và Kỹ thuật', 2004, 'vi', 'Giáo trình cấu trúc dữ liệu và giải thuật.'),
  (24, 7, '9786041001336', 'Toán học cao cấp - Tập 1: Đại số và Hình học giải tích', 'NXB Giáo dục Việt Nam', 2012, 'vi', 'Giáo trình toán đại cương cho sinh viên các ngành kỹ thuật và kinh tế.'),
  (25, 7, '9786041001404', 'Giáo trình Triết học Mác – Lênin', 'NXB Chính trị quốc gia Sự thật', 2021, 'vi', 'Giáo trình môn lý luận chính trị dành cho sinh viên đại học.'),
  (26, 7, '9786041001473', 'Giáo trình Tư tưởng Hồ Chí Minh', 'NXB Chính trị quốc gia Sự thật', 2021, 'vi', 'Giáo trình môn lý luận chính trị dành cho sinh viên đại học.');

-- 5. Sách - Tác giả (thu_tu = 1 là tác giả chính)
INSERT INTO sach_tac_gia (sach_id, tac_gia_id, thu_tu) VALUES
  (1, 1, 1),
  (2, 2, 1),
  (3, 9, 1),
  (4, 10, 1),
  (5, 3, 1),
  (6, 4, 1),
  (7, 5, 1),
  (8, 6, 1),
  (9, 2, 1),
  (10, 2, 1),
  (11, 7, 1),
  (12, 8, 1),
  (13, 11, 1),
  (14, 12, 1),
  (15, 13, 1),
  (16, 14, 1),
  (17, 15, 1),
  (18, 16, 1),
  (19, 17, 1),
  (20, 18, 1),
  (21, 19, 1),
  (21, 20, 2),
  (21, 21, 3),
  (21, 22, 4),
  (22, 23, 1),
  (22, 24, 2),
  (22, 25, 3),
  (22, 26, 4),
  (23, 27, 1),
  (24, 28, 1),
  (25, 29, 1),
  (26, 29, 1);

-- 6. Bản sao (trạng thái khớp với phiếu mượn và yêu cầu bên dưới)
INSERT INTO ban_sao (id, sach_id, ma_ban_sao, trang_thai, vi_tri_ke, ngay_nhap, ghi_chu) VALUES
  (1, 1, 'BK0001-01', 'dang_muon', 'Kệ B1', '2025-01-20', NULL),
  (2, 1, 'BK0001-02', 'con_trong', 'Kệ B1', '2025-03-12', NULL),
  (3, 1, 'BK0001-03', 'con_trong', 'Kệ B1', '2025-06-05', NULL),
  (4, 2, 'BK0002-01', 'con_trong', 'Kệ B1', '2025-03-12', NULL),
  (5, 2, 'BK0002-02', 'con_trong', 'Kệ B1', '2025-06-05', NULL),
  (6, 3, 'BK0003-01', 'con_trong', 'Kệ B1', '2025-06-05', NULL),
  (7, 3, 'BK0003-02', 'con_trong', 'Kệ B1', '2024-08-15', NULL),
  (8, 4, 'BK0004-01', 'con_trong', 'Kệ B1', '2024-08-15', NULL),
  (9, 4, 'BK0004-02', 'con_trong', 'Kệ B1', '2024-10-02', NULL),
  (10, 5, 'BK0005-01', 'dang_muon', 'Kệ A1', '2024-10-02', NULL),
  (11, 5, 'BK0005-02', 'con_trong', 'Kệ A1', '2025-01-20', NULL),
  (12, 6, 'BK0006-01', 'con_trong', 'Kệ A1', '2025-01-20', NULL),
  (13, 6, 'BK0006-02', 'hong', 'Kệ A1', '2025-03-12', 'Rách bìa, chờ sửa chữa'),
  (14, 7, 'BK0007-01', 'con_trong', 'Kệ A1', '2025-03-12', NULL),
  (15, 7, 'BK0007-02', 'con_trong', 'Kệ A1', '2025-06-05', NULL),
  (16, 8, 'BK0008-01', 'con_trong', 'Kệ A1', '2025-06-05', NULL),
  (17, 8, 'BK0008-02', 'con_trong', 'Kệ A1', '2024-08-15', NULL),
  (18, 8, 'BK0008-03', 'thanh_ly', 'Kệ A1', '2024-10-02', 'Quá cũ, đã thanh lý'),
  (19, 9, 'BK0009-01', 'dang_muon', 'Kệ A1', '2024-08-15', NULL),
  (20, 9, 'BK0009-02', 'con_trong', 'Kệ A1', '2024-10-02', NULL),
  (21, 9, 'BK0009-03', 'con_trong', 'Kệ A1', '2025-01-20', NULL),
  (22, 10, 'BK0010-01', 'con_trong', 'Kệ A1', '2024-10-02', NULL),
  (23, 10, 'BK0010-02', 'con_trong', 'Kệ A1', '2025-01-20', NULL),
  (24, 10, 'BK0010-03', 'con_trong', 'Kệ A1', '2025-03-12', NULL),
  (25, 11, 'BK0011-01', 'con_trong', 'Kệ A1', '2025-01-20', NULL),
  (26, 11, 'BK0011-02', 'con_trong', 'Kệ A1', '2025-03-12', NULL),
  (27, 12, 'BK0012-01', 'dang_giu_cho', 'Kệ A1', '2025-03-12', NULL),
  (28, 12, 'BK0012-02', 'con_trong', 'Kệ A1', '2025-06-05', NULL),
  (29, 13, 'BK0013-01', 'dang_muon', 'Kệ A2', '2025-06-05', NULL),
  (30, 13, 'BK0013-02', 'con_trong', 'Kệ A2', '2024-08-15', NULL),
  (31, 13, 'BK0013-03', 'con_trong', 'Kệ A2', '2024-10-02', NULL),
  (32, 14, 'BK0014-01', 'con_trong', 'Kệ A2', '2024-08-15', NULL),
  (33, 14, 'BK0014-02', 'con_trong', 'Kệ A2', '2024-10-02', NULL),
  (34, 15, 'BK0015-01', 'dang_muon', 'Kệ C1', '2024-10-02', NULL),
  (35, 15, 'BK0015-02', 'con_trong', 'Kệ C1', '2025-01-20', NULL),
  (36, 15, 'BK0015-03', 'con_trong', 'Kệ C1', '2025-03-12', NULL),
  (37, 16, 'BK0016-01', 'hong', 'Kệ C1', '2025-01-20', 'Rách bìa khi trả (xem phiếu mượn)'),
  (38, 16, 'BK0016-02', 'con_trong', 'Kệ C1', '2025-03-12', NULL),
  (39, 17, 'BK0017-01', 'con_trong', 'Kệ C1', '2025-03-12', NULL),
  (40, 17, 'BK0017-02', 'con_trong', 'Kệ C1', '2025-06-05', NULL),
  (41, 18, 'BK0018-01', 'dang_muon', 'Kệ D1', '2025-06-05', NULL),
  (42, 18, 'BK0018-02', 'con_trong', 'Kệ D1', '2024-08-15', NULL),
  (43, 19, 'BK0019-01', 'con_trong', 'Kệ D1', '2024-08-15', NULL),
  (44, 19, 'BK0019-02', 'con_trong', 'Kệ D1', '2024-10-02', NULL),
  (45, 20, 'BK0020-01', 'dang_muon', 'Kệ E1', '2024-10-02', NULL),
  (46, 20, 'BK0020-02', 'con_trong', 'Kệ E1', '2025-01-20', NULL),
  (47, 21, 'BK0021-01', 'dang_muon', 'Kệ E1', '2025-01-20', NULL),
  (48, 22, 'BK0022-01', 'con_trong', 'Kệ E1', '2025-03-12', NULL),
  (49, 22, 'BK0022-02', 'con_trong', 'Kệ E1', '2025-06-05', NULL),
  (50, 23, 'BK0023-01', 'con_trong', 'Kệ E1', '2025-06-05', NULL),
  (51, 23, 'BK0023-02', 'con_trong', 'Kệ E1', '2024-08-15', NULL),
  (52, 23, 'BK0023-03', 'con_trong', 'Kệ E1', '2024-10-02', NULL),
  (53, 24, 'BK0024-01', 'dang_muon', 'Kệ F1', '2024-08-15', NULL),
  (54, 24, 'BK0024-02', 'con_trong', 'Kệ F1', '2024-10-02', NULL),
  (55, 24, 'BK0024-03', 'con_trong', 'Kệ F1', '2025-01-20', NULL),
  (56, 25, 'BK0025-01', 'con_trong', 'Kệ F1', '2024-10-02', NULL),
  (57, 25, 'BK0025-02', 'con_trong', 'Kệ F1', '2025-01-20', NULL),
  (58, 25, 'BK0025-03', 'mat', 'Kệ F1', '2025-03-12', 'Độc giả báo mất, chờ bồi thường'),
  (59, 26, 'BK0026-01', 'con_trong', 'Kệ F1', '2025-01-20', NULL),
  (60, 26, 'BK0026-02', 'con_trong', 'Kệ F1', '2025-03-12', NULL);

-- 7. Yêu cầu mượn (đủ 6 trạng thái: cho_duyet, da_duyet, tu_choi, da_huy, het_han, da_nhan)
INSERT INTO yeu_cau_muon (id, nguoi_dung_id, sach_id, ban_sao_id, trang_thai, ghi_chu, yeu_cau_luc, nguoi_xu_ly_id, xu_ly_luc, han_den_lay, ly_do_tu_choi) VALUES
  (1, 4, 1, 1, 'da_nhan', NULL, NOW() - INTERVAL 13 DAY, 2, NOW() - INTERVAL 13 DAY, NOW() - INTERVAL 11 DAY, NULL),
  (2, 7, 17, NULL, 'tu_choi', 'Muốn đọc ở nhà', NOW() - INTERVAL 2 DAY, 3, NOW() - INTERVAL 2 DAY, NULL, 'Tài khoản đang bị khóa'),
  (3, 8, 19, NULL, 'da_huy', 'Độc giả tự hủy yêu cầu', NOW() - INTERVAL 7 DAY, NULL, NULL, NULL, NULL),
  (4, 10, 15, 35, 'het_han', NULL, NOW() - INTERVAL 9 DAY, 2, NOW() - INTERVAL 9 DAY, NOW() - INTERVAL 7 DAY, NULL),
  (5, 11, 12, 27, 'da_duyet', NULL, NOW() - INTERVAL 1 DAY, 3, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 1 DAY, NULL),
  (6, 10, 21, NULL, 'cho_duyet', 'Đặt trước vì bản sao duy nhất đang được mượn', NOW(), NULL, NULL, NULL, NULL);

-- 8. Phiếu mượn (9 phiếu đang mượn, 12 phiếu đã trả)
INSERT INTO phieu_muon (id, ban_sao_id, nguoi_dung_id, yeu_cau_id, nguoi_cho_muon_id, muon_luc, han_tra, tra_luc, nguoi_nhan_tra_id, tinh_trang_tra, so_lan_gia_han, tien_phat, ghi_chu) VALUES
  (1, 16, 7, NULL, 3, NOW() - INTERVAL 60 DAY, CURDATE() - INTERVAL 46 DAY, NOW() - INTERVAL 40 DAY, 2, 'tot', 0, 12000, NULL),
  (2, 22, 6, NULL, 2, NOW() - INTERVAL 50 DAY, CURDATE() - INTERVAL 36 DAY, NOW() - INTERVAL 30 DAY, 3, 'tot', 0, 12000, NULL),
  (3, 20, 4, NULL, 2, NOW() - INTERVAL 45 DAY, CURDATE() - INTERVAL 31 DAY, NOW() - INTERVAL 35 DAY, 2, 'tot', 0, 0, NULL),
  (4, 4, 5, NULL, 3, NOW() - INTERVAL 40 DAY, CURDATE() - INTERVAL 26 DAY, NOW() - INTERVAL 30 DAY, 3, 'tot', 0, 0, NULL),
  (5, 58, 7, NULL, 2, NOW() - INTERVAL 35 DAY, CURDATE() - INTERVAL 21 DAY, NOW() - INTERVAL 15 DAY, 3, 'mat', 0, 12000, 'Báo mất sách, chờ bồi thường theo quy định'),
  (6, 37, 4, NULL, 2, NOW() - INTERVAL 33 DAY, CURDATE() - INTERVAL 19 DAY, NOW() - INTERVAL 22 DAY, 2, 'hong', 0, 0, 'Rách bìa khi trả'),
  (7, 54, 8, NULL, 3, NOW() - INTERVAL 30 DAY, CURDATE() - INTERVAL 16 DAY, NOW() - INTERVAL 20 DAY, 2, 'tot', 0, 0, NULL),
  (8, 32, 9, NULL, 2, NOW() - INTERVAL 28 DAY, CURDATE() - INTERVAL 14 DAY, NOW() - INTERVAL 10 DAY, 3, 'tot', 0, 8000, NULL),
  (9, 50, 11, NULL, 3, NOW() - INTERVAL 25 DAY, CURDATE() - INTERVAL 11 DAY, NOW() - INTERVAL 18 DAY, 3, 'tot', 0, 0, NULL),
  (10, 25, 5, NULL, 2, NOW() - INTERVAL 22 DAY, CURDATE() - INTERVAL 8 DAY, NOW() - INTERVAL 12 DAY, 2, 'tot', 0, 0, NULL),
  (11, 10, 6, NULL, 2, NOW() - INTERVAL 21 DAY, CURDATE() - INTERVAL 7 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (12, 6, 8, NULL, 3, NOW() - INTERVAL 18 DAY, CURDATE() - INTERVAL 4 DAY, NOW() - INTERVAL 9 DAY, 3, 'tot', 0, 0, NULL),
  (13, 43, 6, NULL, 2, NOW() - INTERVAL 15 DAY, CURDATE() - INTERVAL 1 DAY, NOW() - INTERVAL 5 DAY, 2, 'tot', 0, 0, NULL),
  (14, 1, 4, 1, 2, NOW() - INTERVAL 12 DAY, CURDATE() + INTERVAL 2 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (15, 45, 5, NULL, 3, NOW() - INTERVAL 11 DAY, CURDATE() + INTERVAL 3 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (16, 41, 8, NULL, 2, NOW() - INTERVAL 11 DAY, CURDATE() + INTERVAL 10 DAY, NULL, NULL, NULL, 1, 0, 'Đã gia hạn 1 lần'),
  (17, 53, 5, NULL, 2, NOW() - INTERVAL 9 DAY, CURDATE() + INTERVAL 5 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (18, 34, 5, NULL, 3, NOW() - INTERVAL 8 DAY, CURDATE() + INTERVAL 6 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (19, 47, 9, NULL, 3, NOW() - INTERVAL 6 DAY, CURDATE() + INTERVAL 8 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (20, 29, 5, NULL, 2, NOW() - INTERVAL 5 DAY, CURDATE() + INTERVAL 9 DAY, NULL, NULL, NULL, 0, 0, NULL),
  (21, 19, 5, NULL, 3, NOW() - INTERVAL 3 DAY, CURDATE() + INTERVAL 11 DAY, NULL, NULL, NULL, 0, 0, NULL);

-- 9. Nhật ký mượn trả
INSERT INTO nhat_ky_muon (phieu_muon_id, nguoi_thuc_hien_id, hanh_dong, chi_tiet, tao_luc) VALUES
  (1, 3, 'cho_muon', 'Cho mượn bản sao BK0008-01', NOW() - INTERVAL 60 DAY),
  (2, 2, 'cho_muon', 'Cho mượn bản sao BK0010-01', NOW() - INTERVAL 50 DAY),
  (3, 2, 'cho_muon', 'Cho mượn bản sao BK0009-02', NOW() - INTERVAL 45 DAY),
  (1, 2, 'tra_sach', 'Nhận trả sách, tình trạng tốt; phạt trễ 6 ngày = 12000 đ', NOW() - INTERVAL 40 DAY),
  (4, 3, 'cho_muon', 'Cho mượn bản sao BK0002-01', NOW() - INTERVAL 40 DAY),
  (3, 2, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 35 DAY),
  (5, 2, 'cho_muon', 'Cho mượn bản sao BK0025-03', NOW() - INTERVAL 35 DAY),
  (6, 2, 'cho_muon', 'Cho mượn bản sao BK0016-01', NOW() - INTERVAL 33 DAY),
  (2, 3, 'tra_sach', 'Nhận trả sách, tình trạng tốt; phạt trễ 6 ngày = 12000 đ', NOW() - INTERVAL 30 DAY),
  (4, 3, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 30 DAY),
  (7, 3, 'cho_muon', 'Cho mượn bản sao BK0024-02', NOW() - INTERVAL 30 DAY),
  (8, 2, 'cho_muon', 'Cho mượn bản sao BK0014-01', NOW() - INTERVAL 28 DAY),
  (9, 3, 'cho_muon', 'Cho mượn bản sao BK0023-01', NOW() - INTERVAL 25 DAY),
  (6, 2, 'tra_sach', 'Nhận trả sách, sách bị hỏng (rách bìa)', NOW() - INTERVAL 22 DAY),
  (10, 2, 'cho_muon', 'Cho mượn bản sao BK0011-01', NOW() - INTERVAL 22 DAY),
  (11, 2, 'cho_muon', 'Cho mượn bản sao BK0005-01', NOW() - INTERVAL 21 DAY),
  (7, 2, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 20 DAY),
  (9, 3, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 18 DAY),
  (12, 3, 'cho_muon', 'Cho mượn bản sao BK0003-01', NOW() - INTERVAL 18 DAY),
  (5, 3, 'bao_mat', 'Độc giả báo mất sách', NOW() - INTERVAL 15 DAY),
  (13, 2, 'cho_muon', 'Cho mượn bản sao BK0019-01', NOW() - INTERVAL 15 DAY),
  (10, 2, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 12 DAY),
  (14, 2, 'cho_muon', 'Cho mượn bản sao BK0001-01', NOW() - INTERVAL 12 DAY),
  (15, 3, 'cho_muon', 'Cho mượn bản sao BK0020-01', NOW() - INTERVAL 11 DAY),
  (16, 2, 'cho_muon', 'Cho mượn bản sao BK0018-01', NOW() - INTERVAL 11 DAY),
  (8, 3, 'tra_sach', 'Nhận trả sách, tình trạng tốt; phạt trễ 4 ngày = 8000 đ', NOW() - INTERVAL 10 DAY),
  (12, 3, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 9 DAY),
  (17, 2, 'cho_muon', 'Cho mượn bản sao BK0024-01', NOW() - INTERVAL 9 DAY),
  (16, 2, 'gia_han', 'Gia hạn lần 1, cộng thêm 7 ngày', NOW() - INTERVAL 8 DAY),
  (18, 3, 'cho_muon', 'Cho mượn bản sao BK0015-01', NOW() - INTERVAL 8 DAY),
  (19, 3, 'cho_muon', 'Cho mượn bản sao BK0021-01', NOW() - INTERVAL 6 DAY),
  (13, 2, 'tra_sach', 'Nhận trả sách, tình trạng tốt', NOW() - INTERVAL 5 DAY),
  (20, 2, 'cho_muon', 'Cho mượn bản sao BK0013-01', NOW() - INTERVAL 5 DAY),
  (21, 3, 'cho_muon', 'Cho mượn bản sao BK0009-01', NOW() - INTERVAL 3 DAY);

-- =====================================================================
-- TRUY VẤN KIỂM TRA NHANH (chạy riêng sau khi import, không bắt buộc)
-- =====================================================================
-- a) Số dòng mỗi bảng: nguoi_dung 11, danh_muc 7, tac_gia 29, sach 26, sach_tac_gia 32, ban_sao 60,
--    yeu_cau_muon 6, phieu_muon 21, nhat_ky_muon 34, cau_hinh 7
-- SELECT 'ban_sao' AS bang, COUNT(*) AS so_dong FROM ban_sao UNION ALL
-- SELECT 'phieu_muon', COUNT(*) FROM phieu_muon UNION ALL SELECT 'sach', COUNT(*) FROM sach;
--
-- b) Bản sao lệch trạng thái so với phiếu mượn (PHẢI trả về 0 dòng):
-- SELECT bs.id, bs.ma_ban_sao FROM ban_sao bs
-- LEFT JOIN phieu_muon pm ON pm.ban_sao_id = bs.id AND pm.tra_luc IS NULL
-- WHERE (bs.trang_thai = 'dang_muon') <> (pm.id IS NOT NULL);
--
-- c) Phiếu quá hạn (mong đợi 1 dòng: Hoàng Thị Cẩm, trễ 7 ngày):
-- SELECT pm.id, nd.ho_ten, s.ten_sach, bs.ma_ban_sao, pm.han_tra, DATEDIFF(CURDATE(), pm.han_tra) AS so_ngay_tre
-- FROM phieu_muon pm JOIN nguoi_dung nd ON nd.id = pm.nguoi_dung_id
-- JOIN ban_sao bs ON bs.id = pm.ban_sao_id JOIN sach s ON s.id = bs.sach_id
-- WHERE pm.tra_luc IS NULL AND pm.han_tra < CURDATE();
--
-- d) Phiếu sắp đến hạn (trong 3 ngày tới, mong đợi 2 dòng):
-- SELECT pm.id, nd.ho_ten, pm.han_tra FROM phieu_muon pm JOIN nguoi_dung nd ON nd.id = pm.nguoi_dung_id
-- WHERE pm.tra_luc IS NULL AND pm.han_tra BETWEEN CURDATE() AND CURDATE() + INTERVAL 3 DAY;
