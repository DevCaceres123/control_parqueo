<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Boleta;

class Tarifas extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'nombre',
        'precio',
        'estado',
    ];


     public function boletas(){
       return $this->hasMany(Boleta::class, 'boleta_id', 'id');
    }
}
