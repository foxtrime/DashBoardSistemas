<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Posto;
use App\Models\User;
use App\Models\Abastecimento;
use Carbon\Carbon;
use Datatables;


class SgfController extends Controller
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
		
		$qtdVeiculos = DB::connection('mysql')->table('sgf.veiculos')->get();
		//$qtdVeiculos = Veiculo::on('mysql')->count();
		//dd($qtdVeiculos);

		
		//================================================== SEMANAL =====================================
		
		/* CRIA AS VARIÁVEIS DE DATA */
		$sem_passada 			= date('W')-1;
		$sem_atual 				= date('W')-0;

		$segunda_passada    	= date("Y-m-d", strtotime('monday last week'));
		$segunda_atual      	= date("Y-m-d", strtotime('monday this week'));
		$segunda_proxima    	= date("Y-m-d", strtotime('monday next week'));

		
		/* OBTEM OS ABASTECIMENTOS DA SEMANA PASSADA */
		$abastecimentos_SP = 
			DB::connection('mysql')->table('sgf.abastecimentos')->where('data', '>=' ,$segunda_passada)
												->where('data', '<=' ,$segunda_atual)->get(); 

												/* OBTEM OS ABASTECIMENTOS DA SEMANA ATUAL */
		$abastecimentos_SA = 
			DB::connection('mysql')->table('sgf.abastecimentos')->where('data', '>=' ,$segunda_atual)
												->where('data', '<=' ,$segunda_proxima)->get(); 

		/* POPULA A QUANTIDADE E TOTAL DE ABASTECIMENTOS DA SEMANA PASSADA */
		$qtd_abast_SP = 0; $sum_abast_SP = 0;
		foreach($abastecimentos_SP as $a)
		{
			$qtd_abast_SP++;
			$sum_abast_SP = $sum_abast_SP + $a->valor_total;
		}
		
		/* POPULA A QUANTIDADE E TOTAL DE ABASTECIMENTOS DA SEMANA ATUAL */
		$qtd_abast_SA = 0; $sum_abast_SA = 0;
		foreach($abastecimentos_SA as $a)
		{
			$qtd_abast_SA++;
			$sum_abast_SA = $sum_abast_SA + $a->valor_total;
		}
		/* =============================================================================================== */

		$vetor['qtd_abast_SP'] = $qtd_abast_SP;
		$vetor['sum_abast_SP'] = $sum_abast_SP;

		$vetor['qtd_abast_SA'] = $qtd_abast_SA;
		$vetor['sum_abast_SA'] = $sum_abast_SA;

		$V1_qtd = $qtd_abast_SP;
		$V2_qtd = $qtd_abast_SA;

		$V1_sum = $sum_abast_SP;
		$V2_sum = $sum_abast_SA;
		
		
		// se o não existir abastecimentos
		if ($V1_qtd == 0  || $V2_qtd == 0 )
		{
			$vetor['perc_qtd_abast_sem'] = "?"; 
			$vetor['perc_sum_abast_sem'] = "?"; 
		}else{
			$vetor['perc_qtd_abast_sem'] = round((( $V2_qtd - $V1_qtd ) / $V1_qtd * 100),3);
			$vetor['perc_sum_abast_sem'] = round((( $V2_sum - $V1_sum ) / $V1_sum * 100),3);
		}

		//================================================== MENSAL =====================================

		/* CRIA AS VARIÁVEIS DE DATA */
		
		$ini_mes_ant  = date("Y-m-d", strtotime('first day of last month'));
		$fim_mes_ant  = date("Y-m-d", strtotime('last day of last month'));// date("Y-m-d", strtotime('last day of this month')); 

		$ini_mes_atual = date("Y-m-d", strtotime('first day of this month'));
		$fim_mes_atual = date("Y-m-d", strtotime('last day of this month'));// date("Y-m-d", strtotime('last day of this month')); 

		/* OBTEM OS ABASTECIMENTOS DO MES PASSADO */
		$abastecimentos_MP = DB::connection('mysql')->table('sgf.abastecimentos')->where('data', '>=' ,$ini_mes_ant)
										->where('data', '<=' ,$fim_mes_ant)->get(); 

		/* OBTEM OS ABASTECIMENTOS DO MES ATUAL */
		$abastecimentos_MA = DB::connection('mysql')->table('sgf.abastecimentos')->where('data', '>=' ,$ini_mes_atual)
										->where('data', '<=' ,$fim_mes_atual)->get(); 


		/* POPULA A QUANTIDADE E TOTAL DE ABASTECIMENTOS DO MÊS PASSADO */
		$qtd_abast_MP = 0; $sum_abast_MP = 0;
		foreach($abastecimentos_MP as $a)
		{
			$qtd_abast_MP++;
			$sum_abast_MP = $sum_abast_MP + $a->valor_total;
		}
	
		/* POPULA A QUANTIDADE E TOTAL DE ABASTECIMENTOS DO MES ATUAL */
		$qtd_abast_MA = 0; $sum_abast_MA = 0;
		foreach($abastecimentos_MA as $a)
		{
			$qtd_abast_MA++;
			$sum_abast_MA = $sum_abast_MA + $a->valor_total;
		}

		$vetor['qtd_abast_MP'] = $qtd_abast_MP;
		$vetor['sum_abast_MP'] = $sum_abast_MP;
		
		$vetor['qtd_abast_MA'] = $qtd_abast_MA;
		$vetor['sum_abast_MA'] = $sum_abast_MA;
		
		$V1_qtd = $qtd_abast_MP;
		$V2_qtd = $qtd_abast_MA;

		$V1_sum = $sum_abast_MP;
		$V2_sum = $sum_abast_MA;
		
		
		// se o não existir abastecimentos
		if ($V1_qtd == 0  || $V2_qtd == 0 )
		{
			$vetor['perc_qtd_abast_mes'] = "?"; 
			$vetor['perc_sum_abast_mes'] = "?"; 
		}else{
			$vetor['perc_qtd_abast_mes'] = round((( $V2_qtd - $V1_qtd ) / $V1_qtd * 100),3);
			$vetor['perc_sum_abast_mes'] = round((( $V2_sum - $V1_sum ) / $V1_sum * 100),3);
		}
		
		//dd($vetor);
		



		//====================== GRAFICO GASTO ANUAL =====================================
		$ini_ano_atual = date("Y-m-d", strtotime('first day of last year'));
		$fim_ano_atual = date("Y-m-d", strtotime('first day of next year'));// date("Y-m-d", strtotime('last day of this month')); 

		

		$ano = Carbon::now()->year;
		$data_inicio   = Carbon::createFromFormat('Y-m-d H:i:s', $ano.'-01-01 00:00:00');
		$data_fim      = Carbon::createFromFormat('Y-m-d H:i:s', $ano.'-12-31 23:59:59');

		//===================================================================================================

		$valor_total_mensal = [];
	
		$valor_total_mensal  = Abastecimento::select(
								DB::raw('round( sum(valor_total) ,3 ) as total'), 
								DB::raw("DATE_FORMAT(data,'%m/%y') as mes")
				  )
				  ->where('data', '>=' , Carbon::now()->subMonths(24)->startOfMonth() )
				  ->groupBy('mes')
				  ->orderBy('data')
				  ->get();

		$abastecimentos = [];

		$abastecimentos  = Abastecimento::select(
								DB::raw('count(*) quantidade '),
								DB::raw("DATE_FORMAT(data,'%X/%v') as mes")
							)
								
					->where('data', '>=' , Carbon::now()->subMonths(24)->startOfMonth() )
					->groupBy('mes')
					->orderBy('mes')
					->get();

		
		return view('sgf.home', compact('qtdVeiculos','vetor','valor_total_mensal','abastecimentos'));
	}




}
