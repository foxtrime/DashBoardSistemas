<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model 
{
    protected $connection = 'mysql_sisseg'; //altera para conectar no outro banco
    protected $table = "secretarias";

    protected $fillable =[
    	'nome',
        'sigla',
        'email',
        'qtd_combustivel',
    ];

    public function veiculos()
    {
        return $this->hasMany('App\Models\Veiculo');
    } 

    public function abastecimentos()
    {
        return $this->hasMany('App\Models\Abastecimento');
    } 

    public function usuarios()
    {
        return $this->hasMany('App\Models\User');
    } 
    
    public function viagens()
    {
        return $this->hasMany('App\Models\Viagem');
    } 
}
