<?php

namespace App\Http\Requests;

use App\Http\Resources\AuthResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    //Валидация данных для входа
    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }

    public function toResource()
    {
        return new AuthResource($this);
    }
}
