<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Session;

use App\Models\Acompanhamento;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;


class AcompanhamentoController extends Controller
{
    public function visitas($acompanhamento_id)
    {
        $visitas = Visita::where('acompanhamento_id', $acompanhamento_id)->with('acompanhamento','equipe')->get();
        dd($visitas);
  
        return view ('paciente.index-visitas', compact('visitas','paciente','visitas','cid'));    
    }

    public function index()
    {
        
        $acompanhamentos = Acompanhamento::with('paciente','visitas','cid','primeira_visita','ultima_visita')->get();

        //dd( proximaVisita($acompanhamentos[0]) );
        //dd( $acompanhamentos[0]->ultima_visita[0] );

        return view ('acompanhamento.index', compact('acompanhamentos','paciente','visitas','cid','ultima_visita'));    
    }

    public function show(Acompanhamento $acompanhamento)
    {
        $titulo             = 'Visualização de Acompanhamento';
        $acompanhamento    = Acompanhamento::with('paciente', 'visitas','cid' )->where('id', $acompanhamento->id)->first();
        $idade              = \calculaIdade($acompanhamento->paciente->nascimento);
        //dd($acompanhamento);

		return view ('acompanhamento.show', compact('titulo','idade','acompanhamento'));
    }

    
    public function create(Paciente $paciente)
    {
        $acompanhamento = Acompanhamento::where('paciente_id', $paciente->id)->where('ativo',true)->get();
        
		if(sizeof($acompanhamento) > 0 ){
            return back()->withInput()->with('error', 'O Paciente possui um Acompanhamento Ativo!');   
        }
                

        $tipos_acompanhamento = [];

        array_push($tipos_acompanhamento,'MEDICINA') ;
        array_push($tipos_acompanhamento,'ENFERMAGEM') ;
        array_push($tipos_acompanhamento,'FISIOTERAPIA') ;
        array_push($tipos_acompanhamento,'FONOAUDIOLOGIA') ;
        array_push($tipos_acompanhamento,'NUTRIÇÃO') ;
        array_push($tipos_acompanhamento,'PSICOLOGIA') ;
        array_push($tipos_acompanhamento,'ODONTOLOGIA') ;
        array_push($tipos_acompanhamento,'SERVIÇO SOCIAL') ;


        $titulo             = 'Novo Acompanhamento de Paciente';
        $cids               = Cid::all();
         
        $ads        = pegaValorEnum('acompanhamentos', 'atendimento_domiciliar',true);
        $origens    = pegaValorEnum('acompanhamentos', 'origem',true);
        $bases      = pegaValorEnum('acompanhamentos', 'base',true);
        //$situacoes  = pegaValorEnum('acompanhamentos', 'situacao',true);
        $situacoes  = ['APOIO', 'ATENDIMENTO', 'ATENDIMENTO E APOIO'];
        
        
        $origin_caller = str_replace(url('/'), '', url()->previous()) ;

        //dd( "/paciente/acompanhamento/$acompanhamento->paciente_id" );
        if($origin_caller == "/acompanhamento"){
            $origin = "acompanhamento";
            
        }else{
            $origin = "paciente";
        }

        //dd($tipos_acompanhamento);
		return view ('acompanhamento.create-acompanhamento', compact('tipos_acompanhamento','origin','paciente','titulo','ads','origens','bases','cids','situacoes'));
    } 

