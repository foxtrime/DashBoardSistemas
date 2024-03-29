<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends Model  implements Auditable
{
	use \OwenIt\Auditing\Auditable;

	protected $connection = 'mysql_sisseg'; //altera para conectar no outro banco
	protected $table = "roles";      //substitui o nome da tabela que tem as credenciais

	
	protected $fillable = [
    	'acesso',
		'nome',
		'sistema_id'
    ];

    // Relacionamentos

  	public function funcionarios()
 	{
        return $this->belongsToMany('App\Models\Funcionario');
 	}
	  
	public function sistema()
 	{
		return $this->belongsTo('App\Models\Sistema');
 	}


}
