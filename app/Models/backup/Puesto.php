<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;


class Puesto extends Model
{
    protected $table = "puestos";
    protected $fillable = ["nombre","estado"];

    protected function nombre(): Attribute
    {
        return new Attribute(
            set: fn($value) => mb_strtoupper($value),
            get: fn($value) => mb_strtoupper($value),
        );
    }

    public function users(){
        return $this->belongsToMany('App\Models\User','historial_puesto','puesto_id','usuario_id')
        ->withPivot('created_at','descripcion_edicion')
        ->withTimestamps();
        
    }


}