    public function store(Request $request)
    {
        //dd($request->all());
        $request->merge(['nascimento'   => date("Y-m-d", strtotime($request->nascimento))]);
        
        $request->merge(['medicina'         => str_replace('on', "1", $request->medicina)]);
        $request->merge(['enfermagem'       => str_replace('on', "1", $request->enfermagem)]);
        $request->merge(['fisioterapia'     => str_replace('on', "1", $request->fisioterapia)]);
        $request->merge(['fonoaudiologia'   => str_replace('on', "1", $request->fonoaudiologia)]);
        $request->merge(['nutricao'         => str_replace('on', "1", $request->nutricao)]);
        $request->merge(['psicologia'       => str_replace('on', "1", $request->psicologia)]);
        $request->merge(['odontologia'      => str_replace('on', "1", $request->odontologia)]);
        $request->merge(['servico_social'   => str_replace('on', "1", $request->servico_social)]);

        $this->validate($request,[
            "paciente_id"               => 'required',
            "cid_id"                    => 'required',
            "origem"                    => 'required',
            "base"                      => 'required',
            "atendimento_domiciliar"    => 'required',
            //"situacao"                  => 'required',
            "cid"                       => 'required',
            'observacao'                => 'nullable',
        ]);

        $request->merge(['ad_admissao'   => $request->atendimento_domiciliar]);
        
        
       //dd($request->all());

        $paciente = Paciente::find($request->paciente_id);
        //dd($request->all());
        
        DB::beginTransaction();
        try {
            $acompanhamento = new Acompanhamento($request->all());
            $acompanhamento->save();
            
            $paciente->fill( ['situacao' => "AGUARDANDO 1ª VISITA"] );
            $paciente->save();
            
            //dd($paciente);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao criar o Acompanhamento.');    
        }
        DB::commit();

        return redirect("/paciente/acompanhamentos/$request->paciente_id")->with('sucesso', 'Acompanhamento criado com sucesso!');
        

    }

    public function edit(Acompanhamento $acompanhamento)
    {
        //dd($acompanhamento );
        //dd($request->path() );
        //dd( url()->previous() );
        //dd( str_replace(url('/'), '', url()->previous()) );

          
        $tipos_acompanhamento = [];

        if( $acompanhamento->medicina)          array_push($tipos_acompanhamento,'MEDICINA') ;
        if( $acompanhamento->enfermagem)        array_push($tipos_acompanhamento,'ENFERMAGEM') ;
        if( $acompanhamento->fisioterapia)      array_push($tipos_acompanhamento,'FISIOTERAPIA') ;
        if( $acompanhamento->fonoaudiologia)    array_push($tipos_acompanhamento,'FONOAUDIOLOGIA') ;
        if( $acompanhamento->nutricao)          array_push($tipos_acompanhamento,'NUTRIÇÃO') ;
        if( $acompanhamento->psicologia)        array_push($tipos_acompanhamento,'PSICOLOGIA') ;
        if( $acompanhamento->odontologia)       array_push($tipos_acompanhamento,'ODONTOLOGIA') ;
        if( $acompanhamento->servico_social)    array_push($tipos_acompanhamento,'SERVIÇO SOCIAL') ;


        $origin_caller = str_replace(url('/'), '', url()->previous()) ;

        //dd( "/paciente/acompanhamento/$acompanhamento->paciente_id" );
        //dd( "/paciente/acompanhamento/$acompanhamento->id" );
        //dd( $origin_caller );

        if($origin_caller == "/acompanhamento"){
            $origin = "acompanhamento";
            
        }elseif( $origin_caller == "/paciente/acompanhamentos/$acompanhamento->paciente_id" ){
            $origin = "paciente";
        
        }elseif( $origin_caller == "/acompanhamento/$acompanhamento->id/edit" ){
            $origin = "paciente";
        }

        //dd($origin);

        $paciente   = Paciente::where('id', $acompanhamento->paciente_id)->first();
        $titulo     = 'Edição de Acompanhamento';
        $cids       = Cid::all();
        
        $ads        = pegaValorEnum('acompanhamentos', 'atendimento_domiciliar',true);
        $origens    = pegaValorEnum('acompanhamentos', 'origem',true);
        $bases      = pegaValorEnum('acompanhamentos', 'base',true);
        //$situacoes  = pegaValorEnum('acompanhamentos', 'situacao',true);
        $situacoes  = ['APOIO', 'ATENDIMENTO', 'ATENDIMENTO E APOIO'];

        //dd($acompanhamento->medicina);
        
        return view ('acompanhamento.create-acompanhamento', 
                    compact('tipos_acompanhamento','origin','paciente','titulo','ads','origens','bases','cids','situacoes','acompanhamento'));
    } 
    
