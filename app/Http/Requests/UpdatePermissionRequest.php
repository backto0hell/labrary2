<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePermissionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|unique:permissions,name,' . $this->permission->id,
            'code' => 'required|unique:permissions,code,' . $this->permission->id,
            'description' => 'nullable',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function toDTO()
    {
        return new RoleDTO(
            $this->role->id,
            $this->name,
            $this->code,
            $this->description
        );
    }
}
