<?php
/**
 * @var array $users
 * @var string $keyword
 * @var array $pagination
 * @var string $title
 */
?>
<section class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1>Quản lý Tài khoản</h1>
        <a href="<?= e(url('settings')) ?>" class="btn btn-outline">⚙️ Cấu hình hệ thống</a>
    </div>

    <!-- Ô tìm kiếm -->
    <form method="get" action="<?= e(url('users')) ?>" style="margin-bottom: 1.5rem; display: flex; gap: 0.5rem; max-width: 500px;">
        <input type="text" name="q" value="<?= e($keyword) ?>" placeholder="Tìm theo tên, email, mã độc giả, SĐT..." style="flex: 1;">
        <button type="submit" class="btn">Tìm kiếm</button>
    </form>

    <!-- Bảng người dùng -->
    <div style="overflow-x: auto;">
        <table class="table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border); text-align: left;">
                    <th style="padding: 0.75rem;">ID</th>
                    <th style="padding: 0.75rem;">Họ tên / Email</th>
                    <th style="padding: 0.75rem;">Mã ĐG</th>
                    <th style="padding: 0.75rem;">Vai trò</th>
                    <th style="padding: 0.75rem;">Trạng thái</th>
                    <th style="padding: 0.75rem; text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                            Không tìm thấy tài khoản nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as$u): ?>
                        <tr style="border-bottom: 1px solid var(--border);">
                            <td style="padding: 0.75rem;"><?= $u['id'] ?></td>
                            <td style="padding: 0.75rem;">
                                <strong><?= e($u['ho_ten']) ?></strong><br>
                                <small style="color: var(--text-muted);"><?= e($u['email']) ?></small>
                            </td>
                            <td style="padding: 0.75rem;">
                                <code><?= e($u['ma_doc_gia'] ?? '---') ?></code>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if ((int)$u['id'] !== Auth::id()): ?>
                                    <form method="post" action="<?= e(url('users/changeRole')) ?>" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <select name="vai_tro" 
                                                data-old="<?= e($u['vai_tro']) ?>" 
                                                onchange="openRoleModal(this)" 
                                                style="padding: 0.25rem 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                                            <option value="doc_gia" <?= $u['vai_tro'] === 'doc_gia' ? 'selected' : '' ?>>Độc giả</option>
                                            <option value="thu_thu" <?= $u['vai_tro'] === 'thu_thu' ? 'selected' : '' ?>>Thủ thư</option>
                                            <option value="quan_tri" <?= $u['vai_tro'] === 'quan_tri' ? 'selected' : '' ?>>Quản trị</option>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <span class="badge"><?= e(Auth::roleLabel($u['vai_tro'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem;">
                                <?php if (!empty($u['bi_khoa'])): ?>
                                    <span class="badge badge-danger" title="Lý do: <?= e($u['ly_do_khoa'] ?? 'Không rõ') ?>">Bị khóa</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Bình thường</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem; text-align: right;">
                                <?php if ((int)$u['id'] !== Auth::id()): ?>
                                    <?php if (!empty($u['bi_khoa'])): ?>
                                        <form method="post" action="<?= e(url('users/toggleLock')) ?>" style="display: inline;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                            <input type="hidden" name="bi_khoa" value="0">
                                            <button type="submit" class="btn btn-small btn-outline">Mở khóa</button>
                                        </form>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-small btn-danger" onclick="openLockModal(<?= $u['id'] ?>)">Khóa</button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <small style="color: var(--text-muted);">(Tài khoản bạn)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Form ẩn để Khóa tài khoản -->
    <form id="formLockUser" method="post" action="<?= e(url('users/toggleLock')) ?>" style="display: none;">
        <?= csrf_field() ?>
        <input type="hidden" name="id" id="lockUserId">
        <input type="hidden" name="bi_khoa" value="1">
        <input type="hidden" name="ly_do_khoa" id="lockReasonInput">
    </form>

    <!-- Phân trang -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 0.35rem;">
            <?php for ($i = 1; $i <= $pagination['total_pages'];$i++): ?>
                <a href="<?= e(url('users?q=' . urlencode($keyword) . '&page=' .$i)) ?>" 
                   class="btn btn-small <?= $i ===$pagination['page'] ? '' : 'btn-outline' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>

<!-- MODAL XÁC NHẬN ĐỔI VAI TRÒ -->
<div id="roleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius); max-width: 400px; width: 90%; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <h3 style="margin-top: 0;">Xác nhận đổi vai trò</h3>
        <p id="roleModalText" style="margin: 1rem 0; color: var(--text);"></p>
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
            <button type="button" class="btn btn-outline" onclick="cancelRoleChange()">Hủy</button>
            <button type="button" class="btn" onclick="submitRoleChange()">Đồng ý</button>
        </div>
    </div>
</div>

<!-- MODAL KHÓA TÀI KHOẢN -->
<div id="lockModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; padding: 1.5rem; border-radius: var(--radius); max-width: 400px; width: 90%; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        <h3 style="margin-top: 0; color: var(--danger);">Khóa tài khoản</h3>
        <p style="margin: 0.5rem 0; color: var(--text-muted); font-size: 0.9rem;">Vui lòng nhập lý do khóa tài khoản này:</p>
        <textarea id="lockReasonText" rows="3" style="width: 100%; margin-bottom: 1rem; padding: 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border);" placeholder="Ví dụ: Vi phạm quy định mượn sách..."></textarea>
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
            <button type="button" class="btn btn-outline" onclick="closeLockModal()">Hủy</button>
            <button type="button" class="btn btn-danger" onclick="submitLockUser()">Xác nhận khóa</button>
        </div>
    </div>
</div>

<script>
let currentSelectElem = null;

function openRoleModal(selectElem) {
    currentSelectElem = selectElem;
    const oldVal = selectElem.getAttribute('data-old');
    const newVal = selectElem.value;
    
    const roleMap = {
        'doc_gia': 'Độc giả',
        'thu_thu': 'Thủ thư',
        'quan_tri': 'Quản trị viên'
    };

    const oldLabel = roleMap[oldVal] || oldVal;
    const newLabel = roleMap[newVal] || newVal;

    document.getElementById('roleModalText').innerText = `Bạn có chắc chắn muốn đổi vai trò từ "${oldLabel}" sang "${newLabel}" không?`;
    document.getElementById('roleModal').style.display = 'flex';
}

function cancelRoleChange() {
    if (currentSelectElem) {
        currentSelectElem.value = currentSelectElem.getAttribute('data-old');
    }
    document.getElementById('roleModal').style.display = 'none';
}

function submitRoleChange() {
    if (currentSelectElem) {
        currentSelectElem.form.submit();
    }
}

function openLockModal(userId) {
    document.getElementById('lockUserId').value = userId;
    document.getElementById('lockReasonText').value = '';
    document.getElementById('lockModal').style.display = 'flex';
}

function closeLockModal() {
    document.getElementById('lockModal').style.display = 'none';
}

function submitLockUser() {
    const reason = document.getElementById('lockReasonText').value.trim();
    if (!reason) {
        alert('Vui lòng nhập lý do khóa!');
        return;
    }
    document.getElementById('lockReasonInput').value = reason;
    document.getElementById('formLockUser').submit();
}
</script>