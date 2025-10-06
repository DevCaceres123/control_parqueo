<?php

namespace App\Helpers;

use Hashids\Hashids;

class HashidsHelper
{
    protected $hashids;

    public function __construct()
    {
        // Usamos APP_KEY para mantener un valor único por proyecto
        $this->hashids = new Hashids(env('APP_KEY'), 10);
    }

    public function encode($id)
    {
        return $this->hashids->encode($id);
    }

    public function decode($hash)
    {
        $decoded = $this->hashids->decode($hash);
        return $decoded[0] ?? null;
    }
}
