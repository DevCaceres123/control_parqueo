<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Boleta;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vehiculo extends Model
{
    protected $table = "vehiculos";
    use SoftDeletes;


    /**
     * Relacion reversa con persona
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function persona(){
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }



    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'vehiculo_id', 'id');
    }
}
