<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Sistema extends Model  implements Auditable
{
	use \OwenIt\Auditing\Auditable;

	protected $connection = 'mysql_sisseg'; //altera para conectar no outro banco
	protected $table = "sistemas";      //substitui o nome da tabela que tem as credenciais

	
	protected $fillable =[
		'nome',
		'ativo'
	];

	public function roles()
 	{
		return $this->hasMany('App\Models\Role');
 	}

   


}
