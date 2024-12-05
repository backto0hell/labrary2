<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'Логин' => $this->username,
            'Почта' => $this->email,
        ];
    }
}
