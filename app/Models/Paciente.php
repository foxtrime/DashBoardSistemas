<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    

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
