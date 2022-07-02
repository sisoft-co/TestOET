<?php

use Faker\Generator as Faker;
use App\User;
use App\Empresa;


$factory->define(App\OSalida::class, function (Faker $faker) {

    $usuarios = User::all()->pluck('id')->toArray();
    $empresas = Empresa::all()->pluck('nit')->toArray();
    
    return [
        //'id' ------- No necesita porque es Auto-Generado
        // Los siguientes campos contienen Referencias Integrales por cumplir.
        'nit_empresa' => $faker->randomElement($empresas),
        'id_user' => $faker->randomElement($usuarios),

        'fsalida' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'ciudestino' => $faker->city,
        'ocarcliente' => $faker->numerify('O.C. ######'),
        'cchofer' => $faker->numerify('C.C. ###.###'),
        'nomchofer' => $faker->name($gender = 'male'),
        'vehplaca' => $faker->regexify('[A-Z]{3}[0-9]{3}'),
        'observacion' => $faker->text($maxNbChars = 250),
    ];
});
