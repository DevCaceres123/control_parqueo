<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehiculo;
class Boleta extends Model
{
    //
    protected $table= 'boletas';
    
    public function vehiculo(){
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'id');
    }


}
