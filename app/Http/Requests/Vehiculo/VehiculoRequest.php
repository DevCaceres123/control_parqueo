<?php

namespace App\Http\Requests\Vehiculo;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Validation\Rule;

class VehiculoRequest extends BasePrincipalRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        switch ($routeName) {
            case 'vehiculos.store':
                return [
                    'nombre' => 'required|max:50|min:3',
                    'descripcion_vehiculo' => 'required|max:100|min:5',
                    
                ];
            case 'vehiculos.update':
                return [
                    'nombre' => 'required|max:50|min:3',
                    'descripcion_vehiculo' => 'required|max:100|min:5',
                    
                ];
            default:
                return [];
        }
    }
}
