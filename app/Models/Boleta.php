<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehiculo;
use App\Models\Tarifas;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class Boleta extends Model implements Auditable
{
    //
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes,HasFactory;
    protected $table= 'boletas';
    
    public function vehiculo(){
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'id');
    }

     
    public function contacto(){
        return $this->belongsTo(Contacto::class, 'contacto_id', 'id');
    }


        
    public function color(){
        return $this->belongsTo(Color::class, 'contacto_id', 'id');
    }


    public function tarifa(){
        return $this->belongsTo(Tarifas::class, 'boleta_id', 'id');
    }

}
