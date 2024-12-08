<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Role;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\DTO\RoleDTO;

class CreateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'code' => 'required|string|unique:roles,code'
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
        return new RoleDTO(
            null,
            $this->input('name'),
            $this->input('description'),
            $this->input('code'),
            $this->user()->id,
            null,
        );
    }
}
