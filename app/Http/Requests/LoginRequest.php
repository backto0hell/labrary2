<?php

namespace App\Http\Requests;
use App\Http\Resources\AuthResource;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }
    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/^[A-Z][a-zA-Z]{6,}$/',
            'password' => 'required|string|min:8|regex:/[0-9]/|regex:/[@$!%*?&#]/|regex:/[A-Z]/|regex:/[a-z]/',
        ];
    }
    public function toResource()
{
    return new AuthResource($this);
}

}
