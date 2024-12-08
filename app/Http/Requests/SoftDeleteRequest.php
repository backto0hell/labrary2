<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SoftDeleteRequest extends FormRequest
{
    public function rules()
    {
        return ['permission_id' => 'array|exists:permissions,id',];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors(),], 422));
    }
    public function messages()
    {
        return ['permission_id.array' => 'The permission ids must be an array.', 'permission_id.exists' => 'Some of the permission ids do not exist.',];
    }
}
