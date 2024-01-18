<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ext_customer;

class ext_customersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ext_customer = new ext_customer;
        $ext_customer->rut = '17767634-9';
        $ext_customer->full_name = 'Cristopher Fuentealba';
        $ext_customer->phone = '99999999';
        $ext_customer->email = 'cristopher.fuentealba@ynk.cl';
        $ext_customer->save();
    }
}
