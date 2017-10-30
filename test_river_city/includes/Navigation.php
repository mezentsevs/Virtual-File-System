<?php

namespace includes;

/**
 * Navigation - класс для работы с навигацией.
 */
class Navigation
{
    /**
     * Поиск выбранного элемента.
     */
    public static function findSelectedElement()
    {
        global $currentFolder;
        global $currentFile;

        if (isset($_GET['type'])){
            // Тип элемента был передан
            if ($_GET['type'] == 'folder') {
            
                // Обработка папки:
                if (isset($_GET['id'])) {
                    // Id папки был передан
                    $currentFolder = Folder::findById((int)$_GET['id']);
                    $currentFile = null;

                } else {
                    // Id папки не был передан
                    $currentFolder = null;
                }

            } elseif ($_GET['type'] == 'file') {
                
                // Обработка файла:
                if (isset($_GET['id'])) {
                    // Id файла был передан
                    $currentFile = File::findById((int)$_GET['id']);
                    $currentFolder = null;

                } else {
                    // Id файла не был передан
                    $currentFile = null;
                }
            }
        } else {
            // Тип элемента не был передан
            $currentFolder = null;
            $currentFile = null;
        }
    }

    /**
     * Получение дерева навигации.
     * @param array of Folder objects $family
     * @param Folder object $currentFolder
     * @param File object $currentFile
     * @return string
     */
    public static function getNavigationTree($family, $currentFolder, $currentFile)
    {
        // Объявление и инициализация:
        isset($tree) ? null : $tree = '';
        $currentСhildren = [];
        static $currentMember;
        isset($currentMember) ? null : $currentMember = new Folder;

        // Установка метки корневой папки:
        isset($currentMember->id) ? null : $currentMember->id = '0';

        // Получение детей текущей папки:
        foreach ($family as $member) {
            if ($member->pid == $currentMember->id) {
                $currentСhildren[] = $member;
            }
        }

        //Сортировка детей по имени
        if ($currentСhildren) {
            $objects = new ObjSorter($currentСhildren, 'name');
            if ($objects->sorted) {
                $currentСhildren = $objects->sorted;
            }
        }

        // Рекурсивный вывод в дерево:
        if ($currentСhildren) {
            $tree  = '<ul>';
            if ($currentFolder) {
                $AllParentsFolderId = Folder::getAllParentsId(
                        Folder::findAll(),
                        $currentFolder->id
                    );
            } elseif ($currentFile) {
                $AllParentsFolderId = Folder::getAllParentsId(
                        Folder::findAll(),
                        $currentFile->fid
                    );
            }
            foreach ($currentСhildren as $child) {
                $currentMember = clone $child;
                $tree .= '<li>';
                $tree .= '<a';
                if (
                    ($currentFolder &&
                    in_array($child->id, $AllParentsFolderId)) ||
                    ($currentFile &&
                    (in_array($child->id, $AllParentsFolderId) ||
                    $child->id == $currentFile->fid))
                ) {
                    $tree .= ' class="folder folder-selected"';
                } elseif ($currentFolder && $child->id == $currentFolder->id) {
                    $tree .= ' class="folder folder-target"';
                } else {
                    $tree .= ' class="folder"';
                }
                $tree .= ' href="index.php?type=folder&id=';
                $tree .= urlencode($currentMember->id);
                $tree .= '">';
                $tree .= htmlentities($currentMember->name);
                $tree .= '</a>';
                $tree .= self::getNavigationTree(
                    $family,
                    $currentFolder,
                    $currentFile
                );

                // Поиск файлов в папке:
                $items = File::findFilesByFid($child->id);
                if ($items) {
                    $tree .= '<ul>';
                    foreach ($items as $item) {
                        $tree .= '<li>';
                        $tree .= '<a';
                        if ($currentFile && $item->id == $currentFile->id) {
                            $tree .= ' class="file file-target"';
                        } else {
                            $tree .= ' class="file"';
                        }
                        $tree .= ' href="index.php?type=file&id=';
                        $tree .= urlencode($item->id);
                        $tree .= '">';
                        $tree .= htmlentities($item->name);
                        $tree .= '</a>';
                        $tree .= '</li>';
                    }
                    $tree .= '</ul>';
                }
                $tree .= '</li>';
            }
            $tree .= '</ul>';
        }
        return $tree;
    }
}
?>
