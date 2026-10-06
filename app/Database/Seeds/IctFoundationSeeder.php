<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class IctFoundationSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        foreach ([
            ['ACCESS', 'ACCESS Campus (Main)'], ['ISULAN', 'Isulan Campus'], ['TACURONG', 'Tacurong Campus'],
            ['KALAMANSIG', 'Kalamansig Campus'], ['BAGUMBAYAN', 'Bagumbayan Campus'], ['PALIMBANG', 'Palimbang Campus'], ['LUTAYAN', 'Lutayan Campus'], ['SHS', 'Senior High School'],
        ] as [$code, $name]) $this->db->table('campuses')->ignore(true)->insert(['code'=>$code, 'name'=>$name, 'created_at'=>$now, 'updated_at'=>$now]);
        foreach (['replacement_fee'=>'100', 'release_offset_months'=>'1', 'email_domain'=>''] as $key=>$value) $this->db->table('settings')->replace(['key'=>$key, 'value'=>$value, 'updated_at'=>$now]);
    }
}
