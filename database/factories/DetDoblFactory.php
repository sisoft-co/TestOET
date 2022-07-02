<?php

use Faker\Generator as Faker;
use App\Dobl;
use App\User;


$factory->define(App\DetalleDobl::class, function (Faker $faker) {
    $dobls = Dobl::all()->pluck('dobl')->toArray();
    $usuarios = User::all()->pluck('id')->toArray();
    return [
        'dobl_dobls' => $faker->randomElement($dobls),
        'numcontenedor' => $faker->regexify('[A-Z]{3}[0-9]{6}'),
        'id_user' => $faker->randomElement($usuarios),

        'tipocontenedor' => $faker->randomElement($array = array ('20 P.','40 P.','C. S.')),
        'lote' => $faker->randomNumber(6),
        'puertoretiro' => $faker->randomElement($array = array ('PTO. AGUA DULCE','PTO. BVENTURA','PTO. NUEVO', 'PTO. ESTERO', 'PTO.SOLO')),
        'puertoentrega' => $faker->randomElement($array = array ('PTO. AGUA DULCE','PTO. BVENTURA','PTO. NUEVO', 'PTO. ESTERO', 'PTO.SOLO')),
        'detobservacion' => $faker->text($maxNbChars = 250),
    ];
});
