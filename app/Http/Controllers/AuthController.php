<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Funcionario;
use Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function entrar(Request $request)
    {
        $credentials = ['email'=>$request->email,'password'=>$request->password];
        //dd($credentials);
        if(Auth::attempt($credentials)){
            $usuario_logado = Auth::user();
            $retorno = DB::connection('mysql2')->select("select consulta_role($usuario_logado->id , 'DASHPREF', 'LOGIN') as retorno");
            //dd($retorno[0]->retorno);
            if($retorno[0]->retorno){
                return redirect()->intended('home');
            }else{
                return redirect()->back()->with('msg','Voce não tem acesso ao sistema');
            }
        }else{
            return redirect()->back()->with('msg','Acesso Negado, Email ou senha invalida');
        }
    }
}