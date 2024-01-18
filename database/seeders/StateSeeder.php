<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $state = new State;
        $state->name = 'Región de Tarapacá';
        $state->number = 'I';
        $state->save();

        $state = new State;
        $state->name = 'Región de Antofagasta';
        $state->number = 'II';
        $state->save();

        $state = new State;
        $state->name = 'Región de Atacama';
        $state->number = 'III';
        $state->save();

        $state = new State;
        $state->name = 'Región de Coquimbo';
        $state->number = 'IV';
        $state->save();

        $state = new State;
        $state->name = 'Región de Valparaíso';
        $state->number = 'V';
        $state->save();

        $state = new State;
        $state->name = 'Región del Libertador General Bernardo O’Higgins';
        $state->number = 'VI';
        $state->save();

        $state = new State;
        $state->name = 'Región del Maule';
        $state->number = 'VII';
        $state->save();

        $state = new State;
        $state->name = 'Región del Biobío';
        $state->number = 'VIII';
        $state->save();

        $state = new State;
        $state->name = 'Región de La Araucanía';
        $state->number = 'IX';
        $state->save();

        $state = new State;
        $state->name = 'Región de Los Lagos';
        $state->number = 'X';
        $state->save();

        $state = new State;
        $state->name = 'Región Aysén del General Carlos Ibáñez del Campo';
        $state->number = 'XI';
        $state->save();

        $state = new State;
        $state->name = 'Región de Magallanes y Antártica Chilena';
        $state->number = 'XII';
        $state->save();

        $state = new State;
        $state->name = 'Región Metropolitana de Santiago';
        $state->number = 'RM';
        $state->save();

        $state = new State;
        $state->name = 'Región de Los Ríos';
        $state->number = 'XIV';
        $state->save();

        $state = new State;
        $state->name = 'Región de Arica y Parinacota';
        $state->number = 'XV';
        $state->save();

        $state = new State;
        $state->name = 'Región de Ñuble';
        $state->number = 'XVI';
        $state->save();
        
    }
}
