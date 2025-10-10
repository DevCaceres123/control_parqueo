<?php

namespace Database\Factories;

use App\Models\Contacto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactoFactory extends Factory
{
    protected $model = Contacto::class;

    public function definition(): array
    {
        return [           
            'telefono' => $this->faker->phoneNumber(),            
        ];
    }
}
