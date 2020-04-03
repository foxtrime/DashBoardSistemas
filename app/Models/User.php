<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

use Session;



class User extends Authenticatable implements AuditableContract
{
    use Notifiable;
    use \OwenIt\Auditing\Auditable;

    protected $connection = 'mysql_sisseg'; //altera para conectar no outro banco
    protected $table = "funcionarios";      //substitui o nome da tabela que tem as credenciais

    
    protected $fillable = [
        'name', 
        'email', 
        'password',
        'status',
        'cpf',
        'secretaria_id',
        'avatar',
        'motorista',
        'celular',
        'cnh',
        'categoria_cnh',
        'validade_cnh',
        'ativo',
        'matricula'
    ];

    public function roles()
    {
      return $this->belongsToMany('App\Models\Role','funcionario_role','funcionario_id');
    }
  

    public function hasRole($role){
        //recupera o modulo que está na session
        $modulo = session()->all()['modulo'];

        $retorno = DB::connection('mysql_sisseg')->select("select consulta_role($this->id , '$modulo', '$role') as retorno");

        if ( $retorno[0]->retorno ){
            return true;
        }
        return false;
    }
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
