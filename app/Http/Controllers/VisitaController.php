<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
use App\Imports\PacientesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Acompanhamento;
use App\Models\Servidor;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;


class VisitaController extends Controller
{
    public function index()
    {
        $visitas = Visita::with('acompanhamento')->get();
        
        return view ('visita.index', compact('visitas'));    
    }
 
    public function create(Acompanhamento $acompanhamento)
    {
        //dd($acompanhamento);
        
        if($acompanhamento->ativo == false ){
            return back()->withInput()->with('error', 'Esse Acompanhamento não está Ativo!');   
        }
                
        $origin_caller = str_replace(url('/'), '', url()->previous()) ;
        if($origin_caller == "/visita"){
            $origin = "visita";
            
        }else{
            $origin = "paciente";
        }
 

        $titulo         = 'Nova Visita de Paciente';
        $motivos        = pegaValorEnum('visitas', 'motivo',true);
        $servidores     = Servidor::where('ativo', true)->get();
        $diasAtd        = diasEntreDatas( date('Y-m-d'), $acompanhamento->dt_inicio );
        $situacoes      = pegaValorEnum('visitas', 'situacao');
        $tp_atendimentos= pegaValorEnum('visitas', 'tp_atendimento',true);
        
        $tipos_acompanhamento = [];

        if( $acompanhamento->medicina)          array_push($tipos_acompanhamento,'MEDICINA') ;
        if( $acompanhamento->enfermagem)        array_push($tipos_acompanhamento,'ENFERMAGEM') ;
        if( $acompanhamento->fisioterapia)      array_push($tipos_acompanhamento,'FISIOTERAPIA') ;
        if( $acompanhamento->fonoaudiologia)    array_push($tipos_acompanhamento,'FONOAUDIOLOGIA') ;
        if( $acompanhamento->nutricao)          array_push($tipos_acompanhamento,'NUTRIÇÃO') ;
        if( $acompanhamento->psicologia)        array_push($tipos_acompanhamento,'PSICOLOGIA') ;
        if( $acompanhamento->odontologia)       array_push($tipos_acompanhamento,'ODONTOLOGIA') ;
        if( $acompanhamento->servico_social)    array_push($tipos_acompanhamento,'SERVIÇO SOCIAL') ;
        

        return view ('visita.create', compact('tp_atendimentos','origin','acompanhamento','titulo','motivos','servidores','diasAtd','situacoes','tipos_acompanhamento'));
    } 


