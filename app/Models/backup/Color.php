<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = "colores";
    protected $fillable = ["nombre", "color"];
    protected function nombre(): Attribute
    {
        return new Attribute(
            set: fn($value) => mb_strtoupper($value),
            get: fn($value) => mb_strtoupper($value),
        );
    }
}
