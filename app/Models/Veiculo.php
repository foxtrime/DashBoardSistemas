<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model 
{
    protected $connection = 'mysql_sgf';

    protected $dates = ['deleted_at'];

    protected $table = "veiculos";

    protected $fillable =[
        'placa',
        'modelo',
        'tipo',
        'cor',
        'ano',
        'renavam',
        'combustivel',
        'secretaria_id',
        'motorista_id',
        'locadora_id',
        'base_id',
        'agendavel',
        'operacional',
        'origem',

    ];

    public function secretaria()
    {
    	return $this->belongsTo('App\Models\Secretaria','secretaria_id');
    }   

    public function base()
    {
    	return $this->belongsTo('App\Models\Base','base_id');
    }   

    public function abastecimentos()
    {
        return $this->hasMany('App\Models\Abastecimento');
    } 

    public function motorista()
    {
    	return $this->belongsTo('App\Models\User','motorista_id');
    }  
    
    public function locadora()
    {
    	return $this->belongsTo('App\Models\Locadora','locadora_id');
    } 
    
    public function bdts()
    {
        return $this->hasMany('App\Models\Bdt');
    } 

    public function viagens()
    {
        return $this->hasMany('App\Models\Viagem');
    } 

    public function manutencao_itens()
    {
        return $this->belongsTo('App\Models\Manutencao_item');
    } 

    public function manutencoes()
    {
        return $this->hasMany('App\Models\Manutencao');
    } 

}
