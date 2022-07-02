<?php

use Faker\Generator as Faker;
use App\Empresa;
use App\User;

$factory->define(App\Dobl::class, function (Faker $faker) {

    $empresas = Empresa::all()->pluck('nit')->toArray();
    $usuarios = User::all()->pluck('id')->toArray();
    $tmp_fingreso = $faker->date($format = 'Y-m-d', $max = 'now');
    $tmp_ntpallet = $faker->NumberBetween($min = 500, $max = 5000);
    
    return [
        'dobl' => $faker->unique()->swiftBicNumber,
        // Los dos siguientes campos contienen Referencias Integrales por cumplir.
        'nit_empresa' => $faker->randomElement($empresas),
        'id_user' => $faker->randomElement($usuarios),

        'fingreso' => $tmp_fingreso, 
        'producto' => $faker->catchPhrase,
        'ntpallet' => $tmp_ntpallet,
        'saldofin' => $tmp_ntpallet,
        'sliqfin' => $tmp_ntpallet,
        'fliqfin' => $tmp_fingreso,
        'pnrecibido' => $faker->NumberBetween($min = 100, $max = 500),
        'diasnocobro' => $faker->NumberBetween($min = 0, $max = 30),
        'vlrxpallet' => $faker->randomNumber(5),
        'observacion' => $faker->text($maxNbChars = 250),
    ];

});
