<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReclassifyIndividualAccounts extends Migration
{
    public function up()
    {
        $db = $this->db;
        $db->transStart();
        $batchIds = $db->table('account_batches')
            ->select('id')
            ->where('source_file', 'individual')
            ->get()
            ->getResultArray();

        if ($batchIds !== []) {
            $ids = array_column($batchIds, 'id');
            $db->table('accounts')->whereIn('account_batch_id', $ids)->update(['account_batch_id' => null]);
            $db->table('account_batches')->whereIn('id', $ids)->delete();
        }

        $db->transComplete();
        if (! $db->transStatus()) {
            throw new \RuntimeException('Legacy individual accounts could not be reclassified.');
        }
    }

    public function down()
    {
    }
}
