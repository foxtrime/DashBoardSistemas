<?php
use App\helpers\geral;
use App\Models\Servidor;

use Faker\Generator as Faker;

$factory->define(App\Models\Servidor::class, function (Faker $faker) {

	$faker = (new \Faker\Factory())::create('pt_BR');
	  
	//motivo
	$lista 			= pegaValorEnum('servidores','motivo');
	$motivo 	= $lista[array_rand($lista,1)];
	
	return [

		'nome'              => $faker->name,
        'motivo'		=> $motivo,

 	];
});
