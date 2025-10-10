<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Contacto extends Model
{
    use HasFactory;
    protected $fillable = ['telefono'];
    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'contacto_id', 'id');
    }
}
