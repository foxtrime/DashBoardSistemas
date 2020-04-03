<?php
use App\helpers\geral;
use App\Models\Base;
use App\Models\Abastecimento;
use App\Models\Posto;
use App\Models\Veiculo;
use App\Models\Secretaria;

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {

    $faker = (new \Faker\Factory())::create('pt_BR');
   // $role_id  = App\Models\Role::all()->random()->id;
   
 
    return [
        'name'              => $faker->name,
        'email'             => $faker->unique()->safeEmail,
        'status' 		    => $faker->randomElement(['Ativo','Inativo']),
        'password'          => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'avatar' 		    => $faker->imageUrl(120, 150, 'people', true, 'Faker'),
        'remember_token'    => str_random(10),
		'cpf'           	=> $faker->cpf,
        //'role_id'			=> $role_id,
        'motorista'         => $faker-> boolean($chanceOfGettingTrue = 30),
        'secretaria_id'     => Secretaria::all()->random()->id,

        'celular'           => $faker->regexify('9[0-9]{4}[0-9]{4}'),   
        'cnh'               => $faker->regexify('[0-9]{11}'),   
        'categoria_cnh'     => $faker->regexify('[A-D]{1}'),   
        'validade_cnh'      => $faker->dateTimeBetween($startDate = '-1 years', $endDate = '+1 years', $timezone = null) 
    ];
});
