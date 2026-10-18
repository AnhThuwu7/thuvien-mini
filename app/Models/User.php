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
}