<?php

//Auth::routes();
Route::get ("/login", 		"AuthController@login")->name('login');
Route::post('/login', 		"AuthController@entrar");
Route::get ('/logout', 		'AuthController@logout')->name('logout');


//============================= indicador =================================
Route::get('/indicador',                           'IndicadorController@index');
Route::get('/indicador/permanencia/{perido}',      'IndicadorController@permanencia');
Route::get('/indicador/admissao/{perido}',         'IndicadorController@admissao');
Route::get('/indicador/origem/{perido}',         'IndicadorController@origem');
Route::get('/indicador/obito/{perido}',         'IndicadorController@obito');
Route::get('/api/acompanhamentos', 			'Api\ApiController@acompanhamentos');   

Route::group(['middleware' => ['auth']], function () {

	Route::get 	('/alterasenha',		'UserController@AlteraSenha');
	Route::post	('/salvasenha',   		'UserController@SalvarSenha');
	Route::post('/enviarsenhausuario',	'UserController@EnviarSenhaUsuario');
	Route::post('/zerarsenhausuario',   'UserController@ZerarSenhaUsuario');
	Route::get('/', 'HomeController@index')->name('home');
	
	Route::get('/melhoremcasa', 'MelhorEmCasaController@index')->name('melhoremcasa');
	Route::get('/sgf', 			'SgfController@index')->name('sgf');
	
	Route::get('notifications', 'NotificationController@notification')->name('notifications');


	//======================= Rotas para os graficos da dashboard ====================
	Route::get('/embreve/{rotina}',           'HomeController@embreve');

	//======================= API ===============================================
	Route::get('/api/buscaCID',   				'Api\ApiController@buscaCID');   
	Route::get('/api/buscaSUS',   				'Api\ApiController@buscaSUS');   
	Route::get('/api/buscaProntuario',  		'Api\ApiController@buscaProntuario');   
	Route::get('/api/buscaAcompanhamentoAtivo', 'Api\ApiController@buscaAcompanhamentoAtivo');   
	

	
	
	//============================= paciente =================================
	Route::get('importar',										'PacienteController@import');
	Route::get('pacientes',  									'PacienteController@index')->name('pacientes');   

	Route::get('/paciente/acompanhamentos/{paciente}',  		'PacienteController@acompanhamentos');   
	//Route::get('/paciente/acompanhamentos/create/{paciente}', 'PacienteController@createacompanhamento');   
	Route::get('/paciente/visitas/{acompanhamento}',  			'PacienteController@visitas');   
	Route::get('/paciente/visitas/create/{acompanhamento}', 	'PacienteController@createvisita');   
	
	//============================= acompanhamento =================================
	Route::get('/acompanhamento/create/{paciente}',  			'AcompanhamentoController@create');   
	Route::get('/acompanhamento/visitas/{acompanhamento}',  	'AcompanhamentoController@visitas');   


	//============================= visita =================================
	Route::get('/visita/{visita}/edit',  						'VisitaController@edit');   
	Route::get('/visita/create/{acompanhamento}',  				'VisitaController@create');   
	
	//============================= cid =================================
	Route::get('/cid/tabela',                             		'CidController@tabela');
	


	
   //======================= Rotas para os RELATORIOS   =====================================
   Route::get('paciente/relatorios',                'PacienteController@relatorios');
   Route::post('paciente/imprimerelatorios',        'PacienteController@imprimeRelatorio');
   
   Route::get('acompanhamento/relatorios',          'AcompanhamentoController@relatorios');
   Route::post('acompanhamento/imprimerelatorios',  'AcompanhamentoController@imprimeRelatorio'); 
   
   Route::get('visita/relatorios',                	'VisitaController@relatorios');
   Route::post('visita/imprimerelatorios',        	'VisitaController@imprimeRelatorio');

   Route::get('servidor/relatorios',               	'ServidorController@relatorios');
   Route::get('servidor/relatorios',               	'ServidorController@relatorios');
   
   Route::post('cid/imprimerelatorios',       		'CidController@imprimeRelatorio');
   Route::post('cid/imprimerelatorios',       		'CidController@imprimeRelatorio');



	//========================================================================================
	// 										RESOURCE
	//========================================================================================
	Route::resource('cid',				'CidController');
	Route::resource('paciente',			'PacienteController');
	Route::resource('servidor',			'ServidorController');
	Route::resource('visita',			'VisitaController');
	Route::resource('acompanhamento',	'AcompanhamentoController');
	
	

});