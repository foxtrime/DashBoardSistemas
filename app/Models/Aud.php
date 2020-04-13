<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aud extends Model 
{
    
    protected $table = 'auds';

    protected $fillable =[

        'user_id',
        'type',
        'description',
    ];

}
