<?php

namespace App\DTO;

class ChangeLogDTO
{
    public $id;
    public $entity_type;
    public $entity_id;
    public $changed_properties;
    public $mutated_by;
    public $created_by;
    public $created_at;

    public function __construct($id, $entity_type, $entity_id, $old_value, $new_value, $mutated_by, $created_by, $created_at)
    {
        $this->id = $id;
        $this->entity_type = $entity_type;
        $this->entity_id = $entity_id;
        $this->changed_properties = $this->getChangedProperties($old_value, $new_value);
        $this->mutated_by = $mutated_by;
        $this->created_by = $created_by;
        $this->created_at = $created_at;
    }

    private function getChangedProperties($old_value, $new_value)
    {
        // Декодируем значения. Если они некорректны, заменяем на пустой массив.
        $old_data = $this->safeJsonDecode($old_value);
        $new_data = $this->safeJsonDecode($new_value);

        // Собираем изменённые свойства.
        $changed_properties = [];
        foreach ($new_data as $key => $value) {
            if (!array_key_exists($key, $old_data) || $old_data[$key] !== $value) {
                $changed_properties[$key] = [
                    'old' => $old_data[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changed_properties;
    }

    private function safeJsonDecode($json)
    {
        // Декодируем JSON и возвращаем массив, либо пустой массив при ошибке.
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : [];
    }
}
