<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = ['telefono'];
    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'contacto_id', 'id');
    }
}
