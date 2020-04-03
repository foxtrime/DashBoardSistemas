<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


use App\Models\Funcionario;
use App\Models\Role;
use App\Models\User;

use App\Models\Acompanhamento;
use App\Models\Servidor;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;

use Carbon\Carbon;
use Datatables;


class MelhorEmCasaController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
 	public function __construct()
	{
		
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		
		$pacientes 						= Paciente::all();


		// PEGA OS ENDEREÇOS E CONCATENA COM A URL
		// $pacientesa = [];
		// foreach($pacientes as $paciente){
		// 	$rua = $paciente->logradouro;
		// 	$bairro = $paciente->bairro;
		// 	$b = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$rua . $bairro.'&key=AIzaSyD88keSNZva3fJ2F01M6YOw78uf3xrtU1I';
		// 	array_push($pacientesa, $b);
		// }
		// dd($pacientesa);
		
		
		$qtd_pacientes 					= count($pacientes);
		$qtd_pacientes_acompanhamento 	= Acompanhamento::where('ativo', true)->count();

		$qtd_pjuds = 10;
		$qtd_padms = 20;



		/* CRIA AS VARIÁVEIS DE DATA */
		
		$ini_mes_ant  = date("Y-m-d", strtotime('first day of last month'));
		$fim_mes_ant  = date("Y-m-d", strtotime('last day of last month'));// date("Y-m-d", strtotime('last day of this month')); 

		$ini_mes_atual = date("Y-m-d", strtotime('first day of this month'));
		$fim_mes_atual = date("Y-m-d", strtotime('last day of this month'));// date("Y-m-d", strtotime('last day of this month')); 

		/* OBTEM AS VISITAS DO MES PASSADO */
		$visitas_MP = DB::table('visitas')->where('dt_visita', '>=' ,$ini_mes_ant)
										->where('dt_visita', '<=' ,$fim_mes_ant)->get(); 

		/* OBTEM AS VISITAS DO MES ATUAL */
		$visitas_MA = DB::table('visitas')->where('dt_visita', '>=' ,$ini_mes_atual)
										->where('dt_visita', '<=' ,$fim_mes_atual)->get(); 


		/* POPULA A QUANTIDADE E TOTAL DE VISITAS DO MÊS PASSADO */
		$qtd_visitas_MP = 0; 
		foreach($visitas_MP as $a)
		{
			$qtd_visitas_MP++;
		}
	
		/* POPULA A QUANTIDADE E TOTAL DE VISITAS DO MES ATUAL */
		$qtd_visitas_MA = 0; 
		foreach($visitas_MA as $a)
		{
			$qtd_visitas_MA++;
		}

		$vetor['qtd_visitas_MP'] = $qtd_visitas_MP;
		
		$vetor['qtd_visitas_MA'] = $qtd_visitas_MA;
		
		$V1_qtd = $qtd_visitas_MP;
		$V2_qtd = $qtd_visitas_MA;

		
		// se o não existir visitas
		if ($V1_qtd == 0  || $V2_qtd == 0 )
		{
			$vetor['percvisitas_mes'] = "100"; 
		}else{
			$vetor['percvisitas_mes'] = round((( $V2_qtd - $V1_qtd ) / $V1_qtd * 100),2);
		}
		

        $total = $qtd_pacientes;

		

		$gbairro = 
			DB::table('pacientes')
				->select(DB::raw("bairro, count(*) as qtd, round(count(*) * $total / 100 , 2) as percent"))
				->groupBy('bairro')
				->get();

		$gcid = 
			DB::table('acompanhamentos')
				->join('cids', 'acompanhamentos.cid_id', '=', 'cids.id')
				->select(DB::raw("acompanhamentos.cid_id, cids.codigo,  cids.descricao, count(*) as qtd"))
				->groupBy('acompanhamentos.cid_id')
				->orderBy('qtd','DESC')
				->take(10)
				->get();
        
		//dd($gcid);
			

		$visitas = [];

		$visitas  = Visita::select(
								DB::raw('count(*) quantidade '),
								DB::raw("DATE_FORMAT(dt_visita,'%Y/%m') as mes")
							)
								
					->where('dt_visita', '>=' , Carbon::now()->subMonths(24)->startOfMonth() )
					->groupBy('mes')
					->orderBy('mes')
					->get();

		
		//dd($visitas);


		
		return view('melhoremcasa.home', compact('qtd_pacientes', 'qtd_pacientes_acompanhamento','vetor','gbairro','gcid','pacientes','visitas'));
			}


}
