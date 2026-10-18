-- =====================================================================
-- HỆ THỐNG QUẢN LÝ THƯ VIỆN MINI
-- schema.sql - PHIÊN BẢN CHỐT 1.2
--
-- Yêu cầu : MySQL >= 8.0.16 (hoặc MariaDB >= 10.2.1) | InnoDB | utf8mb4
--           (CHECK chỉ có hiệu lực từ MySQL 8.0.16 - chạy SELECT VERSION(); để kiểm tra)
-- Cách chạy: phpMyAdmin -> tab Import (hoặc tab SQL): chạy schema.sql trước, rồi seed.sql
--
-- CẢNH BÁO : file này XÓA và TẠO LẠI toàn bộ CSDL thuvien_mini (dòng DROP DATABASE).
--            Chỉ dùng khi phát triển. Trước khi nộp bài, hãy comment dòng DROP DATABASE.
--
-- QUY ƯỚC QUAN TRỌNG CHO CẢ NHÓM
--  1. Mọi thao tác MƯỢN / TRẢ / GIỮ CHỖ phải nằm trong MỘT transaction, vì
--     ban_sao.trang_thai và phieu_muon.tra_luc cùng phản ánh một sự thật.
--  2. KHÔNG thêm ON UPDATE CASCADE / ON DELETE SET NULL vào các khóa ngoại
--     ban_sao_id, nguoi_dung_id, sach_id của bảng yeu_cau_muon và phieu_muon.
--     Đây là cột gốc của generated column; MySQL sẽ báo lỗi khi import.
--  3. Xóa dữ liệu bằng xóa mềm (xoa_luc) hoặc khóa tài khoản (bi_khoa),
--     không DELETE cứng các bản ghi đã có lịch sử mượn trả.
-- =====================================================================

DROP DATABASE IF EXISTS thuvien_mini;

