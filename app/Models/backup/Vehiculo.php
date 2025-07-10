<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Persona;
use App\Models\Color;
use App\Models\TipoVehiculo;

class Vehiculo extends Model
{
    protected $table = "vehiculos";
    protected $fillable = ["placa", "descripcion","persona_id", "color_id", "tipovehiculo_id"];


    /**
     * Relacion reversa con persona
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function persona(){
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }

    /**
     * Relacion reversa con color
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'id');
    }

    /**
     * Relacion reversa con tipo vehiculo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tipo_vehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipovehiculo_id', 'id');
    }
}
