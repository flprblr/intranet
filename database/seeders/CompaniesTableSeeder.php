<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompaniesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('companies')->delete();
        
        DB::table('companies')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'COMERCIAL DEXIM SPA',
                'rut' => '76498260-6',
                'city' => 'SANTIAGO',
                'commune' => 'VITACURA',
                'address' => 'AV. PRESIDENTE KENNEDY #7900 OF. #001',
                'activity' => 'VENTA AL POR MAYOR DE PRODUCTOS TEXTILES  PRENDAS DE VESTIR Y CALZADO',
                'acteco' => '464100',
                'sovos_user' => 'usr_764982606',
                'sovos_password' => 'abc123',
                'server' => 'igs',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'ARTICULOS DEPORTIVOS BELSPORT SPA',
                'rut' => '79950280-1',
                'city' => 'SANTIAGO',
                'commune' => 'VITACURA',
                'address' => 'AV. PRESIDENTE KENNEDY #7900 OF. #001',
                'activity' => 'VENTA AL POR MENOR DE COMPUTADORES  EQUIPO PERIFERICO PROGRAMAS INFORMATICOS Y EQUIPO DE TELECOM.',
                'acteco' => '474100',
                'sovos_user' => 'adm_bsp',
                'sovos_password' => 'abc123',
                'server' => 'bels',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'COMERCIALIZADORA RACINGLAB SPA',
                'rut' => '76411973-8',
                'city' => 'SANTIAGO',
                'commune' => 'VITACURA',
                'address' => 'AV. PRESIDENTE KENNEDY #7900 OF. #001',
                'activity' => 'VENTA AL POR MAYOR DE PRODUCTOS TEXTILES  PRENDAS DE VESTIR Y CALZADO',
                'acteco' => '464100',
                'sovos_user' => 'usr_764119738',
                'sovos_password' => 'abc123',
                'server' => 'igs',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'OCTAEDRO SPA',
                'rut' => '76296188-1',
                'city' => 'SANTIAGO',
                'commune' => 'VITACURA',
                'address' => 'AV. PRESIDENTE KENNEDY #7900 OF. #001',
                'activity' => 'VENTA AL POR MAYOR DE PRODUCTOS TEXTILES  PRENDAS DE VESTIR Y CALZADO',
                'acteco' => '464100',
                'sovos_user' => 'usr_762961881',
                'sovos_password' => 'abc123',
                'server' => 'igs',
                'created_at' => '2023-01-01 00:00:00',
                'updated_at' => '2023-01-01 00:00:00',
            ),
        ));
        
        
    }
}