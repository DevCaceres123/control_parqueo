<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = 'tipo_vehiculo';
    protected $primaryKey = 'id_tipo';
    protected $fillable = ['nombre', 'tarifa_hora', 'tarifa_dia'];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipo_vehiculo_id');
    }
}

//class TipoVehiculo extends Model
//{
    //
//}
