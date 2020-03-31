<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
use App\Imports\PacientesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Acompanhamento;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;
use App\Models\User;

class PacienteController extends Controller
{

    public function acompanhamentos(Paciente $paciente)
    {
         
        $acompanhamentos = Acompanhamento::where('paciente_id', $paciente->id)->with('paciente','visitas','cid','ultima_visita')->orderBy('id', 'ASC')->get();
       

        //dd($acompanhamentos);
        //dd($acompanhamentos[0]);

        return view ('paciente.acompanhamentos', compact('acompanhamentos','paciente','visitas','cid'));    
    }

    public function visitas(Acompanhamento $acompanhamento)
    {
        $visitas  = Visita::where('acompanhamento_id', $acompanhamento->id)->with('servidores','acompanhamento')->orderBy('id', 'ASC')->get();
        //dd($visitas);

        if( isset($visitas[0]) ){
            $base       = $visitas[0]->acompanhamento->base;
            $situacao   = $visitas[0]->acompanhamento->situacao;
            $diasAtd    = diasEntreDatas( $acompanhamento->dt_termino, $acompanhamento->dt_inicio );
        }else{
            $base       = '---';
            $situacao   = '---';
            $diasAtd    = '---';
        }

        //dd($visitas);
        //dd( $acompanhamento->paciente->nascimento );
        return view ('paciente.visitas', compact('acompanhamento','visitas','cid','diasAtd','base','situacao'));    
    }

    public function index()
    {
        $pacientes = Paciente::with('acompanhamentos')->get();
        return view ('paciente.index', compact('pacientes'));    
    }

    public function create()
    {
        $titulo = 'Novo Paciente';
        $sexos   = pegaValorEnum('pacientes', 'sexo',true);

        //gera novo numero de prontuário
        $novo_prontuario    = Paciente::max('prontuario');
        $novo_prontuario ++;
        $continua = true;
        
        //dd($novo_prontuario);
        while ($continua == true) {
            $paciente = Paciente::where('prontuario', $novo_prontuario)->first();
            if($paciente){
                $novo_prontuario ++;
            }else{
                $continua = false;
            }
        }
		return view ('paciente.create', compact('titulo','novo_prontuario','sexos'));
    }

    public function store(Request $request)
    {
     
        $date       =   $request->nascimento;
        $arr        =   explode('/',$date);
        $newDate    =   "$arr[2]".'-'."$arr[1]".'-'."$arr[0]";
        $request->merge(['nascimento' => date("Y-m-d", strtotime($newDate) )]);

        //gera novo numero de prontuário
        $novo_prontuario    = Paciente::withTrashed()->max('prontuario');
        $novo_prontuario ++;
        
        //dd($novo_prontuario);
        
        $request->merge(['prontuario' => $novo_prontuario ]);
        
        //$request->merge(['nascimento' => date("Y-m-d", strtotime($request->nascimento))]);
        
        $this->validate($request,[
            'prontuario'                => 'required|unique:pacientes',	
            'sus'                       => "min:19|max:19",
            'nome'                      => 'required|min:3|max:100',
            'nascimento'                => 'nullable|date',
            "cep" 						=> "nullable|min:10|max:10",
            'bairro'                    => 'required',
            'logradouro'                => 'required',
            'numero'                    => 'required',
            'complemento'               => 'nullable',
            'telefone1'                 => 'nullable|min:14|max:15',
            'telefone2'                 => 'nullable|min:14|max:15',
            'telefone3'                 => 'nullable|min:14|max:15',
            'observacao'                => 'nullable',

            'cpf'                       => 'required|unique:pacientes',
            'identidade'                => 'required',
            'org_exp_idt'               => 'required',
        ]);
        
        $request->merge(['bairro' => strtoupper($request->bairro) ]);

        //dd($request->all());


        DB::beginTransaction();
        try {
            $paciente = new Paciente($request->all());
            $paciente->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th);
            return back()->withInput()->with('error', 'Falha ao criar o Paciente.');    
        }

