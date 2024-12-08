<?php

namespace App\DTO;

//коллекция
class UserDTO
{
    public $id;
    public $name;
    public $email;
    public $created_at;
    public $updated_at;
    public $roles;

    //Инициализация 
    public function __construct($id, $name, $email, $created_at, $updated_at, $roles)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
        $this->roles = $roles;
    }

    //передача
    public static function fromModel($user)
    {
        return new self(
            $user->id,
            $user->name,
            $user->email,
            $user->created_at,
            $user->updated_at,
            $user->roles
        );
    }
}
