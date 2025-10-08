<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boleta extends Model
{
    //
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

}
