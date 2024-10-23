<?php

namespace App\Http\Requests;
use App\Http\Resources\RegisterResource;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|regex:/^[A-Z][a-zA-Z]{6,}$/|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/[0-9]/|regex:/[@$!%*?&#]/|regex:/[A-Z]/|regex:/[a-z]/',
            'password_confirmation' => 'required|same:password',
            'birthday' => 'required|date_format:Y-m-d',
        ];
    }
    public function toResource()
    {
        return new RegisterResource($this);
    }

}
