<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Solicitante;
use App\Models\Secretaria;
use App\Models\Funcionario;
use App\Models\Role;
use App\Models\User;
//use PDF;

use Illuminate\Support\Facades\Mail;
use App\Mail\CadastraUsuario;

class UserController extends Controller

{
	public function __construct(User $user)
	{
		$this->user = $user; 
		$this->middleware('auth');
	}

	public function index()
	{
		
		$usuario_logado  = Auth::user();
		
		//dd($usuario_logado);
		//dd($usuario_logado->roles[0]->permissions->all()    );

		//teste de segurança
		/* if ( $usuario_logado->can('read_all_users') )
		{
			$usuarios = User::with('secretaria')->orderBy('name')->get();

		}elseif ( $usuario_logado->can('read_sec_users') ){
			$usuarios = User::where('secretaria_id','=', $usuario_logado->secretaria_id)->with('secretaria')->orderBy('name')->get();

		}elseif ( $usuario_logado->can('read_own_users') ){

			$usuarios = User::where('id','=', $usuario_logado->id)->with('secretaria')->orderBy('name')->get();
			
		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		}; */

		return view('usuario.lista', compact('usuarios', 'statuss','usuario_logado'));
	}
	
	public function create()
	{

		$usuario_logado  = User::find(Auth::user()->id);

		
		if ( $usuario_logado->can('create_all_users') )
		{
			$secretarias   = Secretaria::orderBy('nome')->get();
			$roles  			= Role::where('id','>', $usuario_logado->roles[0]->id )->get();

		}elseif ( $usuario_logado->can('create_sec_users') ){

			$secretarias 	= Secretaria::where('id','=', $usuario_logado->secretaria_id)->orderBy('nome')->get();
			$roles  			= Role::where('name','=', 'FUNCIONARIO' )
											->orWhere('name','=', 'FISCAL' )
											->orWhere('name','=', 'MOTORISTA' )
											->get();

		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		};

		

//		dd( $roles );

		$statuss   = pegaValorEnum('users', 'status');

		return view('usuario.create', compact('statuss', 'secretarias','roles'));
	}
	
	public function store(Request $request)
	{       
		$usuario_logado  = User::find(Auth::user()->id);

		if ( $usuario_logado->can('create_all_users') )
		{
			//segue
		}elseif ( $usuario_logado->can('create_sec_users') ){

			if($usuario_logado->secretaria_id != $request->secretaria_id ){
				return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
			}

		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		};

		if ($request->motorista){      
			$request->merge(['validade_cnh' 	=> date('Y-m-d'), strtotime($request->validade_cnh)]);
			$request->merge(['cnh' 				=> str_replace('.', "", $request->cnh)]);
			$request->merge(['motorista' 		=> str_replace('on', "1", $request->motorista)]);
		}
		
		$this->validate($request, [
			'name'                  => 'required|max:255',
			'email'                 => 'nullable|email|max:255|unique:users',
			'cpf'                   => 'required|cpf|unique:users',
		]);
			
			
		//se o novo usuário tiver email o sistema irá gerar uma senha randomica
		//senão a senha será o cpf
		if( $request->email){
			$senha_gerada       = str_random(6);
		}else{
			$cpf = retiraMascaraCPF($request->cpf);
			$senha_gerada       = $cpf;
		}
		
		$request->merge(['password' => bcrypt($senha_gerada)]);

		//dd($request->all());
		// Criar um novo Usuario
		$user = User::create($request->all());
		$user->attachRole($request->role_id);
	
		if($user){
			
			DB::commit();
			//se tiver email cadastrado envia a ele a senha gerada
			if( $request->email){
				Mail::to($request->email)->queue(new CadastraUsuario($user, $senha_gerada));
			}
			return redirect('usuario')->with('sucesso', 'Usuario criado com sucesso!');
		} else {
			//Fail, desfaz as alterações no banco de dados
			DB::rollBack();
			return back()->withInput()->with('error', 'Falha ao criar o Usuario.');    
		}
	
	}
	

	public function show($id)
	{
		$usuario_logado  = User::find(Auth::user()->id);

		//teste de segurança
		if ( $usuario_logado->can('read_all_users') )
		{
			$user = $this->user->find($id);

		}elseif ( $usuario_logado->can('read_sec_users') ){
			$user = $this->user->find($id);
		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		};

		return $user;
	}
	