    public function store(Request $request)
    {
        
        //dd($request->all());
        
        $date       =   $request->dt_visita;
        $arr        =   explode('/',$date);
        $newDate    =   "$arr[2]".'-'."$arr[1]".'-'."$arr[0]";
        //$request->merge(['dt_visita' => date("Y-m-d", strtotime($newDate) )]);
        $data_visita =  date("Y-m-d", strtotime($newDate) );

        if ($request->dt_obito){
            $date       =   $request->dt_obito;
            $arr        =   explode('/',$date);
            $newDate    =   "$arr[2]".'-'."$arr[1]".'-'."$arr[0]";
            $data_obito =  date("Y-m-d", strtotime($newDate) );
        }else{
            $data_obito = null;
        }

        
        $request->merge(['medicina'         => str_replace('on', "1", $request->MEDICINA)]);
        $request->merge(['enfermagem'       => str_replace('on', "1", $request->ENFERMAGEM)]);
        $request->merge(['fisioterapia'     => str_replace('on', "1", $request->FISIOTERAPIA)]);
        $request->merge(['fonoaudiologia'   => str_replace('on', "1", $request->FONOAUDIOLOGIA)]);
        $request->merge(['nutricao'         => str_replace('on', "1", $request->NUTRIÇÃO)]);
        $request->merge(['psicologia'       => str_replace('on', "1", $request->PSICOLOGIA)]);
        $request->merge(['odontologia'      => str_replace('on', "1", $request->ODONTOLOGIA)]);
        $request->merge(['servico_social'   => str_replace('on', "1", $request->SERVIÇO_SOCIAL)]);

       //dd($request->all());


        $this->validate($request,[
            "servidores"            => 'required',
            "acompanhamento_id"     => 'required',
            "motivo"                => 'required',
            "situacao"              => 'required',
            'observacao'            => 'nullable',
        ]);

        //dd($request->all());
        $acompanhamento = Acompanhamento::where('id',$request->acompanhamento_id)->first();
        $paciente       = Paciente::find($acompanhamento->paciente_id);

        try {
            $dt = $acompanhamento->ultima_visita->first()->dt_visita;
            //dd(isset($dt));
           
            $this->validate($request,[
                "dt_visita"             => 'required|date_format:d/m/yy|after_or_equal:'.$dt,
            ]);
        } catch (\Throwable $th) {
            //dd($th);
            $this->validate($request,[
                "dt_visita"             => 'required|date_format:d/m/yy',
            ]);

        }

        $ids_servidor = $pieces = explode(",", $request->servidores);

        DB::beginTransaction();
        try {
            $visita = new Visita;
            $visita->acompanhamento_id  = $request->acompanhamento_id;
            $visita->motivo             = $request->motivo;
            $visita->situacao           = $request->situacao;
            $visita->tp_atendimento     = $request->tp_atendimento;
            $visita->dt_visita          = $data_visita;
            $visita->observacao         = $request->observacao;
            $visita->save();
            
            //verifica se já existe a data de inicio do acompanhemento, se não existir significa que
            // essa é a 1ª visita, então atualiza a data no acompanhamento
            if( $acompanhamento->dt_inicio == null ){
                $acompanhamento->dt_inicio = $data_visita ;
            }
           
            switch ($request->situacao) {

                case 'REALIZADA':
                    $paciente->situacao = 'VISITA REALIZADA';
                    break;

                case 'REALIZADA - ALTA':
                    $paciente->situacao = 'VISITA REALIZADA - ALTA';
                    break;

                case 'NÃO REALIZADA - EQUIPE NÃO ATENDIDA':
                    $paciente->situacao = 'VISITA NÃO REALIZADA - EQUIPE NÃO ATENDIDA';
                    break;

                case 'NÃO REALIZADA - PACIENTE DESACOMPANHADO':
                    $paciente->situacao = 'VISITA NÃO REALIZADA - PACIENTE DESACOMPANHADO';
                    break;

                case 'NÃO REALIZADA - PACIENTE RECUSOU':
                    $paciente->situacao = 'VISITA NÃO REALIZADA - PACIENTE RECUSOU';
                    break;

                case 'NÃO REALIZADA - INTERNADO':
                    $paciente->situacao = 'VISITA NÃO REALIZADA - INTERNADO';
                    break;

                case 'NÃO REALIZADA - PACIENTE AUSENTE':
                    $paciente->situacao = 'VISITA NÃO REALIZADA - PACIENTE AUSENTE';
                    break;

                case 'NÃO REALIZADA - OUTROS':
                    $paciente->situacao = 'VISITA NÃO REALIZADA - OUTROS';
                    break;

                default:
                    $paciente->situacao = $request->situacao;
                    break;
            }


            if (strpos($request->situacao,'ENCERRAMENTO') > -1){
                $acompanhamento->dt_termino = $data_visita;
                $acompanhamento->ativo      = false;
                

                if (strpos($request->situacao,'ÓBITO') > -1){
                    $paciente->obito    = true;
                    $paciente->dt_obito = $data_obito;
                }

            }
         
            $acompanhamento->save();
            
            
            //atualiza a situação do paciente
            $paciente->save();

            //dd($paciente);

            foreach ($ids_servidor as $key => $id) {
                $servidor = Servidor::where('id', $id)->first();

                $visita->servidores()->attach($servidor->id);
                //dd($servidor);
            }

            //dd($request->all());
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao criar a Visita.');    
        }
        DB::commit();

        return redirect("/paciente/visitas/$request->acompanhamento_id")->with('sucesso', 'Visita criada com sucesso!');
         
 
    }

