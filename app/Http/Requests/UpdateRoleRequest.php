<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\DTO\RoleDTO;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }
    public function rules()
    {
        return [
            'name' => 'nullable|unique:roles,name,' . $this->route('id'),
            'code' => 'nullable|unique:roles,code,' . $this->route('id'),
            'description' => 'nullable',
            'permission_ids' => 'nullable|array|exists:permissions,id',
        ];
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
