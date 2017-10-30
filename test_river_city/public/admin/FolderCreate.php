<?php

require('../../vendor/autoload.php');

use includes\MySQLDatabase;
use includes\Folder;

$database = new MySQLDatabase();

// Форма была отправлена
if (isset($_POST['submit'])) {
    
    // Проверка наличия имени папки:
    if ($_POST['name'] != '') {
        
        // Проверка на уникальность имени:
        if (!Folder::checkUniqueName((int)$_POST['id'], trim($_POST['name']))) {
            redirectTo('../index.php?type=folder&id='.$_POST['id'].'&unique=false');
        }

        // Имя не пустое
        // Обработка формы
        $folder = new Folder();
        $folder->pid = (int)$_POST['id'];
        $folder->name = trim($_POST['name']);
        
        if ($folder->save()) {
            
            // Успех
            redirectTo('../index.php?type=folder&id='.$_POST['id']);

        } else {
            
            // Неудача
            redirectTo('../index.php');
        }
    } else {
        
        // Имя папки не прошло валидацию
        // Имя пустое
        redirectTo('../index.php');
    }
} else {
    
    // Вероятно, это GET запрос
    redirectTo('../index.php');
}
?>
