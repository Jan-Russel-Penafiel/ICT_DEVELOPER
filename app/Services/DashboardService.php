<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SettingModel;

final class DashboardService
{
    public function __construct(private readonly SettingModel $settings = new SettingModel()) {}

    public function overviewData(): array
    {
        $db = db_connect();
        $counts = ['id'=>$db->table('id_requests')->whereIn('status', ['submitted','verified','paid'])->countAllResults(),'reset'=>$db->table('reset_requests')->where('status !=', 'closed')->countAllResults(),'repair'=>$db->table('repair_requests')->where('status !=', 'released')->countAllResults()];
        $map = static function (array $rows): array { $totals=[]; foreach ($rows as $row) $totals[(int) $row['campus_id']] = (int) $row['total']; return $totals; };
        $students = $map($db->table('students')->select('campus_id, COUNT(*) AS total')->groupBy('campus_id')->get()->getResultArray());
        $requests = $map($db->table('id_requests i')->select('s.campus_id, COUNT(i.id) AS total')->join('students s', 's.id = i.student_id')->groupBy('s.campus_id')->get()->getResultArray());
        $accounts = $map($db->table('accounts a')->select('s.campus_id, COUNT(a.id) AS total')->join('students s', 's.id = a.student_id')->groupBy('s.campus_id')->get()->getResultArray());
        $campusStats=[]; foreach ($db->table('campuses')->where('code !=', 'SHS')->orderBy('name')->get()->getResultArray() as $campus) { $id=(int) $campus['id']; $campusStats[]=['name'=>$campus['name'],'students'=>$students[$id] ?? 0,'requests'=>$requests[$id] ?? 0,'accounts'=>$accounts[$id] ?? 0]; }
        return compact('counts', 'campusStats');
    }

    public function settings(): array { $settings=[]; foreach ($this->settings->findAll() as $row) $settings[$row['key']] = $row['value']; return $settings; }
    public function saveSettings(array $data): void { foreach (['replacement_fee','release_offset_months','email_domain'] as $key) $this->settings->save(['key'=>$key,'value'=>trim((string) $data[$key]),'updated_at'=>date('Y-m-d H:i:s')]); }
}
