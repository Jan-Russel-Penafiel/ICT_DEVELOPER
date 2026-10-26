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

    public function individualWithDetails(): array
    {
        return $this->select('accounts.*, students.student_no, students.first_name, students.middle_name, students.last_name, campuses.name campus')
            ->join('students', 'students.id = accounts.student_id')
            ->join('campuses', 'campuses.id = students.campus_id')
            ->where('accounts.account_batch_id IS NULL', null, false)
            ->orderBy('accounts.created_at', 'DESC')
            ->findAll();
    }

    public function findIndividual(int $id): ?array
    {
        return $this->where('id', $id)->where('account_batch_id IS NULL', null, false)->first();
    }

    public function deleteForBatch(int $batchId): bool
    {
        return (bool) $this->where('account_batch_id', $batchId)->delete();
    }
}
