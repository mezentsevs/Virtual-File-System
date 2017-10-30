<?php

require('../../vendor/autoload.php');

use includes\MySQLDatabase;
use includes\Folder;
use includes\File;

$database = new MySQLDatabase();

if (!isset($_GET['id'])) {
    
    // Id не был передан
    redirectTo('../index.php');

} else {
    
    // Id был передан
    // Обработка
    $folder = Folder::findById((int)$_GET['id']);
    if ($folder) {
        
        // Поиск всех вложенных папок
        $allChildrensFolders = Folder::getAllChildren(
            Folder::findAll(),
            (int)$_GET['id']
        );
        
        // Удаление всех вложенных папок
        if ($allChildrensFolders) {
            foreach ($allChildrensFolders as $childsFolder) {
                
                // Поиск всех вложенных файлов
                $files = File::findFilesByFid($childsFolder->id);
                
                // Удаление всех вложенных файлов
                if ($files) {
                     foreach ($files as $file) {
                        $file->delete();
                    }
                }
                $childsFolder->delete();
            }
        }
        
        // Поиск файлов папки
        $foldersFiles = File::findFilesByFid($folder->id);
        
        // Удаление файлов папки
        if ($foldersFiles) {
            foreach ($foldersFiles as $foldersFile) {
            $foldersFile->delete();
            }
        }
        
        // Удаление папки
        $folder->delete();
        
        redirectTo('../index.php?type=folder&id='.$folder->pid);

    } else {
        
        // Папка не найдена в базе данных
        redirectTo('../index.php');
    }
}
?>
