<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acompanhamento extends Model 
{
    
    protected $connection = 'mysql_mec'; //altera para conectar no outro banco

    protected $fillable =[
         'ativo',
         'atendimento_domiciliar',
         'ad_admissao',
         'origem',
         'base',

         'observacao',
         'paciente_id',
         'cid_id',
         'dt_inicio',
         'dt_termino',
         'created_at',
         'updated_at',

         'medicina',
         'enfermagem',
         'fisioterapia',
         'fonoaudiologia',
         'nutricao',
         'psicologia',
         'odontologia',
         'servico_social',
    ];

    public function paciente()
    {
        return $this->belongsTo('App\Models\Paciente');
    }

    public function visitas()
    {
        return $this->hasMany('App\Models\Visita');
    }

    public function ultima_visita()
    {
        //return $this->hasMany('App\Models\Visita')->latest('dt_visita')->first();
        //return $this->hasMany('App\Models\Visita')->orderBy('dt_visita','DESC')->take(1);

        $ultima = $this->hasMany('App\Models\Visita')->orderBy('dt_visita','DESC');
        return $ultima;

    }
    
    public function primeira_visita()
    {
        $primeira = $this->hasMany('App\Models\Visita')->orderBy('dt_visita','ASC');
        $a = $primeira;

        return $a;
    }
 
  
    
    public function cid()
    {
        return $this->belongsTo('App\Models\Cid');
    }

}
