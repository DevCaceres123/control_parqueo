<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $fillable = ['parametro', 'valor'];
}

//class Configuracion extends Model
//{
    //
//}
