<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOperationalSoftDeletes extends Migration
{
    private array $tables = ['registration_links', 'students', 'id_requests', 'account_batches', 'accounts', 'reset_requests', 'repair_requests', 'staff_profiles'];

    public function up()
    {
        foreach ($this->tables as $table) {
            $this->forge->addColumn($table, ['deleted_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'updated_at']]);
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            $this->forge->dropColumn($table, 'deleted_at');
        }
    }
}
