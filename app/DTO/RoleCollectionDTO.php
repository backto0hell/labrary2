<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class RoleCollectionDTO
{
    public $roles;

    public function __construct($roles)
    {
        $this->roles = $roles->map(function ($role) {
            return RoleDTO::fromModel($role);
        });
    }

    public static function fromCollection(Collection $roles)
    {
        return new self($roles);
    }
}
