<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function rules()
    {
        $userId = $this->user()->id; // Получаем ID текущего пользователя для исключения из уникальности

        return [
            'username' => 'nullable|string|unique:users,username,' . $userId . '|regex:/^(?![0-9]+$).+$/',
            'email' => 'nullable|string|email|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string|same:password',
            'birthday' => 'nullable|date_format:Y-m-d',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function authorize()
    {
        return true;
    }
}
