<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MvStoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mv_stores')->delete();
        
        DB::table('mv_stores')->insert(array (
            0 => 
            array (
                'id' => 1,
                'connection' => 'Adidas Tienda Oficial',
                'marketplace_id' => '1',
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'connection' => 'ventasmeli@yaneken.cl',
                'marketplace_id' => '1',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'connection' => 'Quiksilver Roxy Meli',
                'marketplace_id' => '1',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'connection' => 'Crocs Meli',
                'marketplace_id' => '1',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'connection' => 'Bamers Meli',
                'marketplace_id' => '1',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            5 => 
            array (
                'id' => 6,
                'connection' => 'Oakley Racinglab Meli',
                'marketplace_id' => '1',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            6 => 
            array (
                'id' => 7,
                'connection' => 'Aufbau',
                'marketplace_id' => '1',
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            7 => 
            array (
                'id' => 8,
                'connection' => 'Roxy - Quiksilver Paris',
                'marketplace_id' => '2',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            8 => 
            array (
                'id' => 9,
                'connection' => 'Oakley Paris',
                'marketplace_id' => '2',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            9 => 
            array (
                'id' => 10,
                'connection' => 'Crocs Paris',
                'marketplace_id' => '2',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            10 => 
            array (
                'id' => 11,
                'connection' => 'Bamers Paris',
                'marketplace_id' => '2',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            11 => 
            array (
                'id' => 12,
                'connection' => 'Oakley F.com',
                'marketplace_id' => '4',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            12 => 
            array (
                'id' => 13,
                'connection' => 'Crocs F.com',
                'marketplace_id' => '4',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            13 => 
            array (
                'id' => 14,
                'connection' => 'Bamers F.com',
                'marketplace_id' => '4',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            14 => 
            array (
                'id' => 15,
                'connection' => 'Belsport (Qs/Rx/Saucony) F.com',
                'marketplace_id' => '4',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            15 => 
            array (
                'id' => 16,
                'connection' => 'Crocs Ripley',
                'marketplace_id' => '3',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            16 => 
            array (
                'id' => 17,
                'connection' => 'Bamers Ripley',
                'marketplace_id' => '3',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            17 => 
            array (
                'id' => 18,
                'connection' => 'Quiksilver Roxy Ripley',
                'marketplace_id' => '3',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            18 => 
            array (
                'id' => 19,
                'connection' => 'Oakley Ripley',
                'marketplace_id' => '3',
                'active' => false,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),

        ));
    }
}
