<?php

namespace includes;

/**
 * File - класс для работы с файлами.
 */
class File extends DatabaseObject
{
    protected static $tableName = "files";
    protected static $dbFields = array('id', 'fid', 'name', 'content', 'size');

    public $id;
    public $fid;
    public $name;
    public $content;
    public $size;

    /**
     * Поиск файлов по fid(folder id) и сортировка по name.
     */
    public static function findFilesByFid($fid)
    {
        global $database;
        $sql  = "SELECT * FROM ".self::$tableName;
        $sql .= " WHERE fid=".$database->escapeValue($fid);
        $sql .= " ORDER BY name ASC";
        return self::findBySql($sql);
    }

    /**
     * Проверка уникальности имени.
     * @return boolean
     */
    public static function checkUniqueName($fid, $name)
    {
        $namesArray = [];

        $folderFiles = self::findFilesByFid($fid);
        foreach ($folderFiles as $file) {
            $namesArray[] = $file->name;
        }

        if ($namesArray) {
            if (in_array($name, $namesArray)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
}
?>
