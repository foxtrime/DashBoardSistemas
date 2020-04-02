<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sistema extends Model  
{
	
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
