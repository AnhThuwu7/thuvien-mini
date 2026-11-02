
<?php
$statusLabels = [
    'con_trong' => 'Còn trống',
    'dang_giu_cho' => 'Đang giữ chỗ',
    'dang_muon' => 'Đang mượn',
    'hong' => 'Hỏng',
    'mat' => 'Mất',
    'thanh_ly' => 'Thanh lý',
];
?>

<section style="max-width: 900px; margin: 0 auto; padding: 1.5rem 0;">

    <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.8rem; margin-bottom: 0.35rem;">
            Sửa bản sao sách
        </h1>

        <p style="color: var(--text-muted); margin: 0;">
            Cập nhật thông tin và tình trạng bản sao trong thư viện.
        </p>
    </div>

    <?php if (!empty($errors)): ?>
        <div style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; border-radius: var(--radius); padding: 1rem; margin-bottom: 1.25rem;">
            <strong>Vui lòng kiểm tra lại thông tin:</strong>

            <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem;">

        <form method="POST" action="<?= e(url('copies/update/' . (int) $copy['id'])) ?>">
            <?= csrf_field() ?>

            <div style="margin-bottom: 1.25rem;">
                <label for="sach_id" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Đầu sách <span style="color: #dc2626;">*</span>
                </label>

                <select
                    id="sach_id"
                    name="sach_id"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;"
                >
                    <option value="">-- Chọn đầu sách --</option>

                    <?php foreach ($books as $book): ?>
                        <option
                            value="<?= (int) $book['id'] ?>"
                            <?= (string) $form['sach_id'] === (string) $book['id'] ? 'selected' : '' ?>
                        >
                            <?= e($book['ten_sach']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="ma_ban_sao" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Mã bản sao <span style="color: #dc2626;">*</span>
                </label>

                <input
                    id="ma_ban_sao"
                    type="text"
                    name="ma_ban_sao"
                    maxlength="30"
                    value="<?= e($form['ma_ban_sao']) ?>"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); box-sizing: border-box;"
                >

                <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.4rem 0 0;">
                    Mã bản sao phải riêng biệt, không trùng với mã đã có.
                </p>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label for="trang_thai" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Trạng thái
                </label>

                <select
                    id="trang_thai"
                    name="trang_thai"
                    required
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;"
                >
                    <?php foreach ($statuses as $value): ?>
                        <option
                            value="<?= e($value) ?>"
                            <?= $form['trang_thai'] === $value ? 'selected' : '' ?>
                        >
                            <?= e($statusLabels[$value] ?? $value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">

                <div style="margin-bottom: 1.25rem;">
                    <label for="vi_tri_ke" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Vị trí kệ
                    </label>

                    <input
                        id="vi_tri_ke"
                        type="text"
                        name="vi_tri_ke"
                        maxlength="50"
                        value="<?= e($form['vi_tri_ke'] ?? '') ?>"
                        placeholder="Ví dụ: Kệ F1"
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); box-sizing: border-box;"
                    >
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label for="ngay_nhap" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                        Ngày nhập
                    </label>

                    <input
                        id="ngay_nhap"
                        type="date"
                        name="ngay_nhap"
                        value="<?= e($form['ngay_nhap'] ?? '') ?>"
                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); box-sizing: border-box;"
                    >
                </div>

            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="ghi_chu" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                    Ghi chú
                </label>

                <textarea
                    id="ghi_chu"
                    name="ghi_chu"
                    maxlength="255"
                    rows="3"
                    placeholder="Nhập ghi chú nếu có..."
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); box-sizing: border-box; resize: vertical;"
                ><?= e($form['ghi_chu'] ?? '') ?></textarea>
            </div>

            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; border-top: 1px solid var(--border); padding-top: 1.25rem;">
                <button type="submit" class="btn">
                    Cập nhật bản sao
                </button>

                <a href="<?= e(url('copies')) ?>" class="btn btn-outline">
                    Quay lại
                </a>
            </div>

        </form>
    </div>

</section>
