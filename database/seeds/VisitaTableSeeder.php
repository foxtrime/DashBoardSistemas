<?php

use Illuminate\Database\Seeder;
use App\helpers\geral;
use App\Models\Acompanhamento;
use App\Models\Servidor;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;

use Faker\Generator as Faker;


class VisitaTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		$faker = (new \Faker\Factory())::create('pt_BR');


		for ($i=0; $i < 50; $i++) { 
		
			print_r($i."\n");
		
			$cid		= Cid::all()->take(5)->random();

			//origem
			$lista 		= pegaValorEnum('acompanhamentos','origem');
			$origem 	= $lista[array_rand($lista,1)];
			
			//base
			$lista 		= pegaValorEnum('acompanhamentos','base');
			$base 		= $lista[array_rand($lista,1)];
			
			//atendimento_domiciliar
			$lista 					= pegaValorEnum('acompanhamentos','atendimento_domiciliar');
			$atendimento_domiciliar = $lista[array_rand($lista,1)];
			
			//motivo
			$lista 				= pegaValorEnum('visitas','motivo');
			$motivo 		= $lista[array_rand($lista,1)];

			$paciente			= Paciente::all()->random();
			$acompanhamento		= Acompanhamento::where('paciente_id', $paciente->id)->orderBy('id', 'desc')->first();

			$servidor			= Servidor::all()->random();

			//situacao
			$lista 				= pegaValorEnum('visitas','situacao');
			$situacao 			= $lista[array_rand($lista,1)];

			
			$qtd = rand(1,3) ;
			$dt = $faker->dateTimeBetween('-120 days', '-14 days')->format('Y-m-d');
			$dt_visita = new DateTime($dt);

			/* if(! isset($acompanhamento) || $acompanhamento->ativo == false ){
				$acompanhamento = new Acompanhamento;
				$acompanhamento->atendimento_domiciliar = $atendimento_domiciliar;
				$acompanhamento->origem 				= $origem;
				$acompanhamento->base 					= $base;
				$acompanhamento->situacao 				= 'ATENDIMENTO';
				$acompanhamento->paciente_id			= $paciente->id;
				$acompanhamento->cid_id					= $cid->id;
				$acompanhamento->dt_inicio				= $dt_visita;
				$acompanhamento->save();
			} */


			if(isset($acompanhamento) && $acompanhamento->ativo == true ){
				$acompanhamento->ativo 		= false;
				$acompanhamento->situacao 	= "ALTA";
				$acompanhamento->dt_termino = date ("Y-m-d" , strtotime("-1 days") );
				$acompanhamento->save();

			}

			$acompanhamento = new Acompanhamento;
			$acompanhamento->atendimento_domiciliar = $atendimento_domiciliar;
			$acompanhamento->origem 				= $origem;
			$acompanhamento->base 					= $base;
			$acompanhamento->medicina				= $faker->randomElement([0,1]);
			$acompanhamento->enfermagem				= $faker->randomElement([0,1]);
			$acompanhamento->fisioterapia			= $faker->randomElement([0,1]);
			$acompanhamento->fonoaudiologia			= $faker->randomElement([0,1]);
			$acompanhamento->nutricao				= $faker->randomElement([0,1]);
			$acompanhamento->psicologia				= $faker->randomElement([0,1]);
			$acompanhamento->odontologia			= $faker->randomElement([0,1]);
			$acompanhamento->servico_social			= $faker->randomElement([0,1]);
			
			$acompanhamento->paciente_id			= $paciente->id;
			$acompanhamento->cid_id					= $cid->id;
			$acompanhamento->dt_inicio				= $dt_visita;
			$acompanhamento->save();

			$paciente->situacao 	= "ATENDIMENTO";
			$paciente->save();
			


			
			//print_r($acompanhamento."\n");

			

			for ($z=0; $z < $qtd; $z++) { 

				$visita = new Visita;
				$visita->acompanhamento_id	= $acompanhamento->id;
				$visita->motivo 		= $motivo;
				$visita->situacao 		= $situacao;
				$visita->dt_visita 			= $dt_visita;
				
				$visita->save();
				$visita->servidores()->attach($servidor->id);

				

				date_add($dt_visita, date_interval_create_from_date_string('7 days'));
			};
		}
	}
}