    public function update(Acompanhamento $acompanhamento, Request $request)
    {
        //dd($request->all());

        $request->merge(['medicina'         => str_replace('on', "1", $request->medicina)]);
        $request->merge(['enfermagem'       => str_replace('on', "1", $request->enfermagem)]);
        $request->merge(['fisioterapia'     => str_replace('on', "1", $request->fisioterapia)]);
        $request->merge(['fonoaudiologia'   => str_replace('on', "1", $request->fonoaudiologia)]);
        $request->merge(['nutricao'         => str_replace('on', "1", $request->nutricao)]);
        $request->merge(['psicologia'       => str_replace('on', "1", $request->psicologia)]);
        $request->merge(['odontologia'      => str_replace('on', "1", $request->odontologia)]);
        $request->merge(['servico_social'   => str_replace('on', "1", $request->servico_social)]);


        $this->validate($request,[
            "paciente_id"               => 'required',
            "cid_id"                    => 'required',
            "origem"                    => 'required',
            "base"                      => 'required',
            "atendimento_domiciliar"    => 'required',
            "cid"                       => 'required',
            'observacao'                => 'nullable',
        ]);

        //dd($request->all());
        
        $paciente = Paciente::find($request->paciente_id);
        //dd($request->all());
        
        DB::beginTransaction();
        try {

            $situacao = $request->situacao; //variavel para usar no IF abaixo
            if ($situacao == 'ALTA' || 
                    $situacao == 'SEM PERFIL' || 
                    $situacao == 'ÓBITO' || 
                    $situacao == 'MUDANÇA DE MUNICÍPIO' ||  
                    $situacao == 'ALTA ADMINISTRATIVA'){


                $acompanhamento->dt_termino = $request->dt_visita;
                $acompanhamento->ativo      = false;
            }

            $acompanhamento->fill($request->all());
            $acompanhamento->save();
            
            $paciente->fill( ['situacao' => $request->situacao] );
            $paciente->save();
             
            //dd($paciente);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao Alterar o Acompanhamento.');    
        }
        DB::commit();
        //return redirect("/paciente/acompanhamentos/$request->paciente_id")->with('sucesso', 'Acompanhamento Alterado com sucesso!');
        
        //dd($request->all());

        //return redirect("/paciente/acompanhamentos/$request->paciente_id")->with('sucesso', 'Acompanhamento Alterado com sucesso!');

        if($request->origin_path == "paciente" ){
            return redirect("/paciente/acompanhamentos/$request->paciente_id")->with('sucesso', 'Acompanhamento Alterado com sucesso!');
        }elseif ($request->origin_path == "acompanhamento" ){
            return redirect("/acompanhamento")->with('sucesso', 'Acompanhamento Alterado com sucesso!');
        }


    }


    public function destroy(Acompanhamento $acompanhamento)
    {
        
        DB::beginTransaction();
        try {


            if( $acompanhamento->ativo ){
                $ac_quantidade      = Acompanhamento::count();
                
                $paciente           = $acompanhamento->paciente;
                
                if( $ac_quantidade > 1){
                    //busca o ultimo acompanhamento para atualizar os dados no paciente
                    $ac_recente         = Acompanhamento::orderBy('dt_inicio','DESC')->first();
                    $paciente->situacao = $ac_recente->situacao;
                }else{
                    $paciente->situacao = "-----";
                }

                $paciente->save();
            }

            
            $acompanhamento->visitas()->delete();
            $acompanhamento->delete();

        } catch (\Throwable $th) {
			DB::rollBack();
            //dd($th);
			return response($th, 500);
        }
        DB::commit();
        return response('ok', 200);
    }
   
}
