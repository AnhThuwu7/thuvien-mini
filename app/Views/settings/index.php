<?php
/**
 * @var array $settings
 * @var string $title
 */
?>
<section class="card" style="max-width: 700px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1>Cấu hình hệ thống</h1>
        <a href="<?= e(url('users')) ?>" class="btn btn-outline">← Quản lý tài khoản</a>
    </div>

    <form method="post" action="<?= e(url('settings')) ?>">
        <?= csrf_field() ?>

        <?php if (!empty($settings)): ?>
            <?php foreach ($settings as $item): ?>
                <div style="margin-bottom: 1.25rem;">
                    <label for="setting_<?= e($item['khoa']) ?>" style="font-weight: 600; display: block; margin-bottom: 0.35rem;">
                        <?= e($item['khoa']) ?> 
                        <?php if (!empty($item['mo_ta'])): ?>
                            <small style="font-weight: normal; color: var(--text-muted);">— <?= e($item['mo_ta']) ?></small>
                        <?php endif; ?>
                    </label>
                    <input type="text" 
                           id="setting_<?= e($item['khoa']) ?>" 
                           name="settings[<?= e($item['khoa']) ?>]" 
                           value="<?= e($item['gia_tri']) ?>" 
                           required>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <button type="submit" class="btn" style="margin-top: 1rem;">Lưu cấu hình</button>
    </form>
</section>