	public function edit($id)
	{
	
		//teste de segurança
		$usuario_logado  	= User::find(Auth::user()->id);
		$usuario 			= User::find($id);

		
		
		if ( $usuario_logado->can('update_all_users') and ! $usuario->hasRole('DSV') )
		{

			$secretarias   = Secretaria::orderBy('nome')->get();
			
		}elseif ( $usuario_logado->can('update_sec_users') and ! $usuario->hasRole('DSV') ){

			if ($usuario->secretaria_id !=  $usuario_logado->secretaria_id){
				return back()->with('erro_seguranca', 'Esse usuário não tem permissão para Alterar esse Usuário.');    
			};

			$secretarias 	= Secretaria::where('id','=', $usuario_logado->secretaria_id)->orderBy('nome')->get();

		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo ou alterar esse usuário.');    
		};

		$roles  = Role::where('id','>=', $usuario_logado->roles[0]->id )->get();
		//dd($usuario->roles[0]->id);
		//dd($usuario);

		$statuss   = pegaValorEnum('users', 'status');
		
		return view('usuario.create', compact('usuario_logado', 'usuario', 'statuss', 'secretarias','roles'));
	}
	
	public function update(Request $request, $id)
	{
		$usuario = User::find($id);
		
		$usuario_logado  	= User::find(Auth::user()->id);

		if ( $usuario_logado->can('update_all_users') and ! $usuario->hasRole('DSV') )
		{
			//segue
		}elseif ( $usuario_logado->can('update_sec_users') and ! $usuario->hasRole('DSV')){

			if($usuario_logado->secretaria_id != $request->secretaria_id ){
				return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
			}

		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		};



		if ($request->motorista){
			$request->merge(['validade_cnh' => date('Y-m-d'), strtotime($request->validade_cnh)]);
			$request->merge(['cnh' => str_replace('.', "", $request->cnh)]);
			$request->merge(['motorista' => str_replace('on', "1", $request->motorista)]);
		}

		// Validar
		$this->validate($request, [
			'name'                  => 'required|max:255',
			'email'                 => 'nullable|email|max:255|unique:users,email,' .$id,
			'cpf'                   => 'cpf|unique:users,cpf,' .$id,
			'role_id'               => 'required',
		]);

		
		// Obter o usuário
		$usuario = User::find($id);
		
		//inicia sessão de banco
		DB::beginTransaction();
		
		// Atualizar as informações
		$salvouUsuario = $usuario->update($request->all());

		$usuario->detachRole( $usuario->roles[0]->id );

		$usuario->attachRole($request->role_id);


		if($salvouUsuario ){
			DB::commit();
			return redirect('usuario')->with('sucesso', 'Informações do usuário atualizadas com sucesso.');
		} else {
    		//Fail, desfaz as alterações no banco de dados
			DB::rollBack();
			return redirect("/$usuario->id/edit")->with(['erros' => 'Falha ao editar']); 
		}

	}
	
	public function destroy($id)
	{
		//teste de segurança
		$usuario_logado  = Auth::user();

		if ( $usuario_logado->can('delete_all_users') and ! $usuario->hasRole('DSV') )
		{
			//segue
		}elseif ( $usuario_logado->can('delete_sec_users') and ! $usuario->hasRole('DSV') ){

			if($usuario_logado->secretaria_id != $request->secretaria_id ){
				return response('Esse usuário não tem permissão para executar essa ação.');
			}

		}else{
			return response('Esse usuário não tem permissão para executar essa ação.');
		};


		//inicia sessão de banco
		DB::beginTransaction();
		//deleta
		
		$apagou_usuario = User::find($id)->delete();
		if($apagou_usuario){
			DB::commit();
			return response('ok', 200);
		} else {
			//Fail, desfaz as alterações no banco de dados
			DB::rollBack();
			return response('erro', 500);
		}
	}
	
	public function AlteraSenha()
	{
		$usuario = Auth::user();
	
		return view('auth.altera_senha',compact('usuario'));    

		
	}

	public function SalvarSenha(Request $request)
	{
		//não deixa usar o cpf como senha
		if ( retiraMascaraCPF(Auth()->user()->cpf)  == $request->password)
		{
			return back()->withErrors('Essa senha não pode ser utilizada. Tente outra!');
		}


		// Validar
		$this->validate($request, [
			'password_atual'        => 'required',
			'password'              => 'required|min:6|confirmed',
			'password_confirmation' => 'required|min:6'
		]);

			
        
//
		// Obter o usuário
		$usuario = User::find(Auth::user()->id);



		if (Hash::check($request->password_atual, $usuario->password))
		{

			$usuario->update(['password' => bcrypt($request->password)]);            

			return redirect('/')->with('sucesso','Senha alterada com sucesso.');
		}else{

			return back()->withErrors('Senha atual não confere');
		}

	}


