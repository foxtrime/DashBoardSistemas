<?php

namespace App\Imports;

use App\Models\Paciente;
use App\Models\Cid;

use Maatwebsite\Excel\Concerns\ToModel;

class PacientesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //$cid = Cid::where('codigo',$row[8])->first()->id;

        return new Paciente([
            'prontuario'                => $row[0],
            'created_at'                => $row[1],
            'nascimento'                => $row[2],
            'nome'                      => $row[3],
            'sus'                       => $row[4],
            'logradouro'                => $row[5],
            'bairro'                    => $row[6],
            'base'                      => $row[7],
            'cid_id'                    => $row[8],
            'telefone1'                 => $row[9],
            'telefone2'                 => $row[9],
            'telefone3'                 => $row[9],
            'observacao'                => $row[11],
          
        ]);
    }
}
