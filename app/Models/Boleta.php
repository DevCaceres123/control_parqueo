<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\SoftDeletes;
class Boleta extends Model
{
    //
    use SoftDeletes;
    protected $table= 'boletas';
    
    public function vehiculo(){
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'id');
    }


}
