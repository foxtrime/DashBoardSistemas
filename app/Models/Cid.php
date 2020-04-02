<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cid extends Model 
{
    protected $connection = 'mysql_mec'; //altera para conectar no outro banco
    
    public $timestamps = false;
   
    protected $fillable =[
        'codigo',
        'descricao',
    ];

    public function acompanhamentos()
    {
        return $this->hasMany('App\Models\Acompanhamento');
    }
}
