<?php

namespace App\Http\Requests\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BasePrincipalRequest;

class TarifaRequest extends BasePrincipalRequest
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
            case 'tarifas.store':
                return [
                    'nombre' => 'required|max:50|min:3',                    
                    'tarifa' => 'required|integer|digits_between:1,2|min:1',
                ];
            case 'tarifas.update':
                return [
                    'nombre' => 'required|max:50|min:3',                    
                    'tarifa' => 'required|integer|digits_between:1,2|min:1',
                ];
            default:
                return [];
        }
    }
}
