<?php

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->requireRole('quan_tri');
    }

    // GET & POST /settings
    public function index(): void
    {
        $settingModel = $this->model('Setting');

        if (is_post()) {
            $settingsData = $_POST['settings'] ?? [];

            if (!is_array($settingsData) || empty($settingsData)) {
                flash('error', 'Không có dữ liệu cấu hình để lưu.');
                $this->redirect(url('settings'));
            }

            $errors = $settingModel->validate($settingsData);

            if ($errors) {
                // Hiển thị lại form với giá trị vừa nhập để người dùng sửa
                flash('error', implode(' ', $errors));

                $settings = $settingModel->getAll();
                foreach ($settings as &$item) {
                    if (isset($settingsData[$item['khoa']]) && is_scalar($settingsData[$item['khoa']])) {
                        $item['gia_tri'] = (string) $settingsData[$item['khoa']];
                    }
                }
                unset($item);

                $this->view('settings/index', [
                    'title'    => 'Cấu hình hệ thống',
                    'settings' => $settings,
                ]);
                return;
            }

            try {
                $settingModel->updateSettings($settingsData);
                flash('success', 'Đã lưu cấu hình hệ thống thành công.');
            } catch (Throwable $e) {
                error_log('[SettingsController::index] ' . $e->getMessage());
                flash('error', 'Không lưu được cấu hình. Vui lòng thử lại.');
            }

            $this->redirect(url('settings'));
        }

        $this->view('settings/index', [
            'title'    => 'Cấu hình hệ thống',
            'settings' => $settingModel->getAll(),
        ]);
    }
}