<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountBatchSourcePath extends Migration
{
    public function up()
    {
        $this->forge->addColumn('account_batches', ['source_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'source_file']]);
    }

    public function down()
    {
        $this->forge->dropColumn('account_batches', 'source_path');
    }
}