CREATE DATABASE IF NOT EXISTS thuvien_mini
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE thuvien_mini;
SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. cau_hinh: cấu hình hệ thống (quản trị chỉnh được)
-- ---------------------------------------------------------------------
CREATE TABLE cau_hinh (
  khoa          VARCHAR(50)  NOT NULL PRIMARY KEY,
  gia_tri       VARCHAR(255) NOT NULL,
  mo_ta         VARCHAR(255) NULL,
  cap_nhat_luc  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. nguoi_dung: độc giả, thủ thư, quản trị viên
-- ---------------------------------------------------------------------
CREATE TABLE nguoi_dung (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_doc_gia      VARCHAR(20)  NULL,                       -- mã độc giả (NULL với nhân viên)
  ho_ten          VARCHAR(100) NOT NULL,
  email           VARCHAR(190) NOT NULL,
  mat_khau_bam    VARCHAR(255) NOT NULL,                   -- password_hash() của PHP
  vai_tro         ENUM('doc_gia','thu_thu','quan_tri') NOT NULL DEFAULT 'doc_gia',
  so_dien_thoai   VARCHAR(20)  NULL,
  bi_khoa         TINYINT(1)   NOT NULL DEFAULT 0,         -- 1 = bị khóa, không được mượn
  ly_do_khoa      VARCHAR(255) NULL,
  tao_luc         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cap_nhat_luc    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  xoa_luc         DATETIME     NULL,                       -- xóa mềm

  UNIQUE KEY uq_nguoi_dung_email (email),
  UNIQUE KEY uq_nguoi_dung_ma_doc_gia (ma_doc_gia),
  KEY idx_nguoi_dung_vai_tro (vai_tro),
  KEY idx_nguoi_dung_ten (ho_ten)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. danh_muc: danh mục / thể loại sách
-- ---------------------------------------------------------------------
CREATE TABLE danh_muc (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten       VARCHAR(100) NOT NULL,
  mo_ta     VARCHAR(255) NULL,
  tao_luc   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY uq_danh_muc_ten (ten)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. tac_gia: tác giả
-- ---------------------------------------------------------------------
CREATE TABLE tac_gia (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten       VARCHAR(150) NOT NULL,
  tieu_su   TEXT         NULL,
  tao_luc   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  KEY idx_tac_gia_ten (ten)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. sach: đầu sách (một danh mục duy nhất, bắt buộc -> không có sách mồ côi)
-- ---------------------------------------------------------------------
CREATE TABLE sach (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  danh_muc_id   INT UNSIGNED NOT NULL,
  isbn          VARCHAR(20)  NULL,
  ten_sach      VARCHAR(255) NOT NULL,
  nha_xuat_ban  VARCHAR(150) NULL,
  nam_xuat_ban  SMALLINT UNSIGNED NULL,
  ngon_ngu      CHAR(2)      NOT NULL DEFAULT 'vi',        -- vi = Tiếng Việt, en = Tiếng Anh
  mo_ta         TEXT         NULL,
  anh_bia       VARCHAR(255) NULL,                         -- đường dẫn ảnh bìa (mở rộng)
  tao_luc       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cap_nhat_luc  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  xoa_luc       DATETIME     NULL,                         -- xóa mềm

  UNIQUE KEY uq_sach_isbn (isbn),
  KEY idx_sach_ten_sach (ten_sach),
  KEY idx_sach_danh_muc (danh_muc_id),

  CONSTRAINT chk_sach_nam CHECK (nam_xuat_ban IS NULL OR nam_xuat_ban BETWEEN 1000 AND 2100),
  CONSTRAINT fk_sach_danh_muc FOREIGN KEY (danh_muc_id)
    REFERENCES danh_muc(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. sach_tac_gia: bảng nối n-n giữa sách và tác giả
-- ---------------------------------------------------------------------
CREATE TABLE sach_tac_gia (
  sach_id    INT UNSIGNED NOT NULL,
  tac_gia_id INT UNSIGNED NOT NULL,
  thu_tu     TINYINT UNSIGNED NOT NULL DEFAULT 1,          -- 1 = tác giả chính

  PRIMARY KEY (sach_id, tac_gia_id),
  KEY idx_sach_tac_gia_tac_gia (tac_gia_id),

  CONSTRAINT fk_sach_tac_gia_sach FOREIGN KEY (sach_id)
    REFERENCES sach(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_sach_tac_gia_tac_gia FOREIGN KEY (tac_gia_id)
    REFERENCES tac_gia(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7. ban_sao: từng quyển vật lý
--    con_trong    : sẵn sàng cho mượn
--    dang_giu_cho : đã duyệt yêu cầu, giữ cho độc giả đến lấy
--    dang_muon    : có phiếu mượn chưa trả
--    hong / mat   : không cho mượn
--    thanh_ly     : đã loại khỏi kho
-- ---------------------------------------------------------------------
CREATE TABLE ban_sao (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sach_id      INT UNSIGNED NOT NULL,
  ma_ban_sao   VARCHAR(30)  NOT NULL,                      -- ví dụ BK0001-02
  trang_thai   ENUM('con_trong','dang_giu_cho','dang_muon','hong','mat','thanh_ly')
               NOT NULL DEFAULT 'con_trong',
  vi_tri_ke    VARCHAR(50)  NULL,
  ngay_nhap    DATE         NULL,
  ghi_chu      VARCHAR(255) NULL,
  tao_luc      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cap_nhat_luc TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  xoa_luc      DATETIME     NULL,

  UNIQUE KEY uq_ban_sao_ma (ma_ban_sao),
  KEY idx_ban_sao_sach_trang_thai (sach_id, trang_thai),

  CONSTRAINT fk_ban_sao_sach FOREIGN KEY (sach_id)
    REFERENCES sach(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 8. yeu_cau_muon: độc giả đăng ký mượn / đặt trước một đầu sách
--    cho_duyet -> da_duyet (gán bản sao, giữ chỗ) -> da_nhan (đã thành phiếu mượn)
--    hoặc tu_choi / da_huy / het_han (quá hạn đến lấy)
-- ---------------------------------------------------------------------
CREATE TABLE yeu_cau_muon (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nguoi_dung_id   INT UNSIGNED NOT NULL,
  sach_id         INT UNSIGNED NOT NULL,
  ban_sao_id      INT UNSIGNED NULL,
  trang_thai      ENUM('cho_duyet','da_duyet','tu_choi','da_huy','het_han','da_nhan')
                  NOT NULL DEFAULT 'cho_duyet',
  ghi_chu         VARCHAR(255) NULL,
  yeu_cau_luc     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  nguoi_xu_ly_id  INT UNSIGNED NULL,
  xu_ly_luc       DATETIME     NULL,
  han_den_lay     DATETIME     NULL,
  ly_do_tu_choi   VARCHAR(255) NULL,

  -- Mỗi độc giả chỉ có 1 yêu cầu còn hiệu lực cho mỗi đầu sách
  khoa_hieu_luc   VARCHAR(40) GENERATED ALWAYS AS (
                    IF(trang_thai IN ('cho_duyet','da_duyet'),
                       CONCAT(nguoi_dung_id, '-', sach_id), NULL)
                  ) STORED,

  -- Một bản sao chỉ được giữ chỗ cho tối đa 1 yêu cầu đã duyệt
  ban_sao_giu_id  INT UNSIGNED GENERATED ALWAYS AS (
                    IF(trang_thai = 'da_duyet', ban_sao_id, NULL)
                  ) STORED,

  UNIQUE KEY uq_yeu_cau_hieu_luc (khoa_hieu_luc),
  UNIQUE KEY uq_yeu_cau_giu_ban_sao (ban_sao_giu_id),
  KEY idx_yeu_cau_trang_thai (trang_thai, yeu_cau_luc),
  KEY idx_yeu_cau_nguoi_dung (nguoi_dung_id),
  KEY idx_yeu_cau_sach (sach_id),
  KEY idx_yeu_cau_ban_sao (ban_sao_id),
  KEY idx_yeu_cau_nguoi_xu_ly (nguoi_xu_ly_id),

  CONSTRAINT fk_yeu_cau_nguoi_dung FOREIGN KEY (nguoi_dung_id)
    REFERENCES nguoi_dung(id) ON DELETE RESTRICT,
  CONSTRAINT fk_yeu_cau_sach FOREIGN KEY (sach_id)
    REFERENCES sach(id) ON DELETE RESTRICT,
  CONSTRAINT fk_yeu_cau_ban_sao FOREIGN KEY (ban_sao_id)
    REFERENCES ban_sao(id) ON DELETE RESTRICT,
  CONSTRAINT fk_yeu_cau_nguoi_xu_ly FOREIGN KEY (nguoi_xu_ly_id)
    REFERENCES nguoi_dung(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 9. phieu_muon: mỗi dòng = một lượt mượn MỘT bản sao
--    tra_luc IS NULL  -> chưa trả (đang mượn)
--    tinh_trang_tra   -> tot / hong / mat (chỉ có giá trị khi đã trả)
--    Khi báo mất sách: đóng phiếu (tra_luc có giá trị, tinh_trang_tra = 'mat')
--    và đặt ban_sao.trang_thai = 'mat'.
-- ---------------------------------------------------------------------
CREATE TABLE phieu_muon (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ban_sao_id          INT UNSIGNED NOT NULL,
  nguoi_dung_id       INT UNSIGNED NOT NULL,
  yeu_cau_id          INT UNSIGNED NULL,
  nguoi_cho_muon_id   INT UNSIGNED NOT NULL,
  muon_luc            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  han_tra             DATE         NOT NULL,
  tra_luc             DATETIME     NULL,
  nguoi_nhan_tra_id   INT UNSIGNED NULL,
  tinh_trang_tra      ENUM('tot','hong','mat') NULL,
  so_lan_gia_han      TINYINT UNSIGNED NOT NULL DEFAULT 0,
  tien_phat           DECIMAL(10,2) NOT NULL DEFAULT 0,
  ghi_chu             VARCHAR(255) NULL,

  -- Một bản sao chỉ có tối đa 1 lượt mượn chưa trả
  ban_sao_dang_muon_id INT UNSIGNED GENERATED ALWAYS AS (
                         IF(tra_luc IS NULL, ban_sao_id, NULL)
                       ) STORED,

  UNIQUE KEY uq_phieu_muon_dang_mo_ban_sao (ban_sao_dang_muon_id),
  UNIQUE KEY uq_phieu_muon_yeu_cau (yeu_cau_id),
  KEY idx_phieu_muon_nguoi_dung_da_tra (nguoi_dung_id, tra_luc),
  KEY idx_phieu_muon_chua_tra_han (tra_luc, han_tra),
  KEY idx_phieu_muon_ban_sao (ban_sao_id),
  KEY idx_phieu_muon_nguoi_cho_muon (nguoi_cho_muon_id),
  KEY idx_phieu_muon_nguoi_nhan_tra (nguoi_nhan_tra_id),

  CONSTRAINT chk_phieu_muon_han_tra CHECK (han_tra >= DATE(muon_luc)),
  CONSTRAINT chk_phieu_muon_tra CHECK (tra_luc IS NULL OR tra_luc >= muon_luc),
  CONSTRAINT chk_phieu_muon_tinh_trang CHECK (tra_luc IS NOT NULL OR tinh_trang_tra IS NULL),
  CONSTRAINT chk_phieu_muon_phat CHECK (tien_phat >= 0),

  CONSTRAINT fk_phieu_muon_ban_sao FOREIGN KEY (ban_sao_id)
    REFERENCES ban_sao(id) ON DELETE RESTRICT,
  CONSTRAINT fk_phieu_muon_nguoi_dung FOREIGN KEY (nguoi_dung_id)
    REFERENCES nguoi_dung(id) ON DELETE RESTRICT,
  CONSTRAINT fk_phieu_muon_yeu_cau FOREIGN KEY (yeu_cau_id)
    REFERENCES yeu_cau_muon(id) ON DELETE SET NULL,
  CONSTRAINT fk_phieu_muon_nguoi_cho_muon FOREIGN KEY (nguoi_cho_muon_id)
    REFERENCES nguoi_dung(id) ON DELETE RESTRICT,
  CONSTRAINT fk_phieu_muon_nguoi_nhan_tra FOREIGN KEY (nguoi_nhan_tra_id)
    REFERENCES nguoi_dung(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 10. nhat_ky_muon: nhật ký thao tác trên phiếu mượn (tùy chọn)
-- ---------------------------------------------------------------------
CREATE TABLE nhat_ky_muon (
  id                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  phieu_muon_id      INT UNSIGNED NOT NULL,
  nguoi_thuc_hien_id INT UNSIGNED NOT NULL,
  hanh_dong          ENUM('cho_muon','gia_han','tra_sach','bao_mat') NOT NULL,
  chi_tiet           VARCHAR(255) NULL,
  tao_luc            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  KEY idx_nhat_ky_muon_phieu_muon (phieu_muon_id),
  KEY idx_nhat_ky_muon_nguoi_thuc_hien (nguoi_thuc_hien_id),

  CONSTRAINT fk_nhat_ky_muon_phieu_muon FOREIGN KEY (phieu_muon_id)
    REFERENCES phieu_muon(id) ON DELETE RESTRICT,
  CONSTRAINT fk_nhat_ky_muon_nguoi_thuc_hien FOREIGN KEY (nguoi_thuc_hien_id)
    REFERENCES nguoi_dung(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Cấu hình mặc định (bắt buộc để ứng dụng chạy)
-- ---------------------------------------------------------------------
INSERT INTO cau_hinh (khoa, gia_tri, mo_ta) VALUES
  ('so_ngay_muon',            '14',   'Số ngày mượn mặc định'),
  ('so_sach_toi_da',          '5',    'Số sách tối đa một độc giả được mượn cùng lúc'),
  ('so_lan_gia_han_toi_da',   '2',    'Số lần gia hạn tối đa cho một phiếu mượn'),
  ('so_ngay_moi_lan_gia_han', '7',    'Số ngày cộng thêm mỗi lần gia hạn'),
  ('so_ngay_giu_sach',        '2',    'Số ngày giữ sách sau khi yêu cầu được duyệt'),
  ('so_ngay_sap_den_han',     '3',    'Phiếu còn dưới hoặc bằng số ngày này là "sắp đến hạn"'),
  ('tien_phat_moi_ngay',      '2000', 'Phí phạt mỗi ngày quá hạn (VNĐ)');
