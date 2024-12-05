<?php

namespace App\DTO;

class RoleDTO
{
    public $id;
    public $name;
    public $code;
    public $description;

    public function __construct($id, $name, $code, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
    }
}
