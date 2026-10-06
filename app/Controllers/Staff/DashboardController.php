<?php

declare(strict_types=1);

namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Services\DashboardService;

final class DashboardController extends BaseController
{
    public function __construct(private readonly DashboardService $dashboard = new DashboardService()) {}
    public function index(): string { return view('staff/dashboard', $this->dashboard->overviewData()); }
    public function settings(): string { return view('staff/settings', ['settings'=>$this->dashboard->settings()]); }
    public function saveSettings()
    {
        $data = $this->request->getPost();
        if (! $this->validateData($data, ['replacement_fee'=>'required|decimal','release_offset_months'=>'required|integer','email_domain'=>'required|max_length[100]'])) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $this->dashboard->saveSettings($data);
        return redirect()->back()->with('success', 'Settings saved.');
    }
}
