<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MvWarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mv_warehouses')->delete();
        
        DB::table('mv_warehouses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'id_warehouse' => '393891f0-de64-489e-9dba-9cd51fe4de02',
                'description' => 'Bodega Belsport',
                'vkorg' => '1000',
                'werks' => 'AB10',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'BELSPORT VIRTUAL',
                'logo' => 'belsport.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'id_warehouse' => '360b8da3-b19f-4d64-ac86-369f0a7aa49b',
                'description' => 'Bodega Bold',
                'vkorg' => '2000',
                'werks' => 'AB20',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'BOLD VIRTUAL',
                'logo' => 'bold.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'id_warehouse' => '65415ae9-75ac-4d88-9844-4c0756951a7e',
                'description' => 'Bodega K1',
                'vkorg' => '3000',
                'werks' => 'AB30',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'K1 VIRTUAL',
                'logo' => 'k1.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'id_warehouse' => '6c30e5dd-4143-45a6-968c-31aa89e4558d',
                'description' => 'Bodega Quiksilver',
                'vkorg' => '7000',
                'werks' => 'AB70',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'QUIKSILVER VIRTUAL',
                'logo' => 'quiksilver.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'id_warehouse' => '0f0b1644-f2eb-444f-b901-3838f15db678',
                'description' => 'Bodega Roxy',
                'vkorg' => '7000',
                'werks' => 'AB71',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'ROXY VIRTUAL',
                'logo' => 'roxy.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            5 => 
            array (
                'id' => 6,
                'id_warehouse' => '1d1217d6-b362-4053-bc1e-7d57c9413185',
                'description' => 'Bodega Saucony',
                'vkorg' => '7400',
                'werks' => 'AB74',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'SAUCONY VIRTUAL',
                'logo' => 'saucony.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            6 => 
            array (
                'id' => 7,
                'id_warehouse' => '693aba65-2037-488c-be22-bdf4ef090e6f',
                'description' => 'Bodega Aufbau',
                'vkorg' => '6100',
                'werks' => '6103',
                'lgort' => '0003',
                'vtweg' => '05',
                'des_sovos' => 'MERCADOLIBRE',
                'logo' => 'lockerstore.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            7 => 
            array (
                'id' => 8,
                'id_warehouse' => '105fdbe3-3597-4c45-b2a3-3eefc5b5412b',
                'description' => 'Bodega Crocs',
                'vkorg' => 'C000',
                'werks' => 'CR01',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'CROCS VIRTUAL',
                'logo' => 'crocs.cl',
                'company_id' => 4,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            8 => 
            array (
                'id' => 9,
                'id_warehouse' => '206a46bc-429f-4a66-a3ea-d80ac6d25323',
                'description' => 'Bodega Bamers',
                'vkorg' => 'B000',
                'werks' => 'DX01',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'BAMERS VIRTUAL',
                'logo' => 'bamers.cl',
                'company_id' => 1,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            9 => 
            array (
                'id' => 10,
                'id_warehouse' => '978e7570-9186-4acf-be69-fe5491857927',
                'description' => 'Bodega Oakley Racinglab',
                'vkorg' => 'D000',
                'werks' => 'OK01',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'THELAB VIRTUAL',
                'logo' => 'thelabstore.cl',
                'company_id' => 3,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            10 => 
            array (
                'id' => 11,
                'id_warehouse' => 'd4db69e2-eb60-483f-969e-b88369e73958',
                'description' => 'Bodega Hoka',
                'vkorg' => '7500',
                'werks' => 'AB75',
                'lgort' => 'MP01',
                'vtweg' => '05',
                'des_sovos' => 'HOKA VIRTUAL',
                'logo' => 'hoka.cl',
                'company_id' => 2,
                'active' => true,
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
        ));
    }
}
