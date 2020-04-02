<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servidor extends Model 
{
    protected $connection = 'mysql_mec'; //altera para conectar no outro banco

    protected $table = 'servidores';

    protected $fillable =[
        'nome',
        'motivo',
        'ativo',
    ];

    public function visitas()
    {
        return $this->belongsToMany('App\Models\Visita');
    }
   
}
