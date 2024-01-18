<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MvAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mv_access')->delete();
        
        DB::table('mv_access')->insert(array (
            0 => 
            array (
                'id' => 1,
                'base_url' => 'https://app.multivende.com/',
                'client_id' => '636354652132',
                'client_secret' => 'V1ZhpZOOdZxNvK3CRFqY8r1QsiwFkQaMCiNxFYbatRwGuxjN85',
                'code' => NULL,
                'merchant_id' => NULL,
                'token' => NULL,
                'token_refresh' => NULL,
                'expires_token' => NULL,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
        ));
        
    }
}
