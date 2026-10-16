<?php

declare(strict_types=1);

namespace App\Models;

final class InstitutionalAccountRequestModel extends BaseIctModel
{
    protected $table = 'institutional_account_requests';
    protected $allowedFields = [
        'ref_no',
        'student_no',
        'first_name',
        'last_name',
        'middle_name',
        'campus_id',
        'program_strand',
        'year_level',
        'section',
        'contact_no',
        'email',
        'status',
    ];

    public function allWithCampus(): array
    {
        return $this->select('institutional_account_requests.*, campuses.name campus')
            ->join('campuses', 'campuses.id = institutional_account_requests.campus_id')
            ->orderBy('institutional_account_requests.created_at', 'DESC')
            ->findAll();
    }

    public function findForTracking(string $reference): ?array
    {
        return $this->select('institutional_account_requests.*, campuses.name campus, institutional_account_requests.ref_no reference_no')
            ->join('campuses', 'campuses.id = institutional_account_requests.campus_id')
            ->where('institutional_account_requests.ref_no', $reference)
            ->first();
    }
}
