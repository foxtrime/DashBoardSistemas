<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\helpers\geral;
use App\Models\User;
//use Session;


class AuthController extends Controller
{
	public function login()
	{

		//testa se o usuário já está logado e redireciona para a home
		if(Auth::user())
		{
			return redirect()->intended('/');
		}
		
		return view('auth.login');
	}

	public function logout()
	{
		//$modulo = Config::get('site_settings');
		//dd(session()->all()['modulo']);
		//acesso('Logout', Auth::user()->cpf);
		Auth::logout();
		return redirect("/");
	}
	

	/**
	 * Gerenciar o login quando for enviado via POST
	 */

	public function entrar(Request $request)
	{
		//armazena na sessão o modulo usado
		session(['modulo' => 'MELHOR EM CASA']);

		
		$credentials = ['email'=>$request->email,'password'=>$request->password, 'ativo' => 1];
		
		//busca o ID do sistema no SISSEG
		$sistema = DB::connection('mysql_sisseg')->table('sistemas')->select('id')->where('nome', 'MELHOR EM CASA')->first();
		
		
		//busca TODAS as ROLES do sistema no SISSEG
		$roles 	= DB::connection('mysql_sisseg')->table('roles')->select('nome')->where('sistema_id', $sistema->id)->get();

		//cria array com as ROLES do sistema
		$aRoles = [];
		foreach($roles as $role){
			array_push($aRoles, $role->nome);
		}
		//autentica
		if (Auth::attempt($credentials)) {
			
			//busca usuario
			$user = User::where('email', $request->email)->first();
			
			//dd($user);
			//varre array de roles e verifica se o usuario logado possui elguma delas
			foreach($aRoles as $role){
				if ( $user->hasRole($role) ){
					//se tiver a role inicia sistema
					return redirect()->intended('/');
				}
			}

			//se chegar até aqui é pq varreu o array de roles e não encontrou nenhuma associada ao usuário, então "desloga" e envia mensagem de erro
			Auth::logout();
			return redirect()->back()->with('erro','Voce não tem acesso ao sistema');
		
		}else{

			return redirect()->back()->with('erro','Acesso Negado, Email ou senha invalida');

		}
		
	}

}

