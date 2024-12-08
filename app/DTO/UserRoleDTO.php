<?php

namespace App\DTO;

class UserRoleDTO
{
    public $user_id;
    public $role_id;
    public $created_by;
    public $deleted_by;

    public function __construct($user_id, $role_id, $created_by, $deleted_by)
    {
        $this->user_id = $user_id;
        $this->role_id = $role_id;
        $this->created_by = $created_by;
        $this->deleted_by = $deleted_by;
    }

    public static function fromModel($userRole)
    {
        return new self(
            $userRole->user_id,
            $userRole->role_id,
            $userRole->created_by,
            $userRole->deleted_by
        );
    }
}