    public function show(Visita $visita)
    {
        //
    }
    public function edit(Visita $visita)
    {

        //dd($visita->id);
        
        
        $tipos_acompanhamento = [];

        if( $visita->acompanhamento->medicina)          array_push($tipos_acompanhamento,'MEDICINA') ;
        if( $visita->acompanhamento->enfermagem)        array_push($tipos_acompanhamento,'ENFERMAGEM') ;
        if( $visita->acompanhamento->fisioterapia)      array_push($tipos_acompanhamento,'FISIOTERAPIA') ;
        if( $visita->acompanhamento->fonoaudiologia)    array_push($tipos_acompanhamento,'FONOAUDIOLOGIA') ;
        if( $visita->acompanhamento->nutricao)          array_push($tipos_acompanhamento,'NUTRIÇÃO') ;
        if( $visita->acompanhamento->psicologia)        array_push($tipos_acompanhamento,'PSICOLOGIA') ;
        if( $visita->acompanhamento->odontologia)       array_push($tipos_acompanhamento,'ODONTOLOGIA') ;
        if( $visita->acompanhamento->servico_social)    array_push($tipos_acompanhamento,'SERVIÇO SOCIAL') ;
        


        //$visita = Visita::with('servidores')->find($visita->id)->first();

        //dd($visita->id);
        //$visita = Visita::with('acompanhamento')->find(3846)->first();

        $origin_caller = str_replace(url('/'), '', url()->previous()) ;

        

        if($origin_caller == "/visita"){
            $origin = "visita";
            
        }elseif( $origin_caller == "/paciente/visitas/$visita->acompanhamento_id" ){
            $origin = "paciente";
        }
 
        //dd($origin_caller);
        //dd("/paciente/visitas/$visita->acompanhamento_id");

        //dd($origin);

        $titulo             = 'Edição de Visita';
        $motivos            = pegaValorEnum('visitas', 'motivo',true);
        $servidores         = Servidor::get();
        $acompanhamento     = $visita->acompanhamento;
        $diasAtd            = diasEntreDatas( date('Y-m-d'), $acompanhamento->dt_inicio );
        $situacoes          = pegaValorEnum('visitas', 'situacao');
        $tp_atendimentos    = pegaValorEnum('visitas', 'tp_atendimento',true);
        

        //dd($origin);

        return view ('visita.create', compact('tipos_acompanhamento','origin','acompanhamento','titulo','motivos','servidores',
                                                'diasAtd','situacoes','visita','tp_atendimentos'));
    }

