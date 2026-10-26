<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveInstitutionalAccountRequests extends Migration
{
    public function up()
    {
        $this->forge->dropTable('institutional_account_requests', true);
    }

    public function down()
    {
    }
}
