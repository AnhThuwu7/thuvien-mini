<?php
/**
 * @var array $me
 * @var string $title
 */
?>
<section class="card" style="max-width: 560px; margin: 0 auto;">
    <h1 style="margin-bottom: 1.5rem;">Hồ sơ của tôi</h1>

    <div class="profile-grid">
        <div class="profile-item">
            <span class="profile-label">Họ tên</span>
            <span class="profile-value"><?= e($me['ho_ten']) ?></span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Email</span>
            <span class="profile-value"><?= e($me['email']) ?></span>
        </div>

        <?php if (!empty($me['ma_doc_gia'])): ?>
        <div class="profile-item">
            <span class="profile-label">Mã độc giả</span>
            <span class="profile-value"><?= e($me['ma_doc_gia']) ?></span>
        </div>
        <?php endif; ?>

        <div class="profile-item">
            <span class="profile-label">Vai trò</span>
            <span class="profile-value">
                <span class="badge"><?= e(Auth::roleLabel($me['vai_tro'])) ?></span>
            </span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Số điện thoại</span>
            <span class="profile-value"><?= e($me['so_dien_thoai'] ?? 'Chưa cập nhật') ?></span>
        </div>

        <div class="profile-item">
            <span class="profile-label">Trạng thái</span>
            <span class="profile-value">
                <?php if (!empty($me['bi_khoa'])): ?>
                    <span class="badge badge-danger">Bị khóa</span>
                    <?php if (!empty($me['ly_do_khoa'])): ?>
                        <div class="profile-note"><?= e($me['ly_do_khoa']) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="badge badge-success">Bình thường</span>
                <?php endif; ?>
            </span>
        </div>
    </div>
</section>

<style>
.profile-grid {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.profile-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border);
}

.profile-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.profile-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.profile-value {
    font-size: 1.05rem;
    color: var(--text);
    font-weight: 500;
}

.profile-note {
    margin-top: 0.35rem;
    font-size: 0.9rem;
    color: var(--danger);
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    background: var(--primary-light);
    color: var(--primary-dark);
}

.badge-success {
    background: #EBF8F1;
    color: #276749;
}

.badge-danger {
    background: #FFF5F5;
    color: #C53030;
}
</style>