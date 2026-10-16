<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddInstitutionalAccountStatuses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('account_batches', [
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active', 'after' => 'created_by'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('account_batches', 'status');
    }
}
