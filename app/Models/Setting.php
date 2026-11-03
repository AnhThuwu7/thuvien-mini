<?php

class Setting extends Model
{
    /**
     * Khoảng giá trị hợp lệ (số nguyên) cho từng khóa cấu hình.
     * Khóa nào không có trong bảng này chỉ cần không rỗng và dài tối đa 255 ký tự.
     */
    public const RULES = [
        'so_ngay_muon'            => ['min' => 1, 'max' => 90,      'label' => 'Số ngày mượn mặc định'],
        'so_sach_toi_da'          => ['min' => 1, 'max' => 20,      'label' => 'Số sách tối đa được mượn cùng lúc'],
        'so_lan_gia_han_toi_da'   => ['min' => 0, 'max' => 5,       'label' => 'Số lần gia hạn tối đa'],
        'so_ngay_moi_lan_gia_han' => ['min' => 1, 'max' => 30,      'label' => 'Số ngày mỗi lần gia hạn'],
        'so_ngay_giu_sach'        => ['min' => 1, 'max' => 14,      'label' => 'Số ngày giữ sách sau khi duyệt'],
        'so_gio_giu_sach'         => ['min' => 1, 'max' => 336,     'label' => 'Số giờ giữ sách sau khi duyệt'],
        'so_ngay_sap_den_han'     => ['min' => 1, 'max' => 14,      'label' => 'Số ngày tính là "sắp đến hạn"'],
        'tien_phat_moi_ngay'      => ['min' => 0, 'max' => 1000000, 'label' => 'Phí phạt mỗi ngày quá hạn (VNĐ)'],
    ];

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

    /**
     * Kiểm tra dữ liệu gửi lên. Trả về mảng lỗi (rỗng nếu hợp lệ).
     * Chỉ chấp nhận các khóa đã tồn tại trong bảng cau_hinh.
     *
     * @param array<string,mixed> $input khoa => gia_tri
     * @return string[]
     */
    public function validate(array $input): array
    {
        $errors = [];
        $known = array_column($this->getAll(), 'khoa');

        foreach ($input as $khoa => $giaTri) {
            $khoa = (string) $khoa;

            if (!in_array($khoa, $known, true)) {
                $errors[] = 'Khóa cấu hình "' . $khoa . '" không tồn tại.';
                continue;
            }
            if (!is_scalar($giaTri)) {
                $errors[] = 'Giá trị của "' . $khoa . '" không hợp lệ.';
                continue;
            }

            $value = trim((string) $giaTri);

            if (isset(self::RULES[$khoa])) {
                $rule = self::RULES[$khoa];
                $isInt = preg_match('/^\d{1,9}$/', $value) === 1;

                if (!$isInt || (int) $value < $rule['min'] || (int) $value > $rule['max']) {
                    $errors[] = $rule['label'] . ' phải là số nguyên từ '
                        . $rule['min'] . ' đến ' . $rule['max'] . '.';
                }
            } elseif ($value === '' || mb_strlen($value) > 255) {
                $errors[] = 'Giá trị của "' . $khoa . '" không được để trống và tối đa 255 ký tự.';
            }
        }

        return $errors;
    }

    /**
     * Cập nhật mảng cấu hình (khoa => gia_tri).
     * Gọi validate() trước; hàm này ghi tất cả trong một transaction.
     */
    public function updateSettings(array $settings): bool
    {
        $this->transaction(function () use ($settings): void {
            foreach ($settings as $khoa => $giaTri) {
                $this->execute(
                    "UPDATE cau_hinh SET gia_tri = ? WHERE khoa = ?",
                    [trim((string) $giaTri), (string) $khoa]
                );
            }
        });

        return true;
    }
}