    public function update(Request $request, Visita $visita)
    {
        //dd($request->all());
        
        $date       =   $request->dt_visita;
        $arr        =   explode('/',$date);
        $newDate    =   "$arr[2]".'-'."$arr[1]".'-'."$arr[0]";
        $request->merge(['dt_visita' => date("Y-m-d", strtotime($newDate) )]);
        $data_visita =  date("Y-m-d", strtotime($newDate) );
        
        $request->merge(['medicina'         => str_replace('on', "1", $request->MEDICINA)]);
        $request->merge(['enfermagem'       => str_replace('on', "1", $request->ENFERMAGEM)]);
        $request->merge(['fisioterapia'     => str_replace('on', "1", $request->FISIOTERAPIA)]);
        $request->merge(['fonoaudiologia'   => str_replace('on', "1", $request->FONOAUDIOLOGIA)]);
        $request->merge(['nutricao'         => str_replace('on', "1", $request->NUTRIÇÃO)]);
        $request->merge(['psicologia'       => str_replace('on', "1", $request->PSICOLOGIA)]);
        $request->merge(['odontologia'      => str_replace('on', "1", $request->ODONTOLOGIA)]);
        $request->merge(['servico_social'   => str_replace('on', "1", $request->SERVIÇO_SOCIAL)]);

        $this->validate($request,[
            "servidores"            => 'required',
            "acompanhamento_id"     => 'required',
            "motivo"                => 'required',
            "situacao"              => 'required',
            "dt_visita"             => 'required|date',
            'observacao'            => 'nullable',
        ]);
            
        


        $acompanhamento = Acompanhamento::with('ultima_visita')->where('id',$request->acompanhamento_id)->first();
        $visita         = Visita::where('id',$request->visita_id)->first();
        $paciente       = Paciente::find($acompanhamento->paciente_id)->first();
        $ultima_visita  = $acompanhamento->ultima_visita[0];



        //dd($request->all());

        //separa o nome dos servidores
        //$nomes_servidor = $pieces = explode(",", $request->servidores);
        $ids_servidor = $pieces = explode(",", $request->servidores);

        //seta variável que informa se é a ultima visita do acompanhamento
        if($visita == $ultima_visita){
            $ultima = true;
        }else{
            $ultima = false;
        }

        //seta variável que informa se é for uma visita que a SITUAÇÃO dê alta
        if ($request->situacao == 'ALTA' || $request->situacao == 'SEM PERFIL'   || $request->situacao == 'ÓBITO' 
                                || $request->situacao == 'MUDANÇA DE MUNICÍPIO'  ||  $request->situacao == 'ALTA ADMINISTRATIVA'){

            $alta = true;
        }else{
            $alta = false;
        }
        
        
        
        DB::beginTransaction();
        try {
            
            //dd($request->situacao);
            $visita->motivo             = $request->motivo;
            $visita->dt_visita          = $request->dt_visita;
            $visita->situacao           = $request->situacao;
            $visita->tp_atendimento     = $request->tp_atendimento;
            $visita->observacao         = $request->observacao;
            $visita->save();
                       

            
            //se a dt_visita é menor ao dt_inicio do acompanhamento devemos atualizar a data de inicio do acompanhamento
            if ($visita->dt_visita < $acompanhamento->dt_inicio){
                $acompanhamento->dt_inicio = $request->dt_visita;
            }

            //testa se é a ultima visita
            if($ultima){
                //atualiza a situação do paciente
                $paciente->situacao  = $request->situacao;
                $paciente->save();


            }else{
                if ($alta){
                    return back()->withInput()->with('erro', 'Essa SITUAÇÃO não pode ser utilizada pois existe(m) visita(s) poesterior(es) a essa!');    
                }
            }
           
            
            //atualiza acompanhamento ativo
            if ($alta){
                $acompanhamento->ativo      = false;
                $acompanhamento->dt_termino = $request->dt_visita;
            }else{
                $acompanhamento->ativo      = true;
            }
            
            //dd($request->all());         

            $acompanhamento->save();
            
           

            //dd($paciente);

            $visita->servidores()->detach();

            foreach ($ids_servidor as $key => $id) {
                $servidor = Servidor::where('id', $id)->first();

                $visita->servidores()->attach($servidor->id);
                //dd($servidor);
            }

            //dd($request->all());
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao criar a Visita.');    
        }
        DB::commit();


        if($request->origin_path == "paciente" ){
            return redirect("/paciente/visitas/$request->acompanhamento_id")->with('sucesso', 'Visita Alterada com sucesso!');
        }elseif ($request->origin_path == "visita" ){
            return redirect("/visita")->with('sucesso', 'Acompanhamento Alterado com sucesso!');
        }


        
    }
    public function destroy(Request $request)
    {
        $visita             = Visita::where('id',$request->id_visita)->first();


        $acompanhamento     = Acompanhamento::with('ultima_visita')->where('id',$visita->acompanhamento_id)->first();
        $visitas            = Visita::where('acompanhamento_id',$acompanhamento->id)->orderBy('dt_visita', 'DESC')->get();
        $paciente           = Paciente::find($acompanhamento->paciente_id)->first();

        

        $primeira_visita    = $acompanhamento->primeira_visita->first();
        $ultima_visita      = $acompanhamento->ultima_visita->first();
        $penultima_visita   = $visitas[count($visitas)-2];


        //return $ultima_visita;
        
        DB::beginTransaction();
        try {

            if( $acompanhamento->ativo ){
                //return "ativo";
                if( count($visitas) > 1  ){
                    if( $visita == $ultima_visita ){
                        //return "ativo mais que 1 - ultima visita";
                        $acompanhamento->situacao   = $visitas[1]->situacao;
                        $acompanhamento->save();

                        $paciente->situacao         = $visitas[1]->situacao;
                        $paciente->save();
                    }else if( $visita == $primeira_visita ){
                        //return "ativo mais que 1 - primeira visita";
                        //return $penultima_visita->dt_visita;
                        $acompanhamento->dt_inicio   = $penultima_visita->dt_visita;
                        $acompanhamento->save();
                    }

                    //return "ativo mais que 1 - não é a ultima";
                }else{
                    //return "else";
                    //return "ativo 1";
                    $acompanhamento->situacao   = null;
                    $acompanhamento->save();

                    $paciente->situacao         = "----";
                    $paciente->save();

                }
                
                $visita->servidores()->detach();
                $visita->delete();
            }else{
                DB::rollBack();
                //dd($th);
                return response('Não pode excluir Visita de Acompanhamento Inativo', 500);
            }



        } catch (\Throwable $th) {
            DB::rollBack();
            //dd($th);
            return response('erro', 500);
        }

        DB::commit();
        return response('ok', 200);
    }
}
