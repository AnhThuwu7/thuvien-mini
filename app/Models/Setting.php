<?php

class Setting extends Model
{
    // Lấy tất cả cấu hình hệ thống
    public function getAll(): array
    {
        return $this->fetchAll("SELECT khoa, gia_tri, mo_ta FROM cau_hinh ORDER BY khoa ASC");
    }

    // Cập nhật mảng cấu hình
    public function updateSettings(array $settings): bool
    {
        foreach ($settings as $khoa => $giaTri) {
            $this->execute(
                "UPDATE cau_hinh SET gia_tri = ? WHERE khoa = ?",
                [trim((string)$giaTri), (string)$khoa]
            );
        }
        return true;
    }
}