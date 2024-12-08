<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\DTO\RoleDTO;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = $this->route('role');

        return [
            'name' => 'required|string|unique:roles,name,' . ($role ? $role->id : 'null'),
            'description' => 'nullable|string',
            'code' => 'required|string|unique:roles,code,' . ($role ? $role->id : 'null')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }


    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name must be unique.',
            'code.required' => 'The code field is required.',
            'code.unique' => 'The code must be unique.'
        ];
    }

    public function toDTO(): RoleDTO
    {
        $role = $this->route('role');

        return new RoleDTO(
            $role ? $role->id : null,
            $this->input('name'),
            $this->input('description'),
            $this->input('code'),
            $role ? $role->created_by : null,
            $role ? $role->deleted_by : null
        );
    }
}
