<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\DTO\RoleDTO;

class CreatePermissionRequest extends FormRequest
{

    public function authorize()
    {
        return Auth::check();
    }
    public function rules()
    {
        return [
            'name' => 'required|unique:permissions,name',
            'code' => 'required|unique:permissions,code',
            'description' => 'nullable',
        ];
    }
    public function toDTO()
    {
        return new RoleDTO(
            null,
            $this->name,
            $this->code,
            $this->description
        );
    }
}
