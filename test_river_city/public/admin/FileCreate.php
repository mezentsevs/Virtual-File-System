<?php

require('../../vendor/autoload.php');

use includes\MySQLDatabase;
use includes\File;

$database = new MySQLDatabase();

// Форма была отправлена
if (isset($_POST['submit'])) {
    
    // Проверка наличия имени файла:
    if ($_POST['name'] != '') {
        
        // Проверка на уникальность имени:
        if (!File::checkUniqueName((int)$_POST['id'], trim($_POST['name']))) {
            redirectTo('../index.php?type=folder&id='.$_POST['id'].'&unique=false');
        }

        // Имя не пустое
        // Обработка формы
        $file = new File();
        $file->fid = (int)$_POST['id'];
        $file->name = trim($_POST['name']);
        $file->content = '';
        $file->size = 0;
        
        if ($file->save()) {
            
            // Успех
            redirectTo('../index.php?type=folder&id='.$_POST['id']);

        } else {
            
            // Неудача
            redirectTo('../index.php');
        }
    } else {
        
        // Имя файла не прошло валидацию
        // Имя пустое
        redirectTo('../index.php');
    }
} else {
    
    // Вероятно, это GET запрос
    redirectTo('../index.php');
}

?>
