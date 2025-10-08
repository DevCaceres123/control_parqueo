<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'contacto_id', 'id');
    }
}
