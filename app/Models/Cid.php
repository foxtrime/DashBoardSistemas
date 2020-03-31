<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Cid extends Model  implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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
