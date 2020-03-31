<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Funcionario;
use App\Models\Role;
use App\Models\User;

use App\Models\Acompanhamento;
use App\Models\Servidor;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;

use Carbon\Carbon;
use Yajra\DataTables\DataTables;


class IndicadorController extends Controller
{
/*     public function __construct()
	{
		
		$this->middleware('auth');
	}
 */
	public function index()
	{
        //$menor_visita   = Visita::min('dt_visita');
        $menor_visita   = "2020-02-01";Visita::min('dt_visita');
        $maior_visita   = Visita::max('dt_visita');

        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        $datas =   DB::table('acompanhamentos')
        ->select(DB::raw("	distinct(DATE_FORMAT(dt_inicio, '%m%Y')) as periodo, 
                                    ELT(MONTH(dt_inicio), 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 
                                    'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro') as mes, 
                                    DATE_FORMAT(dt_inicio, '%Y') as ano"
                            )
                    )->where('dt_inicio' ,'!=' ,null)->orderBy('dt_inicio')->get();


        //dd($datas);
        //dd($menor_visita .' - '.$maior_visita);
			
		
        return view('indicador.index'
            ,compact('datas')
        );
    }
    

    public function permanencia($periodo)
	{
		// Criar o objeto da coleção que será usado para o dataTables
		$colecao    = collect();
        $total_dias = 0;

        $mes        = substr($periodo, 0, 2);
        $ano        = substr($periodo, 2, 4);


        $acompanhamentos    = Acompanhamento::whereRaw("year(dt_termino) = ".$ano)
                                            ->whereRaw("month(dt_termino) = ".$mes)
                                            ->where("ativo", "=", "0")
                                            ->get();
		
        foreach($acompanhamentos as $acompanhamento)
		{
            $total_dias = $total_dias + diasEntreDatas( $acompanhamento->dt_termino, $acompanhamento->dt_inicio );
        }
        
        $total_alta = count($acompanhamentos);
        if($total_alta > 0){
            $media      = round($total_dias / $total_alta , 2);
        }else{
            $media      = 0;
        }


        $colecao->push([
            'mes'				=> $mes,
            'ano'				=> $ano,
            'total_dias' 	    => $total_dias,
            'total_alta'		=> $total_alta,
            'media'				=> $media
        ]);

        // Retornar a tabela pronta

        return Datatables::of($colecao)
            ->make(true);

        //return response()->json($acompanhamentos, 200);

    }


