<?php

namespace includes;

/**
 * ObjSorter - класс для сортировки массива объектов.
 */
class ObjSorter
{
    var $property;
    var $sorted;

    /**
     * Сортировка массива объектов по свойству.
     * @param array of objects $objects_array
     * @param string $property
     */
    function __construct($objects_array, $property = null)
    {
        $sample = $objects_array[0];
        $vars = get_object_vars($sample);
        if (isset($property)) {
            if (isset($sample->$property)) {
                $this->property = $property;
                usort($objects_array, array($this,'_compare'));
            } else {
                $this->sorted = false;
                return;
            }
        } else {
            list($property,$var) = each($sample);
            $this->property = $property;
            usort($objects_array, array($this,'_compare'));
        }
        $this->sorted = ($objects_array);
    }

    /**
     * Функция сравнения для usort.
     */
    function _compare($apple, $orange) 
    {
        $property = $this->property;
        if ($apple->$property == $orange->$property) return 0;
        return ($apple->$property < $orange->$property) ? -1 : 1;
    }
}
?>
