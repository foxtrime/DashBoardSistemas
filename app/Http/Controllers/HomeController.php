<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


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
		return view('home', compact('qtd_pacientes', 'qtd_pacientes_acompanhamento','vetor','gbairro','gcid'));
	}


	public function embreve($rotina)
	{
		return view ('embreve');
	}

}
