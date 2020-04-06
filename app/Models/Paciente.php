<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    
    protected $connection = 'mysql_mec'; //altera para conectar no outro banco


    protected $fillable =[
           'dt_cadastro',
           'prontuario',
           'situacao',
           'cpf',
           'identidade',
           'org_exp_idt',
           'nome',
           'nascimento',
           'sexo',
           'cuidador',
           'sus',
           'latitude',
           'longitude',
           'bairro',
           'logradouro',
           'numero',
           'complemento',
           'cep',
           'telefone1',
           'telefone2',
           'telefone3',
           'internado',
           'obito',
           'dt_obito',
           'observacao',
           'created_at',
           'updated_at',
           'deleted_at',
    ];

    public function acompanhamentos()
    {
        return $this->hasMany('App\Models\Acompanhamento');
    }
}
