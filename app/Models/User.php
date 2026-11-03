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
        public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT id FROM nguoi_dung WHERE email = ? AND xoa_luc IS NULL';
        $params = [$email];
        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }
        $sql .= ' LIMIT 1';
        return $this->fetchOne($sql, $params) !== null;
    }

    public function nextReaderCode(): string
    {
        $row = $this->fetchOne(
            "SELECT ma_doc_gia FROM nguoi_dung
              WHERE ma_doc_gia IS NOT NULL AND ma_doc_gia LIKE 'DG%'
              ORDER BY id DESC
              LIMIT 1"
        );
        $n = 1;
        if ($row && preg_match('/(\d+)/', (string) $row['ma_doc_gia'], $m)) {
            $n = (int) $m[1] + 1;
        }
        return 'DG' . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public function registerReader(string $hoTen, string $email, string $password, ?string $phone = null): int
    {
        $hoTen = trim($hoTen);
        $email = trim(mb_strtolower($email));
        $phone = $phone !== null ? trim($phone) : null;
        if ($phone === '') {
            $phone = null;
        }

        if ($hoTen === '' || mb_strlen($hoTen) > 100) {
            throw new RuntimeException('Họ tên không hợp lệ.');
        }
        if ($email === '' || mb_strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Email không hợp lệ.');
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\s]).{8,}$/u', $password)) {
            throw new RuntimeException(
                'Mật khẩu tối thiểu 8 ký tự, gồm chữ hoa, chữ thường và ký hiệu đặc biệt.'
            );
        }
        if ($this->emailExists($email)) {
            throw new RuntimeException('Email này đã được đăng ký.');
        }

        $code = $this->nextReaderCode();
        for ($i = 0; $i < 5; $i++) {
            $dup = $this->fetchOne(
                'SELECT id FROM nguoi_dung WHERE ma_doc_gia = ? LIMIT 1',
                [$code]
            );
            if (!$dup) {
                break;
            }
            $code = $this->nextReaderCode();
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $this->execute(
            "INSERT INTO nguoi_dung
                (ma_doc_gia, ho_ten, email, mat_khau_bam, vai_tro, so_dien_thoai, bi_khoa)
             VALUES (?, ?, ?, ?, 'doc_gia', ?, 0)",
            [$code, $hoTen, $email, $hash, $phone]
        );
        return $this->lastInsertId();
    }
}