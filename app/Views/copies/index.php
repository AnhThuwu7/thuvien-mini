
<?php
$statuses = [
    'con_trong' => 'Còn trống',
    'dang_giu_cho' => 'Đang giữ chỗ',
    'dang_muon' => 'Đang mượn',
    'hong' => 'Hỏng',
    'mat' => 'Mất',
    'thanh_ly' => 'Thanh lý',
];

$statusColors = [
    'con_trong' => [
        'background' => '#dcfce7',
        'color' => '#166534',
    ],
    'dang_giu_cho' => [
        'background' => '#fef3c7',
        'color' => '#92400e',
    ],
    'dang_muon' => [
        'background' => '#dbeafe',
        'color' => '#1d4ed8',
    ],
    'hong' => [
        'background' => '#fee2e2',
        'color' => '#991b1b',
    ],
    'mat' => [
        'background' => '#fce7f3',
        'color' => '#9d174d',
    ],
    'thanh_ly' => [
        'background' => '#e5e7eb',
        'color' => '#374151',
    ],
];
?>

<section style="max-width: 1200px; margin: 0 auto; padding: 1.5rem 0;">

    <!-- Tiêu đề và nút thêm -->
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 1.8rem; margin-bottom: 0.35rem;">
                Quản lý bản sao sách
            </h1>

            <p style="color: var(--text-muted); margin: 0;">
                Quản lý từng bản sao, vị trí lưu trữ và tình trạng sách trong thư viện.
            </p>
        </div>

        <a href="<?= e(url('copies/create')) ?>" class="btn">
            + Thêm bản sao
        </a>
    </div>

    <!-- Tìm kiếm và lọc -->
    <div style="background: #fff; padding: 1rem; border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 1.25rem;">
        <form
            method="get"
            action="<?= e(url('copies')) ?>"
            style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;"
        >
            <input
                type="search"
                name="q"
                value="<?= e($keyword) ?>"
                placeholder="Tìm mã bản sao, tên sách hoặc vị trí kệ..."
                style="flex: 1; min-width: 240px; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm);"
            >

            <select
                name="status"
                style="min-width: 190px; padding: 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-sm); background: #fff;"
            >
                <option value="">Tất cả trạng thái</option>

                <?php foreach ($statuses as $value => $label): ?>
                    <option
                        value="<?= e($value) ?>"
                        <?= $status === $value ? 'selected' : '' ?>
                    >
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn">
                Tìm kiếm
            </button>

            <a href="<?= e(url('copies')) ?>" class="btn btn-outline">
                Làm mới
            </a>
        </form>
    </div>

    <!-- Bảng danh sách -->
    <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius); overflow-x: auto;">
        <table style="width: 100%; min-width: 1050px; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--primary-light); text-align: left;">
                    <th style="padding: 0.85rem; width: 65px;">ID</th>
                    <th style="padding: 0.85rem; width: 150px;">Mã bản sao</th>
                    <th style="padding: 0.85rem;">Tên sách</th>
                    <th style="padding: 0.85rem; width: 145px;">Trạng thái</th>
                    <th style="padding: 0.85rem; width: 110px;">Vị trí kệ</th>
                    <th style="padding: 0.85rem; width: 125px;">Ngày nhập</th>
                    <th style="padding: 0.85rem; min-width: 160px;">Ghi chú</th>
                    <th style="padding: 0.85rem; text-align: center; width: 145px;">Thao tác</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($copies)): ?>
                    <tr>
                        <td colspan="8" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            <?php if ($keyword !== '' || $status !== ''): ?>
                                Không tìm thấy bản sao phù hợp với điều kiện tìm kiếm.
                            <?php else: ?>
                                Chưa có bản sao sách trong thư viện.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($copies as $copy): ?>
                        <?php
                        $copyStatus = $copy['trang_thai'];
                        $badge = $statusColors[$copyStatus] ?? [
                            'background' => '#f3f4f6',
                            'color' => '#374151',
                        ];
                        ?>

                        <tr style="border-top: 1px solid var(--border);">
                            <td style="padding: 0.85rem;">
                                <?= (int) $copy['id'] ?>
                            </td>

                            <td style="padding: 0.85rem; font-weight: 600; white-space: nowrap;">
                                <?= e($copy['ma_ban_sao']) ?>
                            </td>

                            <td style="padding: 0.85rem; font-weight: 600;">
                                <?= e($copy['ten_sach']) ?>
                            </td>

                            <td style="padding: 0.85rem;">
                                <span style="display: inline-block; padding: 0.35rem 0.6rem; border-radius: 999px; background: <?= e($badge['background']) ?>; color: <?= e($badge['color']) ?>; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">
                                    <?= e($statuses[$copyStatus] ?? $copyStatus) ?>
                                </span>
                            </td>

                            <td style="padding: 0.85rem;">
                                <?= e($copy['vi_tri_ke'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem; white-space: nowrap;">
                                <?= e($copy['ngay_nhap'] ?? '—') ?>
                            </td>

                            <td style="padding: 0.85rem; max-width: 240px; overflow-wrap: anywhere;">
                                <?= e($copy['ghi_chu'] ?: '—') ?>
                            </td>

                            <td style="padding: 0.85rem; text-align: center; white-space: nowrap;">
                                <a
                                    href="<?= e(url('copies/edit/' . (int) $copy['id'])) ?>"
                                    class="btn"
                                >
                                    Sửa
                                </a>

                                <?php if (!in_array($copyStatus, ['dang_muon', 'dang_giu_cho'], true)): ?>
                                    <form
                                        method="POST"
                                        action="<?= e(url('copies/delete/' . (int) $copy['id'])) ?>"
                                        style="display: inline;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản sao này không?');"
                                    >
                                        <?= csrf_field() ?>

                                        <button
                                            type="submit"
                                            class="btn"
                                            style="background: #dc2626; color: #fff; border: none; margin-left: 0.25rem;"
                                        >
                                            Xóa
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Tổng số bản sao -->
    <p style="margin-top: 0.75rem; color: var(--text-muted); font-size: 0.9rem;">
        Tổng số bản sao đang hiển thị: <?= count($copies) ?>
    </p>

    <div style="margin-top: 1rem;">
        <a href="<?= e(url('books')) ?>" class="btn btn-outline">
            Quay lại quản lý sách
        </a>
    </div>

</section>
