<?php

namespace App\DTO;

class ChangeLogDTO
{
    public $entity_type;
    public $entity_id;
    public $changed_properties;
    public $mutated_by;
    public $created_by;
    public $created_at;

    public function __construct($entity_type, $entity_id, $old_value, $new_value, $mutated_by, $created_by, $created_at)
    {
        $this->entity_type = $entity_type;
        $this->entity_id = $entity_id;
        $this->changed_properties = $this->getChangedProperties($old_value, $new_value);
        $this->mutated_by = $mutated_by;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
    }

    private function getChangedProperties($old_value, $new_value)
    {
        $old_data = json_decode($old_value, true);
        $new_data = json_decode($new_value, true);

        $changed_properties = [];

        foreach ($new_data as $key => $value) {
            if (!isset($old_data[$key]) || $old_data[$key] !== $new_data[$key]) {
                $changed_properties[$key] = [
                    'old' => $old_data[$key] ?? null,
                    'new' => $new_data[$key]
                ];
            }
        }

        return $changed_properties;
    }
}
