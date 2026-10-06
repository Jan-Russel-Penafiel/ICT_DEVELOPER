<?php

declare(strict_types=1);

namespace App\Models;

final class ResetRequestModel extends BaseIctModel
{
    protected $table = 'reset_requests';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['ref_no', 'student_id', 'student_no', 'requester_name', 'contact', 'channel', 'verified_by', 'status'];

    public function newestFirst(): array
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }

    public function findForTracking(string $reference): ?array
    {
        return $this->select('id, ref_no reference_no, status, created_at')
            ->where('ref_no', $reference)
            ->first();
    }
}
