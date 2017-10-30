<?php

namespace includes;

/**
 * Folder - класс для работы с папками.
 */
class Folder extends DatabaseObject
{
    protected static $tableName = "folders";
    protected static $dbFields = array('id', 'pid', 'name');

    public $id;
    public $pid;
    public $name;

    /**
     * Поиск всех папок и сортировка по pid(parent id).
     */
    public static function findAllFolders()
    {
        global $database;
        $sql  = "SELECT * FROM ".self::$tableName;
        $sql .= " ORDER BY pid ASC";
        return self::findBySql($sql);
    }

    /**
     * Поиск папок по pid(parent id) и сортировка по name.
     */
    public static function findFoldersByPid($pid=0)
    {
        global $database;
        $sql  = "SELECT * FROM ".self::$tableName;
        $sql .= " WHERE pid=".$database->escapeValue($pid);
        $sql .= " ORDER BY name ASC";
        return self::findBySql($sql);
    }

    /**
     * Получение массива с id всех родительских папок.
     */
    public static function getAllParentsId($family, $id)
    {
        // Объявление и инициализация:
        static $allParents;
        isset($allParents) ? null : $allParents = [];
        static $currentMember;
        isset($currentMember) ? null : $currentMember = self::findById($id);

        // Получение родителя текущей папки:
        foreach ($family as $member) {
            if ($member->id == $currentMember->pid) {
                $allParents[] = $member->id;
                $currentMember = clone $member;
                self::getAllParentsId($family, $currentMember->id);
            }
        }
        return $allParents;
    }

    /**
     * Получение массива со всеми дочерними папками.
     */
    public static function getAllChildren($family, $id)
    {
        // Объявление и инициализация:
        $currentСhildren = [];
        static $allChildren;
        isset($allChildren) ? null : $allChildren = [];
        static $currentMember;
        isset($currentMember) ? null : $currentMember = self::findById($id);

        // Получение детей текущей папки:
        foreach ($family as $member) {
            if ($member->pid == $currentMember->id) {
                $currentСhildren[] = $member;
            }
        }

        // Рекурсивный вывод в массив:
        if ($currentСhildren) {
            foreach ($currentСhildren as $child) {
                    $allChildren[] = $child;
                    $currentMember = clone $child;
                    self::getAllChildren($family, $currentMember->id);
            }
        }

        return $allChildren;
    }

    /**
     * Показ статистики.
     */
    public function showStatistics() {

        $foldersCount = 0;
        $filesCount = 0;
        $filesSize = 0;
        $allFiles = [];

        // Поиск файлов в текущей папке:
        $currentFolderFiles = File::findFilesByFid($this->id);
        if ($currentFolderFiles) {
            $allFiles = $currentFolderFiles;
        }

        // Поиск всех дочерних папок:
        $allFolders = self::getAllChildren(self::findAll(), $this->id);
        if ($allFolders) {
            foreach ($allFolders as $folder) {
                $files = File::findFilesByFid($folder->id);
                $allFiles = arraySum($allFiles,$files);
            }
            
            // Подсчет папок:
            $foldersCount = count($allFolders);
        }
        
        if ($allFiles) {

            // Подсчет файлов:
            $filesCount = count($allFiles);

            foreach ($allFiles as $file) {
                
                // Определение общего размера файлов:
                $filesSize += $file->size;
            }
        }

        // Варианты окончаний для папок:
        $foldersCountEndings = getNumEnding(
            $foldersCount,
            ['папка', 'папки', 'папок']
        );

        // Варианты окончаний для файлов:
        $filesCountEndings = getNumEnding(
            $filesCount,
            ['файл', 'файла', 'файлов']
        );

        return [
            'foldersCount' => $foldersCount.' '.$foldersCountEndings,
            'filesCount' => $filesCount.' '.$filesCountEndings,
            'filesSize' => sizeAsText($filesSize)
            ];
    }

    /**
     * Проверка уникальности имени.
     * @return boolean
     */
    public static function checkUniqueName($pid, $name)
    {
        $namesArray = [];

        $childrenFolders = self::findFoldersByPid($pid);
        foreach ($childrenFolders as $folder) {
            $namesArray[] = $folder->name;
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
