<?php

declare(strict_types=1);

namespace App\Models;

final class AccountModel extends BaseIctModel
{
    protected $table = 'accounts';
    protected $allowedFields = ['account_batch_id', 'student_id', 'email', 'org_unit'];

    public function forBatchExport(int $batchId): array
    {
        return $this->select('accounts.*, students.first_name, students.last_name')
            ->join('students', 'students.id = accounts.student_id')
            ->where('account_batch_id', $batchId)
            ->findAll();
    }

    public function deleteForBatch(int $batchId): bool
    {
        return (bool) $this->where('account_batch_id', $batchId)->delete();
    }
}
