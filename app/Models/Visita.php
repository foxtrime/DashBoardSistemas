<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Visita extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $fillable =[
        'acompanhamento_id',
        'motivo',
        'dt_visita',
        'situacao',
        'observacao',
        'destino_pos_alta',
        'tp_atendimento',
        'created_at',
        'updated_at',
    ];

    public function acompanhamento()
    {
        return $this->belongsTo('App\Models\Acompanhamento');
    }

    public function servidores()
    {
        return $this->belongsToMany('App\Models\Servidor');
    }
}