	public function ZerarSenhaUsuario(Request $request)
	{

		//teste de segurança
		$usuario_logado  = Auth::user();
		
		if ( $usuario_logado->hasRole('ADMINISTRADOR') || $usuario_logado->hasRole('DSV'))
		{
			// busca o usuario
			$usuario   = User::find($request->id);        
			//$enviar_email  = $usuario->email;

			
			//$enviar_email = 'filipe.molina@mesquita.rj.gov.br';

			//gera nova senha
			//$senha_gerada       	= str_random(6);
			//$usuario->password 	= bcrypt($senha_gerada);
			$senha_gerada       	= substr($usuario->cpf,0,3);
			
			$usuario->password 	= bcrypt($senha_gerada);

			//salva o usuário
			$usuario->save();


			//envia email com a senha de acesso
			/* Mail::send('emails.zerasenhafuncionario',[ 'email' => $usuario->email, 'senha' => $senha_gerada ], function($message) use ($enviar_email)
			{
				$message->to($enviar_email);
				//$message->to('marcelo.miranda.pp@gmail.com');
				$message->subject('Senha de acesso ao SGF');
			});
 */
			return response('ok', 200);	

		}else{
			return response('sem permissão', 501);
		};

	
	}	
	







	/* ==================================           AVATAR               =========================*/
	public function AlteraAvatar()
	{
		//dd("aqui");
		$usuario = User::find(Auth::user()->id);
		$funcionario_logado    = Funcionario::find(Auth::user()->funcionario_id);
		return view('funcionarios.altera_avatar',compact('usuario','funcionario_logado'));    
		
	}
	   
   
	public function relatorios()
  	{
		$usuario_logado  = Auth::user();

		if ( $usuario_logado->can('read_all_users') && $usuario_logado->can('read_all_relatorios') )
		{
			$acesso 			= 'ALL';
			$secretarias  	= Secretaria::orderBy('nome')->orderBy('nome')->get();
			$usuarios     = User::orderby('name')->get();

		}elseif ( $usuario_logado->can('read_sec_users') && $usuario_logado->can('read_sec_relatorios') ){
			$acesso 			= 'SEC';
			$secretarias 	= Secretaria::where('id','=', $usuario_logado->secretaria_id)->orderBy('nome')->get();
			$usuarios     	= User::where('secretaria_id','=', $usuario_logado->secretaria_id)->orderBy('name')->get();
			
		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		};

		return view('usuario.relatorio.escolhe', compact('secretarias','usuarios','acesso'));
	}

	public function imprimeRelatorio(Request $request)
  	{
		  
		$usuario_logado  = Auth::user();
		if ( $usuario_logado->can('read_all_users') && $usuario_logado->can('read_all_relatorios') )
		{
			//siga em frente mano!!!!!!

		}elseif ( $usuario_logado->can('read_sec_users') && $usuario_logado->can('read_sec_relatorios') ){
			if(  $usuario_logado->secretaria_id != $request->secretaria)
			{
				return back()->with('erro_seguranca', 'Esse usuário não tem permissão para visualizar informações da Secretaria Selecionada.');    	
			}
		}else{
			return back()->with('erro_seguranca', 'Esse usuário não tem permissão para acessar esse módulo.');    
		};

    	$sec = "";
		$titulo = 'Relatório de Usuários';

    
		$select = ' select usr.name, usr.email, usr.status, usr.cpf,usr.celular, usr.cnh, usr.categoria_cnh, usr.validade_cnh, sec.nome, sec.sigla ';

		$from   = ' from users usr, secretarias sec ';

		$where  = " where usr.secretaria_id = sec.id" ;

		if ($request->secretaria != 'T'  ) {
			
			$where  = $where  ." and sec.id = "  .$request->secretaria;
		}

		if ($request->status != 'T') {
			$where  = $where  ." and usr.status = "  ."'$request->status'";
		}

    
		if ($request->motorista != 'T') {
			$where  = $where  ." and motorista = ". $request->motorista ;
		}
    
     

		switch ($request->ordem_relatorio) {
			case 'NAME':
				$ordem = " order By name, sigla  ";
				break;

			case 'SECRETARIA':
				$ordem = " order By sigla, name ";
				break;

			case 'STATUS':
				$ordem = " order By status, name, sigla ";
				break;

			case 'CNH':
				$ordem = " order By -cnh  desc , name, sigla ";
				break;

			case 'CATEGORIA_CNH':
				$ordem = " order By -categoria_cnh  desc , name, sigla ";
				break;

			case 'VALIDADE_CNH':
				$ordem = " order By -validade_cnh  desc , name, sigla ";
				break;
		}

		//para mostrar no relatorio um icone informando a ordem do relatório
		$seta = strtolower ($request->ordem_relatorio);

		$sql = $select .$from .$where .$ordem;
		$dados = DB::select($sql);
		
		/* SOMATÓRIOS */
		$sum_usuarios = 0;
		foreach($dados as $key=>$dado){
			$sum_usuarios++;
		};

		return view ('usuario.relatorio.geral', compact('dados', 'titulo','sum_usuarios','seta'));
		
	}
}