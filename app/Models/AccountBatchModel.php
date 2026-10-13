<?php

declare(strict_types=1);

namespace App\Models;

final class AccountBatchModel extends BaseIctModel
{
    protected $table = 'account_batches';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['campus_id', 'section', 'source_file', 'source_path', 'exported_at', 'created_by'];

    public function withAccountCounts(): array
    {
        return $this->select('account_batches.*, c.name campus, COUNT(a.id) account_count')
            ->join('campuses c', 'c.id = account_batches.campus_id')
            ->join('accounts a', 'a.account_batch_id = account_batches.id', 'left')
            ->groupBy('account_batches.id')
            ->orderBy('account_batches.created_at', 'DESC')
            ->findAll();
    }
}
