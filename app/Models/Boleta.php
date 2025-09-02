<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehiculo;
class Boleta extends Model
{
    //

    public function vehiculo(){
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'id');
    }


}
