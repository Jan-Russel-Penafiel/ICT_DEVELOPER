<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInstitutionalAccountRequests extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ref_no' => ['type' => 'VARCHAR', 'constraint' => 32],
            'student_no' => ['type' => 'VARCHAR', 'constraint' => 50],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'middle_name' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'campus_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'program_strand' => ['type' => 'VARCHAR', 'constraint' => 120],
            'year_level' => ['type' => 'VARCHAR', 'constraint' => 30],
            'section' => ['type' => 'VARCHAR', 'constraint' => 50],
            'contact_no' => ['type' => 'VARCHAR', 'constraint' => 30],
            'email' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('ref_no');
        $this->forge->addKey(['campus_id', 'student_no']);
        $this->forge->createTable('institutional_account_requests', true);
    }

    public function down()
    {
        $this->forge->dropTable('institutional_account_requests', true);
    }
}
