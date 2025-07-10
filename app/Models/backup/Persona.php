<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = "personas";
    protected $fillable = ["ci","nombres", "ap_paterno", "ap_materno", "estado"];
}
