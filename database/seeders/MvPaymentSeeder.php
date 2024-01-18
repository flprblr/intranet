<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MvPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mv_payments')->delete();
        
        DB::table('mv_payments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'description' => 'MercadoPago',
                'sap_id' => '4015',
                'pos_id' => '110',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'description' => 'Marketplace Ripley',
                'sap_id' => '4018',
                'pos_id' => '112',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'description' => 'Marketplace Paris',
                'sap_id' => '4019',
                'pos_id' => '113',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'description' => 'Marketplace FPay',
                'sap_id' => '4020',
                'pos_id' => '114',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
        ));
    }
}
