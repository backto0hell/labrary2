<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class PermissionCollectionDTO
{
    public $permissions;

    public function __construct(Collection $permissions)
    {
        $this->permissions = $permissions->map(function ($permission) {
            return PermissionDTO::fromModel($permission);
        });
    }

    public static function fromCollection(Collection $permissions)
    {
        return new self($permissions);
    }
}
