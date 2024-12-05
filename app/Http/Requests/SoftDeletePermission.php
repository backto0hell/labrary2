<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SoftDeletePermission extends FormRequest
{
    public function rules()
    {
        return [
            'permission_ids' => 'array|exists:permissions,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
        ], 422));
    }

    public function messages()
    {
        return [
            'permission_ids.array' => 'The permission ids must be an array.',
            'permission_ids.exists' => 'Some of the permission ids do not exist.',
        ];
    }
}
