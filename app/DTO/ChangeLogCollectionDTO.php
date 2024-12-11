<?php

namespace App\DTO;

class ChangeLogCollectionDTO
{
    public $logs;

    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }
}
