<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResumenDiario extends Model
{
    protected $table = 'resumen_diario';
    protected $primaryKey = 'id_resumen';
    protected $fillable = ['fecha', 'total_vehiculos', 'total_ingresos', 'total_multas', 'creado_por'];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}


//class ResumenDiario extends Model
//{
    //
//}
