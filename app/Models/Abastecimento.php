<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abastecimento extends Model 
{
    protected $connection = 'mysql_sgf'; //altera para conectar no outro banco

    protected $dates = ['deleted_at'];

    protected $table = 'abastecimentos';

    protected $fillable =[

        'veiculo_id',
        'posto_id',
        'secretaria_id',
        'serie_nf',
        'nota_fiscal',
        'talao',
        'talao_antigo',
        'odometro',
        
        'qtd', 
        'valor_comb',
        'valor_total',
        'combustivel',
        'data',
        'observacao',
        'created_at',
    ];

    public function veiculo()
    {
    	return $this->belongsTo('App\Models\Veiculo','veiculo_id')->withTrashed();
    }   
    
    public function secretaria()
    {
        return $this->belongsTo('App\Models\Secretaria','secretaria_id');
    }   

    public function posto()
    {
    	return $this->belongsTo('App\Models\Posto','posto_id')->withTrashed();
    }   


    public function edicoes()
    {
    	return $this->hasMany('App\Models\edicao_abastecimento')->withTrashed();
    }

}