    public function admissao($periodo)
	{
		// Criar o objeto da coleção que será usado para o dataTables
		$colecao    = collect();
        $total_dias = 0;

        $mes        = substr($periodo, 0, 2);
        $ano        = substr($periodo, 2, 4);

        $admitidos          = Acompanhamento::whereRaw("year(dt_inicio) = ".$ano)
                                            ->whereRaw("month(dt_inicio) = ".$mes)
                                            ->get();
        
		$total_ad1 = 0;
		$total_ad2 = 0;
        $total_ad3 = 0;
        $sem_classificacao = 0;
        foreach($admitidos as $admitido)
		{
            switch ($admitido->ad_admissao) {
                case 'AD1':
                    $total_ad1 ++;        
                    break;

                case 'AD2':
                    $total_ad2 ++;        
                    break;

                case 'AD3':
                    $total_ad3 ++;        
                    break;
                
                default:
                    $sem_classificacao ++;
                    break;
            }

        }

        $total_admitidos = $total_ad1 + $total_ad2 + $total_ad3 + $sem_classificacao;

        if($total_ad1 > 0 ){
            $percentual_ad1_admissao = round( ( $total_ad1 * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_ad1_admissao = 0;
        }

        if($total_ad2 > 0 ){
            $percentual_ad2_admissao = round( ( $total_ad2 * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_ad2_admissao = 0;
        }

        if($total_ad3 > 0 ){
            $percentual_ad3_admissao = round( ( $total_ad3 * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_ad3_admissao = 0;
        }

        $colecao->push([
            'mes'				    => $mes,
            'ano'				    => $ano,
            'total_admitidos'	    => $total_admitidos,
            'total_ad1' 	        => $total_ad1,
            'total_ad2'		        => $total_ad2,
            'total_ad3'			    => $total_ad3,
            'percentual_admissao'   => "$percentual_ad1_admissao% + $percentual_ad2_admissao% + $percentual_ad3_admissao%",
        ]);

        // Retornar a tabela pronta

        return Datatables::of($colecao)
            ->make(true);

        //return response()->json($acompanhamentos, 200);

    }


    public function origem($periodo)
	{
		// Criar o objeto da coleção que será usado para o dataTables
		$colecao    = collect();
        $total_dias = 0;

        $mes        = substr($periodo, 0, 2);
        $ano        = substr($periodo, 2, 4);

        $admitidos          = Acompanhamento::whereRaw("year(dt_inicio) = ".$ano)
                                            ->whereRaw("month(dt_inicio) = ".$mes)
                                            ->get();
   

        $basica = 0;
        $hospital = 0;
        $emergencia = 0;
        $espontanea = 0;
        $sem_classificacao = 0;

        //'HOSPITAL','EMERGÊNCIA','ESPONTÂNEA','ATENÇÃO BÁSICA'
        foreach($admitidos as $admitido)
		{
            switch ($admitido->origem) {
                case 'ATENÇÃO BÁSICA':
                    $basica ++;        
                    break;

                case 'HOSPITAL':
                    $hospital ++;        
                    break;

                case 'EMERGÊNCIA':
                    $emergencia ++;        
                    break;

                case 'ESPONTÂNEA':
                    $espontanea ++;        
                    break;
                
                
                default:
                    $sem_classificacao ++;
                    break;
            }

        }

        $total_admitidos = $basica + $hospital + $emergencia + $espontanea + $sem_classificacao;

        if($basica > 0 ){
            $percentual_basica = round( ( $basica * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_basica = 0;
        }

        if($hospital > 0 ){
            $percentual_hospital = round( ( $hospital * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_hospital = 0;
        }

        if($emergencia > 0 ){
            $percentual_emergencia = round( ( $emergencia * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_emergencia = 0;
        }

        if($espontanea > 0 ){
            $percentual_espontanea = round( ( $espontanea * 100 ) / $total_admitidos , 2 );
        }else{
            $percentual_espontanea = 0;
        }


        $colecao->push([
            'mes'				    => $mes,
            'ano'				    => $ano,
            'total_admitidos'	    => $total_admitidos,
            'basica'			    => $basica,
            'hospital' 	            => $hospital,
            'emergencia'		    => $emergencia,
            'espontanea'			=> $espontanea,
            'percentual_admissao'   => "$percentual_basica% + $percentual_hospital% + $percentual_emergencia% + $percentual_espontanea%" ,
        ]);

        // Retornar a tabela pronta

        return Datatables::of($colecao)
            ->make(true);

        //return response()->json($acompanhamentos, 200);

    }

    public function obito($periodo)
	{
		// Criar o objeto da coleção que será usado para o dataTables
		$colecao    = collect();
        $total_obito = 0;

        $mes        = substr($periodo, 0, 2);
        $ano        = substr($periodo, 2, 4);

        /*         $acompanhamentos_ativos = Acompanhamento::whereRaw("year(dt_inicio) <= ".$ano)
                                        ->whereRaw("month(dt_inicio) <= ".$mes)
        
                                        ->whereRaw("year(dt_termino) >= ".$ano)
                                        ->whereRaw("month(dt_termino) >= ".$mes)
                                        ->get();
         */                                

         $acompanhamentos_ativos = Acompanhamento::where("dt_termino", "<", "$ano-$mes-31")
                                                ->orWhere("dt_termino","=", null)
                                                ->get();

         //return $acompanhamentos_ativos;

                                //$acompanhamentos_ativos     = Acompanhamento::where("ativo", "=", "1")->get();
        $total_acompanhamento       = count($acompanhamentos_ativos);


        $ac_periodo    = Acompanhamento::whereRaw("year(dt_termino) = ".$ano)
            ->whereRaw("month(dt_termino) = ".$mes)
            ->get();
        

        foreach($ac_periodo as $acompanhamento)
        {
            if($acompanhamento->paciente->obito)
                $total_obito ++;
        }

    

        if($total_obito > 0 ){
            $percentual_obito = round( ( $total_obito * 100 ) / $total_acompanhamento , 2 );
        }else{
            $percentual_obito = 0;
        }

       


        $colecao->push([
            'mes'				    => $mes,
            'ano'				    => $ano,
            'total_acompanhamento'  => $total_acompanhamento,
            'total_obito'           => $total_obito,
            'percentual_obito'      => "$percentual_obito%" ,
        ]);

        // Retornar a tabela pronta

        return Datatables::of($colecao)
            ->make(true);

        //return response()->json($acompanhamentos, 200);

    }
}
