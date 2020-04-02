<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model 
{
    protected $connection = 'mysql_mec'; //altera para conectar no outro banco

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
