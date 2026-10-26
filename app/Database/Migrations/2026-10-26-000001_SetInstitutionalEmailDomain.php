<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetInstitutionalEmailDomain extends Migration
{
    public function up()
    {
        $settings = $this->db->table('settings');
        $now = date('Y-m-d H:i:s');
        if ($settings->where('key', 'email_domain')->countAllResults() === 0) {
            $settings->insert(['key' => 'email_domain', 'value' => 'sksu.edu.ph', 'updated_at' => $now]);
            return;
        }

        $settings
            ->where('key', 'email_domain')
            ->where('value', '')
            ->update(['value' => 'sksu.edu.ph', 'updated_at' => $now]);
    }

    public function down()
    {
        $this->db->table('settings')
            ->where('key', 'email_domain')
            ->where('value', 'sksu.edu.ph')
            ->update(['value' => '', 'updated_at' => date('Y-m-d H:i:s')]);
    }
}
