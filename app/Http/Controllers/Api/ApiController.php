<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Acompanhamento;
use App\Models\Paciente;
use App\Models\Cid;

class ApiController extends Controller
{

	public function adicionaGeocodePaciente(Request $request)
	{
		$paciente = Paciente::where('id', $request->id)->first();

		$paciente->latitude = $request->latitude;
		$paciente->longitude = $request->longitude;
		$paciente->save();
        
		return response()->json($paciente, 202);
		//return response()->json("OK", 202);
			
	}


	public function acompanhamentos()
    {
		$acompanhamentos = Acompanhamento::with('paciente','visitas','cid','primeira_visita','ultima_visita')->get();
		

		return response()->json($acompanhamentos[0]->ultima_visita, 200);
	}
	

	public function buscaAcompanhamentoAtivo(Request $request)
	{
		$acompanhamento = Acompanhamento::where('paciente_id', $request->paciente_id)->where('ativo',true)->get();

		if(sizeof($acompanhamento) > 0 ){
			return response()->json($acompanhamento, 200);
		}else{
			return response()->json($acompanhamento, 204);
		}
        
	}

	public function buscaCID(Request $request)
	{
		$cid = Cid::where('codigo', $request->cid)->get();

		if(sizeof($cid) > 0 ){
			return response()->json($cid, 202);
		}else{
			return response()->json($cid, 200);
		}
        
	}

	public function buscaSUS(Request $request)
	{
		$paciente = Paciente::where('sus', $request->sus)->get();

		if(sizeof($paciente) > 0 ){
			return response()->json($paciente, 202);
		}else{
			return response()->json($paciente, 200);
		}
        
	}

	public function buscaProntuario(Request $request)
	{
		$paciente = Paciente::where('prontuario', $request->prontuario)->get();

		if(sizeof($paciente) > 0 ){
			return response()->json($paciente, 202);
		}else{
			return response()->json($paciente, 200);
		}
        
	}

}
