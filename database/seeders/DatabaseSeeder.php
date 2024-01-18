<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Region;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            StationSeeder::class,
            CompaniesTableSeeder::class,
            //EcommercesTableSeeder::class,
            OrderStatusesTableSeeder::class,
            ext_customersSeeder::class,
            MvAccessSeeder::class,
            MvMarketplaceSeeder::class,
            MvPaymentSeeder::class,
            MvStoresSeeder::class,
            MvWarehouseSeeder::class
        ]);
    }
}
