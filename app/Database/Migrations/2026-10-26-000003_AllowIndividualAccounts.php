<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowIndividualAccounts extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('accounts', [
            'account_batch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('accounts', [
            'account_batch_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
        ]);
    }
}
