<?php

namespace App\DTO;

class RolePermissionDTO
{
    public $role_id;
    public $permission_id;
    public $created_by;
    public $deleted_by;

    public function __construct($role_id, $permission_id, $created_by, $deleted_by)
    {
        $this->role_id = $role_id;
        $this->permission_id = $permission_id;
        $this->created_by = $created_by;
        $this->deleted_by = $deleted_by;
    }

    public static function fromModel($rolePermission)
    {
        return new self(
            $rolePermission->role_id,
            $rolePermission->permission_id,
            $rolePermission->created_by,
            $rolePermission->deleted_by
        );
    }
}
