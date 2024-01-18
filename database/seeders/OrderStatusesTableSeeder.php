<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('order_statuses')->delete();

        DB::table('order_statuses')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Procesando',
                'slug' => 'processing',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'POS: Error Producto',
                'slug' => 'pos-sku-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Sovos: Enviado',
                'slug' => 'sovos-sent',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Sovos: Error',
                'slug' => 'sovos-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Sovos: Emitido',
                'slug' => 'sovos-ok',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'POS: Enviado',
                'slug' => 'pos-sent',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'POS: Error',
                'slug' => 'pos-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'POS: Emitido',
                'slug' => 'pos-ok',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Woocommerce: Completado',
                'slug' => 'completed',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'En Camino',
                'slug' => 'en-camino',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'En Preparación',
                'slug' => 'en-preparacion',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            11 =>
            array(
                'id' => 20,
                'name' => 'Sovos: Reverse',
                'slug' => 'sovos-rev',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            12 =>
            array(
                'id' => 21,
                'name' => 'Sovos: Reverse Complete',
                'slug' => 'sovos-rev-ok',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            13 =>
            array(
                'id' => 22,
                'name' => 'Sovos: Reverse Enviado',
                'slug' => 'sovos-rev-sent',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            14 =>
            array(
                'id' => 23,
                'name' => 'Sovos: Error',
                'slug' => 'sovos-rev-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            15 =>
            array(
                'id' => 24,
                'name' => 'POS: Enviado Reverse',
                'slug' => 'pos-rev-sent',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            16 =>
            array(
                'id' => 25,
                'name' => 'POS: Error Reverse',
                'slug' => 'pos-rev-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            17 =>
            array(
                'id' => 26,
                'name' => 'POS: Emitido Reverse',
                'slug' => 'pos-rev-ok',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            18 =>
            array(
                'id' => 12,
                'name' => 'SAP: Error',
                'slug' => 'sap-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            19 =>
            array(
                'id' => 13,
                'name' => 'SAP: Enviado',
                'slug' => 'sap-sent',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            20 =>
            array(
                'id' => 14,
                'name' => 'SAP: Emitido',
                'slug' => 'sap-ok',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            21 =>
            array(
                'id' => 15,
                'name' => 'Multivende: Error',
                'slug' => 'mv-doc-error',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),

            22 =>
            array(
                'id' => 16,
                'name' => 'Multivende: Enviado',
                'slug' => 'mv-doc-sent',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),

            23 =>
            array(
                'id' => 17,
                'name' => 'Multivende: Emitido',
                'slug' => 'mv-doc-ok',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            )


        ));
    }
}
