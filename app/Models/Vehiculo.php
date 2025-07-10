<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculo';
    protected $fillable = ['placa', 'tipo_vehiculo_id', 'hora_entrada', 'hora_salida', 'estado'];

    public function tipo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'vehiculo_id');
    }
}


//class Vehiculo extends Model
//{
    //
//}
