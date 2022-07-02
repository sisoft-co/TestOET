<?php

use Faker\Generator as Faker;

$factory->define(App\Empresa::class, function (Faker $faker) {
//$faker->addProvider(new Faker\Provider\es_ES\Person($faker));

    return [
        'nit' => $faker->randomNumber($nbDigits = 9, $strict = true),
        'nombre' => $faker->company,
        'contacto' => $faker->firstName,
        'direccion' => $faker->streetAddress,
        'telefono1' => $faker->tollFreePhoneNumber,
        'telefono2' => $faker->tollFreephoneNumber,
        'email' => $faker->safeEmail,
    ];
});
