<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EcommercesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        // DB::table('ecommerces')->delete();

        DB::table('ecommerces')->insert(array(
            0 =>
            array(
                'id' => 1,
                'prefix' => 'YNK',
                'url' => 'https://tienda.ynk.cl/',
                'api_key' => 'ck_92ba852ca857df6dea29894e3f56f1bf3c6dcef4',
                'api_secret' => 'cs_6e7afbe66c0823d95c2759ed13f495846343d114',
                'logo' => 'ynk',
                'VKORG' => 'AAAA',
                'WERKS' => 'BBBB',
                'LGORT' => 'CCCC',
                'AUART' => 'DDDD',
                'FKART' => 'EEEE',
                'company_id' => 2,
                'created_at' => '2023-07-19 18:10:28',
                'updated_at' => '2023-07-19 18:10:28',
            ),
            1 =>
            array(
                'id' => 2,
                'prefix' => 'HOKA',
                'url' => 'https://stg-tiendahoka-staging.kinsta.cloud/',
                'api_key' => 'ck_9b9ca7fa966bf702023b7c690c9fa04cf998dd3b',
                'api_secret' => 'cs_027a5eaf1bb9d0c35cb7e7d1ac55d5ab8863cce5',
                'logo' => 'ynk',
                'VKORG' => 'AAAA',
                'WERKS' => 'BBBB',
                'LGORT' => 'CCCC',
                'AUART' => 'DDDD',
                'FKART' => 'EEEE',
                'company_id' => 2,
                'created_at' => '2023-07-19 18:10:28',
                'updated_at' => NULL,
            ),
        ));
    }
}
