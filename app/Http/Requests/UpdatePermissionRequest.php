<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\DTO\PermissionDTO;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $permissionId = $this->route('permission') ? $this->route('permission')->id : null;

        return [
            'name' => 'required|string|unique:permissions,name,' . $permissionId,
            'description' => 'nullable|string',
            'code' => 'required|string|unique:permissions,code,' . $permissionId
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

    public function toDTO(): PermissionDTO
    {
        return new PermissionDTO(
            $this->route('permission')->id,
            $this->input('name'),
            $this->input('description'),
            $this->input('code'),
            $this->route('permission')->created_by,
            $this->route('permission')->deleted_by
        );
    }
}
