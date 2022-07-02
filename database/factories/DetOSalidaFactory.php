<?php

use Faker\Generator as Faker;
use App\OSalida;
use App\Dobl;
use App\User;

$factory->define(App\DetalleOSalida::class, function (Faker $faker) {

    //$osalidas = OSalida::select('id', 'nit_empresa')->get();
    $osalidas = OSalida::all()->pluck('id', 'nit_empresa')->toArray();
    $dobls = Dobl::all()->pluck('dobl')->toArray();
    $usuarios = User::all()->pluck('id')->toArray();
    
    return [
        'id_osalida' => $faker->randomElement($osalidas, 'id'),
        'nit_empresa' =>$osalidas->nit_empresa,
       // 'nit_empresa' =>$faker->randomElement($osalidas, 'nit_empresa', 'id'),
        'id_dobl' => $faker->randomElement($dobls),
        'id_user' => $faker->randomElement($usuarios),

        'fsalida' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'producto' => $faker->catchPhrase,
        'saldoinicio' => $faker->NumberBetween($min = 1, $max = 300),
        'cantretirada' => $faker->NumberBetween($min = 1, $max = 300),
        'saldofin' => $faker->NumberBetween($min = 1, $max = 300),
        'empaque' => $faker->randomElement($array = array ('CAJAS','BIDONES','BOLSAS', 'PALLETS', 'PIPAS', 'BOLSONES', 'BULTOS')),
        'pbruto' => $faker->NumberBetween($min = 50, $max = 500),
        'pneto' => $faker->NumberBetween($min = 50, $max = 500),
        'nliquidacion' => $faker->randomNumber(2),
    ];
});
