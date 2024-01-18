<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MvMarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mv_marketplaces')->delete();
        
        DB::table('mv_marketplaces')->insert(array (
            0 => 
            array (
                'id' => 1,
                'marketplace' => 'Mercado Libre',
                'auart' => 'ZMV0',
                'payment_id' => '1',
                'connection' => 'mercadolibre-connections',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'marketplace' => 'Paris',
                'auart' => 'ZMV2',
                'payment_id' => '3',
                'connection' => 'paris-connections',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'marketplace' => 'Ripley',
                'auart' => 'ZMV3',
                'payment_id' => '2',
                'connection' => 'ripley-connections',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'marketplace' => 'Fcom',
                'auart' => 'ZMV4',
                'payment_id' => '4',
                'connection' => 'fcom-connections',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
        ));
    }
}

