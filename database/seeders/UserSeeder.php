<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'Administrador';
        $user->email = 'dev@ynk.cl';
        $user->password = Hash::make('WbO7LV5W3ac=');
        $user->save();
        $user->assignRole($user->name);
    }
}
