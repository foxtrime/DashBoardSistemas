<?php
use App\helpers\geral;
use App\Models\Paciente;
use App\Models\Cid;

use Jarouche\ViaCEP\HelperViaCep;

use Faker\Generator as Faker;

$factory->define(App\Models\Paciente::class, function (Faker $faker) {

	$faker = (new \Faker\Factory())::create('pt_BR');
	  
	/* //origem
	$lista 		= pegaValorEnum('pacientes','origem');
	$origem 	= $lista[array_rand($lista,1)];
	
	//base
	$lista 		= pegaValorEnum('pacientes','base');
	$base 		= $lista[array_rand($lista,1)];
	 */
	//situacao
	//$lista 		= pegaValorEnum('pacientes','situacao');
	//$situacao 	= $lista[array_rand($lista,1)];


	//AGM-114-R9X 

	$obito 		= null;
	$internado 	= false;

/* 	switch ($situacao) {
		case 'ÓBITO':
			$obito 		= $faker->dateTimeBetween("-1 years", "now")->format('Y-m-d');
			break;
		
		case 'INTERNAÇÃO':
			$internado 	= true;
			break;
	}

	$cid_id	= Cid::all()->random(); */

	//26550-001 a 26599-999

	$a= true;
	while ($a ) {
		$c1 = rand ( 26550 , 26599 ) ;
		$c2 = str_pad(rand ( 1 , 9 )*100 , 3 , '0');
		$cep = $c1.'-'.$c2;
		//Utilizando via Classe
		$class = new Jarouche\ViaCEP\BuscaViaCEPJSONP();
		/*como é JSONP, existe a opção de setar o nome da callback function, 
		* ESTÁ OPÇÃO ESTÁ SOMENTE DISPONÍVEL SE UTILIZAR A CLASSE Jarouche\ViaCEP\BuscaViaCEPJSONP();
		*/
		$class->setCallbackFunction('teste_teste');
		
		//Faz o retorno do CEP
		$result = $class->retornaCEP($cep);
		# code...
	
		print_r($cep."\n");

		if(! isset($result['erro'])){
			$a = false;
		}
		
	}

	print_r($result);
	//print_r($result['bairro']);
	//echo ($cep);
	//echo $class->retornaConteudoRequisicao();
	//print_r($result);

	//exit;


	/* $lista_bairros = ['ALTO URUGUAI','CENTRO','CHATUBA','COREIA','SANTA TERESINHA','COSMORAMA','CRUZEIRO DO SUL','EDSON PASSOS',
						'PRESIDENTE JUSCELINO','VILA EMIL','BAIRRO INDUSTRIAL','BANCO DE AREIA','BNH','JACUTINGA','ROCHA SOBRINHO',
						'SANTO ELIAS','VILA NORMA'];
	$bairro 		= $lista_bairros[array_rand($lista_bairros,1)];
 */

	return [

		'prontuario'		=> $faker->randomNumber(5),
        'dt_cadastro'       => $faker->dateTimeBetween("-3 years", "-2 years")->format('Y-m-d'),
    //    'origem'			=> $origem,
    //    'base'				=> $base,
        'nome'              => $faker->name,
        'nascimento'        => $faker->dateTimeBetween("-90 years", "-18 years")->format('Y-m-d'),
        'sus'				=> $faker->regexify('[0-9]{3}\.[0-9]{3}\.[0-9]{3}\.[0-9]{3}\.[0-9]{3}'),
		'bairro'			=> $result['bairro'],
		'logradouro'   		=> $result['logradouro'],
		'numero'   			=> $faker->randomNumber(5),
		'complemento'  		=> $faker->secondaryAddress,
		'cep'          		=> $result['cep'],
        'telefone1'         => $faker->regexify('9[0-9]{4}[0-9]{4}'),
        'telefone2'         => $faker->regexify('9[0-9]{4}[0-9]{4}'),
        'telefone3'			=> $faker->regexify('9[0-9]{4}[0-9]{4}'),
        'internado'			=> $internado,
        'obito'				=> $obito,
        'observacao'		=> $faker->text($maxNbChars = 300), 
        //'situacao'			=> $situacao,
        //'situacao_obs'		=> $faker->text($maxNbChars = 300), 
        //'cid_id'			=> $cid_id,

		
		
 	];
});
