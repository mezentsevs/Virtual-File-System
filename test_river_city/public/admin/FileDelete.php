<?php

require('../../vendor/autoload.php');

use includes\MySQLDatabase;
use includes\File;

$database = new MySQLDatabase();

if (!isset($_GET['id'])) {
    
    // Id не был передан
    redirectTo('../index.php');

} else {
    
    // Id был передан
    // Обработка
    $file = File::findById((int)$_GET['id']);

    if ($file && $file->delete()) {
        
        // Успех
        redirectTo('../index.php?type=folder&id='.$file->fid);

    } else {
        
        // Неудача
        redirectTo('../index.php');
    }
}
?>
