<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Config_atraso extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table= 'config_atraso';
}
