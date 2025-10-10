<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Color extends Model
{
    use SoftDeletes;
    protected $fillable = ['nombre', 'color'];
    protected $table='colores';
    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'contacto_id', 'id');
    }
}
