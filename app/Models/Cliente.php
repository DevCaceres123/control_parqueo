<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $fillable = ['nombre_completo', 'ci', 'telefono', 'direccion'];

    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'cliente_id');
    }
}

//class Cliente extends Model
//{
    //
//}
