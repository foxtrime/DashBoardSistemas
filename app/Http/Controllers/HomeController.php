<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use GuzzleHttp\Middleware;


use App\Models\Funcionario;
use App\Models\Role;
use App\Models\User;

use App\Models\Acompanhamento;
use App\Models\Servidor;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\Cid;

use Carbon\Carbon;
use Datatables;


class HomeController extends Controller
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
		$pacientes = Paciente::all();
		return view('home', compact('pacientes'));
	}


	public function embreve($rotina)
	{
		return view ('embreve');
	}
	
	public function geo()
	{

		
		$pacientes = Paciente::all();
		//$pacientes = Paciente::where('bairro', 'NILÓPOLIS')->get();

		
		foreach ($pacientes as $key => $paciente) {
			
			if ( is_null($paciente->latitude) ) {
				$endereco = tirarAcentos("$paciente->logradouro" ." , " .$paciente->numero ." - " .$paciente->bairro ."  " .$paciente->cep ." - mesquita - brasil");
			
				//dd( $endereco);
				$chamada = "https://maps.googleapis.com/maps/api/geocode/json?address=$endereco&key=AIzaSyD88keSNZva3fJ2F01M6YOw78uf3xrtU1I";
					
				$client 	= new \GuzzleHttp\Client();
				$res 		= $client->request('GET', $chamada );
				$resposta 	= json_decode($res->getBody()->getContents());
				
				$latitude	=	$resposta->results[0]->geometry->location->lat;
				$longitude	=	$resposta->results[0]->geometry->location->lng;
				
				
	
				//$promise->wait();

				echo $paciente->id ."/n";
				$paciente->latitude = $latitude;
				$paciente->longitude = $longitude;
				$paciente->save();
				

			};

			


		}
		

			

		


		die();
		return view ('embreve');
	}

}
