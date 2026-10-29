<?php

class Setting extends Model
{
    /** Lấy tất cả cấu hình hệ thống */
    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT khoa, gia_tri, mo_ta FROM cau_hinh ORDER BY khoa ASC"
        );
    }

    /**
     * Lấy một giá trị cấu hình theo khóa.
     * @param mixed $default giá trị mặc định nếu không có dòng
     */
    public function getValue(string $key, $default = null)
    {
        $row = $this->fetchOne(
            "SELECT gia_tri FROM cau_hinh WHERE khoa = ? LIMIT 1",
            [$key]
        );

        if ($row === null) {
            return $default;
        }

        return $row['gia_tri'];
    }

    /** Cập nhật mảng cấu hình (khoa => gia_tri) */
    public function updateSettings(array $settings): bool
    {
        foreach ($settings as $khoa => $giaTri) {
            $this->execute(
                "UPDATE cau_hinh SET gia_tri = ? WHERE khoa = ?",
                [trim((string) $giaTri), (string) $khoa]
            );
        }
        return true;
    }
}