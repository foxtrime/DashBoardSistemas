<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Acompanhamento;
use App\Models\Servidor;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;


class ServidorController extends Controller
{
    
    public function index()
    {
        $servidores = Servidor::with('visitas')->get();
        return view ('servidor.index', compact('servidores'));    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $titulo         = 'Novo Servidor';
        $especialidades        = pegaValorEnum('servidores', 'especialidade',true);

        return view ('servidor.create', compact('titulo','especialidades'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //ajusta o checkbox de ATIVO
		if($request->ativo =="on"){
			$request->merge(['ativo' => 1 ]);
		}else{
			$request->merge(['ativo' => 0 ]);
        }
        
        $this->validate($request,[
            'nome'             => 'required|min:3|max:100|unique:servidores',
            "especialidade"    => 'required',
        ]);

        DB::beginTransaction();
        try {
            $servidor = new Servidor($request->all());
            $servidor->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao criar o Servidor.');    
        }
        DB::commit();

        return redirect('servidor')->with('sucesso', 'Servidor criado com sucesso!');    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Servidor  $equipe
     * @return \Illuminate\Http\Response
     */
    public function show(Servidor $servidor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Servidor  $equipe
     * @return \Illuminate\Http\Response
     */
    public function edit(Servidor $servidor)
    {
        $titulo   = 'Edição de Servidor';
        $especialidades = pegaValorEnum('servidores', 'especialidade',true);
        
        return view ('servidor.create', compact('servidor','titulo','especialidades'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servidor  $equipe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Servidor $servidor)
    {
        //ajusta o checkbox de ATIVO
		if($request->ativo =="on"){
			$request->merge(['ativo' => 1 ]);
		}else{
			$request->merge(['ativo' => 0 ]);
        }
        
        $this->validate($request,[
            'nome'             => "required|min:3|max:100|unique:servidores,nome,$servidor->id",
            "especialidade"    => 'required',
        ]);


        DB::beginTransaction();
        try {
            $servidor->fill($request->all());
            $servidor->save();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return back()->withInput()->with('error', 'Falha ao Editar o Servidor.');    
        }
        DB::commit();

        return redirect('servidor')->with('sucesso', 'Servidor Editado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Servidor  $equipe
     * @return \Illuminate\Http\Response
     */
    public function destroy(Servidor $servidor)
    {

        DB::beginTransaction();
        try {
        
            $servidor = Servidor::with('visitas')->where('id', $servidor->id)->first();


            if( count( $servidor->visitas ) > 0 ){
                return response('np', 200);        
            }

            $servidor->delete();


        } catch (\Throwable $th) {
			DB::rollBack();
            //dd($th);
			return response($th, 500);
        }
        DB::commit();
        return response('ok', 200);
    }
}
