<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Cid;

class CidController extends Controller
{
    public function index()
    {
        $cids = Cid::all();
        return view ('cid.index', compact('cids'));    
    }


    public function tabela()
	{
		$cids = Cid::get();
		
		// Criar o objeto da coleção que será usado para o dataTables
		$colecao = collect();

		// Iterar pelos currículos e montar cada linha da tabela
		foreach($cids as $cid)
		{
			
			$colecao->push([
				'codigo'				=> $cid->codigo,
				'descricao'				=> $cid->descricao,
			]);
			
		}

        // Retornar a tabela pronta
        return Datatables::of($colecao)->make(true);
    }

}
