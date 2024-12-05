<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\DTO\RoleDTO;


class CreateRoleRequest extends FormRequest
{

    public function authorize()
    {
        return Auth::check();
    }
    public function rules()
    {
        return [
            'name' => 'required|unique:roles',
            'code' => 'required|unique:roles',
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
