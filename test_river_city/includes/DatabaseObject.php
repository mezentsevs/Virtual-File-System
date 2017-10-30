<?php

namespace includes;

/**
 * DatabaseObject - базовый класс для оъектов базы данных.
 */
class DatabaseObject
{
    protected static $tableName;
    protected static $dbFields;

    // Общеиспользуемые методы базы данных:

    /**
     * Поиск всех объектов.
     */
    public static function findAll()
    {
        return static::findBySql("SELECT * FROM ".static::$tableName);
    }

    /**
     * Поиск объектов по id.
     */
    public static function findById($id=0)
    {
        global $database;
        $resultArray = static::findBySql(
            "SELECT * FROM ".
            static::$tableName.
            " WHERE id={$id} LIMIT 1"
        );
        return !empty($resultArray) ? array_shift($resultArray) : false;
    }

    /**
     * Поиск объектов по sql.
     */
    public static function findBySql($sql="")
    {
        global $database;
        $resultSet = $database->query($sql);
        $objectArray = array();
        while ($row = $database->fetchArray($resultSet)) {
            $objectArray[] = static::instantiate($row);
        }
        $database->freeResult($resultSet);
        return $objectArray;
    }

    /**
     * Подсчет количества всех объектов.
     */
    public static function countAll()
    {
        global $database;
        $sql = "SELECT COUNT(*) FROM ".static::$tableName;
        $resultSet = $database->query($sql);
        $row = $database->fetchArray($resultSet);
        return array_shift($row);
    }
    
    /**
     * Инстанцирование объекта.
     */
    private static function instantiate($record)
    {
        $className = get_called_class();
        $object = new $className;
        foreach ($record as $attribute => $value) {
            if ($object->hasAttribute($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    /**
     * Проверка наличия атрибута.
     */
    private function hasAttribute($attribute)
    {
        
        // Получение ассоциативного массива с атрибутами и значениями:
        $objectVars = $this->attributes();

        // Проверка наличия ключа в массиве (true/false):
        return array_key_exists($attribute, $objectVars);
    }

    /**
     * Получение ассоциативного массива атрибутов со значениями.
     */    
    protected function attributes()
    {
        $attributes = array();
        foreach (static::$dbFields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    /**
     * Экранирование атрибутов.
     */
    protected function sanitizedAttributes()
    {
        global $database;
        $cleanAttributes = array();
        foreach ($this->attributes() as $key => $value) {
            $cleanAttributes[$key] = $database->escapeValue($value);
        }
        return $cleanAttributes;
    }

    /**
     * Сохранение.
     */
    public function save()
    {
        
        // Проверка наличия id и выбор метода:
        return isset($this->id) ? $this->update() : $this->create();
    }

    /**
     * Создание.
     */    
    public function create()
    {
        global $database;
        $attributes = $this->sanitizedAttributes();
        
        $attributesWithoutId = $attributes;
        unset($attributesWithoutId['id']);

        $sql  = "INSERT INTO ".static::$tableName." (";
        $sql .= join(", ", array_keys($attributesWithoutId));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributesWithoutId));
        $sql .= "')";
        if ($database->query($sql)) {
            $this->id = $database->insertId();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Обновление.
     */
    public function update()
    {
        global $database;
        $attributes = $this->sanitizedAttributes();

        $attributesWithoutId = $attributes;
        unset($attributesWithoutId['id']);

        $attributePairs = array();
        foreach ($attributesWithoutId as $key => $value) {
            $attributePairs[] = "{$key}='{$value}'";
        }
        $sql  = "UPDATE ".static::$tableName." SET ";
        $sql .= join(", ", $attributePairs);
        $sql .= " WHERE id=". $database->escapeValue($this->id);
        $database->query($sql);
        return ($database->affectedRows() == 1) ? true : false;
    }

    /**
     * Удаление
     */
    public function delete()
    {
        global $database;
        $sql  = "DELETE FROM ".static::$tableName;
        $sql .= " WHERE id=". $database->escapeValue($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return ($database->affectedRows() == 1) ? true : false;
    }

}
?>