        DB::commit();
        return redirect('paciente')->with('sucesso_swal', "Paciente $paciente->nome criado com sucesso! Prontuário Nº $paciente->prontuario");
    }

    public function show(Paciente $paciente)
    {
        $titulo             = 'Visualização de Paciente';
        $idade              = \calculaIdade($paciente->nascimento, $paciente->dt_obito);
        $acompanhamentos    = Acompanhamento::with('paciente', 'visitas','cid' )->where('paciente_id', $paciente->id)->get();
		return view ('paciente.show', compact('paciente','titulo','idade','acompanhamentos'));
    }

    public function edit(Paciente $paciente)
    {
        //dd($paciente);
        $titulo = 'Edição de Paciente';

        //dd($paciente->nascimento);

        //dd( date("d/m/Y", strtotime($paciente->nascimento)) );


        if(isset($paciente->nascimento)){
            $idade  = \calculaIdade($paciente->nascimento, $paciente->dt_obito);

        }else{
            $idade = "-";
        }

        $sexos  = pegaValorEnum('pacientes', 'sexo',true);
		return view ('paciente.create', compact('paciente','titulo','idade','sexos'));
    }


    public function update(Request $request, Paciente $paciente)
    {
        $request->merge(['nascimento' => date("Y-m-d", strtotime($request->nascimento))]);

        //dd($request->all());
        $this->validate($request,[
            'prontuario'                => "required|unique:pacientes,prontuario,$paciente->id",
            'sus'                       => "min:19|max:19|unique:pacientes,sus,$paciente->id",
            'nome'                      => 'required|min:3|max:100',
            'nascimento'                => 'nullable|date',
            "cep" 						=> "nullable|min:10|max:10",
            'bairro'                    => 'required',
            'logradouro'                => 'required',
            'numero'                    => 'required',
            'complemento'               => 'nullable',
            'telefone1'                 => 'nullable|min:14|max:15',
            'telefone2'                 => 'nullable|min:14|max:15',
            'telefone3'                 => 'nullable|min:14|max:15',
            'observacao'                => 'nullable',
            'situacao_obs'              => 'nullable',

            'cpf'                       => "required|unique:pacientes,cpf,$paciente->id",
            'identidade'                => 'required',
            'org_exp_idt'               => 'required',
        ]);
        
        $request->merge(['bairro' => strtoupper($request->bairro) ]);
        //dd($request->all());


        DB::beginTransaction();
        try {
            $paciente->fill($request->all());
            $paciente->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao Editar o Paciente.');    
        }
        DB::commit();

        return redirect('paciente')->with('sucesso', 'Paciente Editado com sucesso!');
    }

    public function destroy(Paciente $paciente)
    {
        // desabilitando a exclusão
        return response('erro', 500);


        DB::beginTransaction();
        try {
            foreach ($paciente->acompanhamentos as $key => $acompanhamento) {
                $acompanhamento->visitas()->delete();
            }
        
            $paciente->acompanhamentos()->delete();
            $paciente->delete();


        } catch (\Throwable $th) {
			DB::rollBack();
            //dd($th);
			return response('erro', 500);
        }
        DB::commit();
        return response('ok', 200);
    }


    public function relatorios()
	{
		$usuario_logado  = User::find(Auth::user()->id);

        //dd($usuario_logado);

        
		$tipos			= ['COMPLETO','AGENDAMENTO','OPERACÃO','SECRETARIA','BASE','LOCADORA'];
		
        
        $bairros		= Paciente::distinct()->orderBy("bairro")->get('bairro');
        $situacoes		= Paciente::distinct()->orderBy("situacao")->get('situacao');
        $situacoes	    = DB::table('pacientes')->select('situacao')->where('situacao','!=', "")->distinct('situacao')->orderBy("situacao")->get();
      
        //dd($situacoes);
        //dd($bairros);
		//$bases       	= Base::orderBy('nome')->get();
		
		//$origens			= pegaValorEnum('veiculos','origem');
		//$locadoras     = Locadora::orderBy('razao_social')->get();
	
		
		
		return view('paciente.relatorio.escolhe',compact('tipos','bairros','situacoes'));
		//return view('paciente.relatorio.escolhe',compact('tipos','secretarias','bases','locadoras','origens','acesso'));
	}
    
    
	public function imprimeRelatorio(Request $request)
	{
        

        $hoje = Carbon::today()->toDateString();
        
        //dd( $hoje->subYears(50));
        
		//dd($request->all());
		$titulo = " Relatório geral de Pacientes";

		$sec = "";
		$select 	= "SELECT pac.nome, pac.prontuario, pac.nascimento, pac.dt_obito, pac.cpf, pac.bairro, pac.situacao";

		$from 	= " from pacientes pac ";

        switch ($request->idade) {
            case 'T':
                $where 	= 'where 1=1 ';
                break;
            
            case '1':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-61 years")) ."'";
                break;
            
            case '2':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-65 years")) ."'";
                $where 	= $where ." and  pac.nascimento < " ."'" .date('Y-m-d', strtotime("-61 years")) ."'";
                break;
            
            case '3':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-70 years")) ."'";
                $where 	= $where ." and  pac.nascimento < " ."'" .date('Y-m-d', strtotime("-66 years")) ."'";
                break;
        
            case '4':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-75 years")) ."'";
                $where 	= $where ." and  pac.nascimento < " ."'" .date('Y-m-d', strtotime("-71 years")) ."'";
                break;
        
            case '5':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-80 years")) ."'";
                $where 	= $where ." and  pac.nascimento < " ."'" .date('Y-m-d', strtotime("-76 years")) ."'";
                break;
        
            case '6':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-85 years")) ."'";
                $where 	= $where ." and  pac.nascimento < " ."'" .date('Y-m-d', strtotime("-81 years")) ."'";
                break;
        
            case '7':
                $where 	= "where pac.nascimento > " ."'" .date('Y-m-d', strtotime("-90 years")) ."'";
                $where 	= $where ." and  pac.nascimento < " ."'" .date('Y-m-d', strtotime("-86 years")) ."'";
                break;

            case '8':
                    $where 	= "where pac.nascimento <= " ."'" .date('Y-m-d', strtotime("-90 years")) ."'";
                    break;
        }
    
		if ($request->bairro != 'T') {
			$where  = $where  ."and pac.bairro = '$request->bairro'" ;
		}

		if($request->situacao != 'T')
		{		 			
			$where  =   $where ." and pac.situacao = '$request->situacao'"; 
		}

		
		


				



		switch ($request->ordem_relatorio) {
   		
			case 'NOME':
				$ordem = " order By nome";
				break;
			case 'PRONTUARIO':
				$ordem = " order By prontuario";
				break;
			case 'IDADE':
				$ordem = " order By nascimento, nome";
				break;
		};

		$sql = $select .$from .$where .$ordem;
		
		$seta = strtolower ($request->ordem_relatorio);
		//dd($sql);
		$dados = DB::select($sql);
        
		/* SOMATÓRIOS */
		$sum_qtd_pacientes = 0;
		foreach($dados as $key=>$dado){
            $sum_qtd_pacientes++;
		};
        //dd($dados[0]);

        //dd($sum_qtd_pacientes);

		return view ('paciente.relatorio.geral', compact('dados', 'titulo','sum_qtd_pacientes','seta'));

	}







  /*   public function import() 
    {
        
        $collection = Excel::toCollection(new PacientesImport, 'PACIENTES_ATIVOS_2020.xlsx');


        //dd($collection2);
        

        DB::beginTransaction();
        try {
            foreach ($collection[0] as $key => $registro) {
                
                if($key > 0 && $key < 354){
                    //dd( date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[16]), "Y-m-d") );
                    //dd($registro);
            
                    //==================================   PACIENTE

                    $paciente       = Paciente::where('nome', $registro[3])->first();
                    
                    if( ! $paciente){
                        $paciente               = new Paciente;
                    }

                    $paciente->prontuario   = $registro[0];
                    $paciente->dt_cadastro  = null;
                    $paciente->nascimento   = null;

                    if($registro[1] != null)
                        $paciente->dt_cadastro = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[1]), "Y-m-d");
                    
                    if($registro[2] != null)
                        $paciente->nascimento = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[2]), "Y-m-d");
                    
                    //print_r($nascimento . ' - ' .$key .'</br>' );

                    $paciente->nome       = $registro[3];
                    $paciente->sus        =  substr($registro[4],0,3) . '.' 
                                            .substr($registro[4],3,3) . '.' 
                                            .substr($registro[4],6,3) . '.'
                                            .substr($registro[4],9,3) . '.'
                                            .substr($registro[4],12,3);
                                            
                    $paciente->logradouro = $registro[5];
                    $paciente->bairro     = $registro[6];
                    

                    $telefones  = explode("/", $registro[9]);
                    $qtd_tel    = count($telefones);
                    $paciente->telefone1 = null;
                    $paciente->telefone2 = null;
                    $paciente->telefone3 = null;
                    switch ($qtd_tel) {
                        case '1':
                            $paciente->telefone1 = $telefones[0];
                            break;
                        
                        case '2':
                            $paciente->telefone1 = $telefones[0];
                            $paciente->telefone2 = $telefones[1];
                            break;

                        case '3':
                            $paciente->telefone1 = $telefones[0];
                            $paciente->telefone2 = $telefones[1];
                            $paciente->telefone3 = $telefones[2];
                            break;
                    }

                    $paciente->observacao       = $registro[11];
                    $paciente->situacao         = 'ATENDIMENTO';
                  
                    //ALTA
                    if( strstr(strtoupper($registro[11]), 'ALTA') === 'ALTA') {
                        $paciente->situacao = 'ALTA';
                    }

                    //SEM PERFIL
                    if( strstr(strtoupper($registro[11]), 'SEM PERFIL') === 'SEM PERFIL') {
                        $paciente->situacao = 'SEM PERFIL';
                    }

                    $paciente->save();
                    
                    $paciente_id    = $paciente->id;

                   

                    //==================================   ACOMPANHAMENTO

                    $acompanhamento = new Acompanhamento;
                    $acompanhamento->paciente_id        = $paciente->id;
                    $acompanhamento->base               = $registro[7];
                    //$acompanhamento->ativo              = 1;
                    $acompanhamento->enfermagem			= 1;
                    $acompanhamento->medicina			= 1;
                    $acompanhamento->fisioterapia		= 1;
                    $acompanhamento->fonoaudiologia		= 1;
                    $acompanhamento->nutricao			= 1;
                    $acompanhamento->psicologia			= 1;
                    $acompanhamento->odontologia		= 1;
                    $acompanhamento->servico_social		= 1;

                    $cid = Cid::where('codigo',$registro[8])->first();
                    if( $cid != null ){
                        $acompanhamento->cid_id     = $cid->id;
                    }else{
                        $acompanhamento->cid_id     = 1;
                    }

                    if($registro[10] != null)
                        $acompanhamento->dt_inicio = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[10]), "Y-m-d");

                    
                    $acompanhamento->dt_termino = null;
                    
                    
                    //$acompanhamento->situacao = $paciente->situacao;

                    $acompanhamento->observacao =   $registro[11] .
                                                     " / " .$registro[34] 
                                                    ." / Data da Vacina: " .$registro[35] 
                                                    ." / Destino Pós alta: " .$registro[36] ;
                    $acompanhamento->save();


                    ////print_r('Salvou paciente: ' .$paciente->nome . ' - ' .$key .'</br>' );
                    //==================================   VISITAS

                    $paciente = Paciente::find($paciente_id);

                    //dd($paciente);
                    if($registro[10] != null){
                        $primeira_visita = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[10]), "Y-m-d");

                        if( $primeira_visita != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id    = $acompanhamento->id;
                            $visita->dt_visita      = $primeira_visita;
                            $visita->motivo  = 'ATENDIMENTO';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    }

                    //dd($paciente->id);
                    
                    try {
                        $fisio  = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[14]), "Y-m-d");
                        if( $fisio != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $fisio;
                            $visita->motivo             = 'ATENDIMENTO';
                            $visita->tp_atendimento     = 'FISIOTERAPIA';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }

                    } catch (\Exception $e) {
                        $fisio = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }

                    try {
                        $nutri  = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[15]), "Y-m-d");
                        if( $nutri != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $nutri;
                            $visita->motivo             = 'ATENDIMENTO';
                            $visita->tp_atendimento     = 'NUTRICÃO';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    } catch (\Exception $e) {
                        $nutri = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }

                    try {
                        $psi    = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[16]), "Y-m-d");
                        if( $psi != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $psi;
                            $visita->motivo             = 'ATENDIMENTO';
                            $visita->tp_atendimento     = 'PSICOLOGIA';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    } catch (\Exception $e) {
                        $psi = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }                    

                    try {
                        $as     = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[17]), "Y-m-d");
                        if( $as != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $as;
                            $visita->motivo             = 'ATENDIMENTO';
                            $visita->tp_atendimento     = 'SERVIÇO SOCIAL';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    } catch (\Exception $e) {
                        $as = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }

                    try {
                        $odonto = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[18]), "Y-m-d");
                        if( $odonto != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $odonto;
                            $visita->motivo             = 'ATENDIMENTO';
                            $visita->tp_atendimento     = 'ODONTOLOGIA';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    } catch (\Exception $e) {
                        $odonto = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }

                    try {
                        $fono   = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[19]), "Y-m-d");
                        if( $fono != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $fono;
                            $visita->motivo             = 'ATENDIMENTO';
                            $visita->tp_atendimento     = 'FONOAUDIOLOGIA';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    } catch (\Exception $e) {
                        $fono = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }

                    try {
                        $coleta = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[20]), "Y-m-d");
                        if( $coleta != '1970-01-01'){
                            $visita = new Visita;
                            $visita->acompanhamento_id  = $acompanhamento->id;
                            $visita->dt_visita          = $coleta;
                            $visita->motivo             = 'APOIO';
                            //$visita->tp_atendimento     = 'FONOAUDIOLOGIA';
                            $visita->save();
                            $visita->servidores()->attach(1);
                        }
                    } catch (\Exception $e) {
                        $coleta = null;
                    }

                    try {
                        //print_r('ALTA: ' .$alta .' --- ' .$paciente->nome . ' - ' .$key .'</br>' );
                        $alta   = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[36]), "Y-m-d");
                        
                        if( $alta != '1970-01-01'){
                            $acompanhamento->ativo                  = 0;
                            $acompanhamento->dt_termino             = $alta;
                            $acompanhamento->destino_pos_alta       = $registro[37]; 
                            $acompanhamento->save();
                        }

                    } catch (\Exception $e) {
                        $alta = null;
                        //
                    }

                    try {
                        $obito  = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($registro[38]), "Y-m-d");
                        if( $obito != '1970-01-01'){

                            $acompanhamento->ativo                  = 0;
                            $acompanhamento->dt_termino             = $obito;
                            $acompanhamento->save();
    
                            $paciente->obito      = true;
                            $paciente->dt_obito   = $obito;
                            $paciente->situacao   = 'ÓBITO';
    
                            $paciente->save();
    
                            print_r('OBITO: ' .$obito .' --- ' .$paciente->nome . ' - ' .$key .'</br>' );
                        }

                    } catch (\Exception $e) {
                        $obito = null;
                        //echo 'Exceção capturada: ',  $e->getMessage(), "\n";
                    }

                    print_r($key .'</br>' );


                }
            }

           // dd("ok");
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
        DB::commit();
        
        dd("terminou");
    } */
}
