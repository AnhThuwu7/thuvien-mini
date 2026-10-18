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
            if (is_array($settingsData) && !empty($settingsData)) {
                $settingModel->updateSettings($settingsData);
                flash('success', 'Đã lưu cấu hình hệ thống thành công.');
            }
            $this->redirect(url('settings'));
        }

        $settings = $settingModel->getAll();

        $this->view('settings/index', [
            'title'    => 'Cấu hình hệ thống',
            'settings' => $settings,
        ]);
    }
}