<?php

class User extends Model
{
    // Dùng khi đăng nhập: bỏ qua tài khoản đã xóa mềm
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne(
            'SELECT id, ma_doc_gia, ho_ten, email, mat_khau_bam, vai_tro, bi_khoa, ly_do_khoa
               FROM nguoi_dung
              WHERE email = ? AND xoa_luc IS NULL
              LIMIT 1',
            [$email]
        );
    }

    // Hồ sơ (không trả về mật khẩu băm)
    public function findById(int $id): ?array
    {
        return $this->fetchOne(
            'SELECT id, ma_doc_gia, ho_ten, email, vai_tro, so_dien_thoai, bi_khoa, ly_do_khoa
               FROM nguoi_dung
              WHERE id = ? AND xoa_luc IS NULL
              LIMIT 1',
            [$id]
        );
    }

    // Lấy danh sách người dùng có phân trang & tìm kiếm cho Admin
    public function getPaginatedUsers(string $keyword = '', int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT id, ma_doc_gia, ho_ten, email, vai_tro, so_dien_thoai, bi_khoa, ly_do_khoa, tao_luc
                  FROM nguoi_dung
                 WHERE xoa_luc IS NULL";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (ho_ten LIKE ? OR email LIKE ? OR ma_doc_gia LIKE ? OR so_dien_thoai LIKE ?)";
            $kw = '%' . $keyword . '%';
            $params = [$kw, $kw, $kw, $kw];
        }

        $sql .= " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";
        return $this->fetchAll($sql, $params);
    }

    // Tổng số dòng người dùng để tính phân trang
    public function getTotalUsersCount(string $keyword = ''): int
    {
        $sql = "SELECT COUNT(*) AS total FROM nguoi_dung WHERE xoa_luc IS NULL";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (ho_ten LIKE ? OR email LIKE ? OR ma_doc_gia LIKE ? OR so_dien_thoai LIKE ?)";
            $kw = '%' . $keyword . '%';
            $params = [$kw, $kw, $kw, $kw];
        }

        $row = $this->fetchOne($sql, $params);
        return (int) ($row['total'] ?? 0);
    }

    // Cập nhật trạng thái Khóa / Mở khóa
    public function updateLockStatus(int $id, int $biKhoa, ?string $lyDo = null): bool
    {
        return $this->execute(
            "UPDATE nguoi_dung 
                SET bi_khoa = ?, ly_do_khoa = ? 
              WHERE id = ? AND xoa_luc IS NULL",
            [$biKhoa, $biKhoa === 1 ? $lyDo : null, $id]
        ) > 0;
    }

    // Cập nhật Vai trò
    public function updateRole(int $id, string $vaiTro): bool
    {
        return $this->execute(
            "UPDATE nguoi_dung SET vai_tro = ? WHERE id = ? AND xoa_luc IS NULL",
            [$vaiTro, $id]
        ) > 0;
    }

    // API Tìm kiếm độc giả (dành cho TV3 dùng khi lập phiếu mượn)
    public function searchReadersForApi(string $keyword): array
    {
        $sql = "SELECT id, ma_doc_gia, ho_ten, email, so_dien_thoai, bi_khoa, ly_do_khoa
                  FROM nguoi_dung
                 WHERE xoa_luc IS NULL 
                   AND vai_tro = 'doc_gia'";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (ho_ten LIKE ? OR email LIKE ? OR ma_doc_gia LIKE ?)";
            $kw = '%' . $keyword . '%';
            $params = [$kw, $kw, $kw];
        }

        $sql .= " ORDER BY ho_ten ASC LIMIT 10";
        return $this->fetchAll($sql, $params);
    }
}