<?php

require('../vendor/autoload.php');

use includes\MySQLDatabase;
use includes\Folder;
use includes\File;
use includes\Navigation;

$database = new MySQLDatabase();
Navigation::findSelectedElement();

if (isset($_POST['submit'])) {
    
    // Обработка формы текущего файла:
    
    $file = new File();

    $file->id = $currentFile->id;
    $file->fid = $currentFile->fid;
    $file->name = trim($_POST['currentFileName']);
    $file->content = trim($_POST['currentFileContent']);
    $file->size = strlen($file->content);

    // Проверка наличия имени файла:
    if ($file->name != '') {

        // Сохранение файла:
        if ($file->save()) {
            
            // Успех
            redirectTo('index.php?type=file&id='.$currentFile->id);
        }
    } else {

        // Имя файла не прошло валидацию
        redirectTo('index.php?type=file&id='.$currentFile->id);
    }

}

// Параметр уникальности имени:
(isset($_GET['unique']) && ($_GET['unique'] == 'false')) ?
    $unique = false : $unique = true;

?>
<?php includeLayoutTemplate("header.php"); ?>
<?php

// Вывод предупреждения уникальности имени:
if (!$unique) {
    echo '<script>alert("Такое имя уже существует!");</script>';
}
?>
<nav>
    <?php echo Navigation::getNavigationTree(
        Folder::findAll(),
        $currentFolder,
        $currentFile
        );
    ?>
</nav>
<main>
    <?php
        if (isset($currentFolder)) {
            
            // Вывод текущей папки:
            echo '<header class="main-header">';
            echo '<h2>'.htmlentities($currentFolder->name).'</h2>';
            echo '<p>';
            echo $currentFolder->showStatistics()['filesSize'].', ';
            echo $currentFolder->showStatistics()['foldersCount'].', ';
            echo $currentFolder->showStatistics()['filesCount'];
            echo '</p>';
            echo '</header>';

    ?>
            <!-- Кнопка - создать папку: -->
            <form id="formFolderCreate" action="admin/FolderCreate.php" method="post">
                <input type="hidden" name="id" value="<?php echo $currentFolder->id; ?>">
                <input type="hidden" id="folderName" name="name">
                <div>
                    <input id="folder-create" type="submit" name="submit" value=""
                        onclick="setNameByPrompt('folderName', 'Введите имя папки');">
                    <br>
                    <label for="folder-create">Создать папку</label>
                </div>
            </form>

            <!-- Кнопка - создать файл: -->
            <form id="formFileCreate" action="admin/FileCreate.php" method="post">
                <input type="hidden" name="id" value="<?php echo $currentFolder->id; ?>">
                <input type="hidden" id="fileName" name="name">
                <div>
                    <input id="file-create" type="submit" name="submit" value=""
                        onclick="setNameByPrompt('fileName', 'Введите имя файла');">
                    <br>
                    <label for="file-create">Создать файл</label>
                </div>
            </form>

    <?php        
            if ($currentFolder->pid != 0) {
                // Ссылка - удалить папку:
                echo '<a id="folder-delete" href="admin/FolderDelete.php?id=';
                echo urlencode($currentFolder->id);
                echo '"';
                echo " onclick=\"return confirm('Вы уверены?');\"";
                echo '>';
                echo '</a>';
            }

        } elseif (isset($currentFile)) {

    ?>
            <!-- Форма обновления текущего файла: -->
            <form id="formFileUpdate" action="index.php?type=file&id=
                <?php echo urlencode($currentFile->id); ?>" method="post">
                <header class="main-header">
                    <h2>
                        <input id="currentFileName" type="text" name="currentFileName"
                        value="<?php echo htmlentities($currentFile->name); ?>">
                    </h2>
                    <p><?php echo sizeAsText($currentFile->size); ?></p>
                </header>
                <textarea name="currentFileContent" rows="30" cols="75"><?php echo htmlentities($currentFile->content); ?></textarea><br>
                <input id="file-update" type="submit" name="submit" value="">
            </form>

    <?php

            // Ссылка - удалить файл:
            echo '<a id="file-delete" href="admin/FileDelete.php?id=';
            echo urlencode($currentFile->id);
            echo '"';
            echo " onclick=\"return confirm('Вы уверены?');\"";
            echo '>';
            echo '</a>';

        } else {
            
            // Вывод приветствия:
            echo '<header class="main-header">';
            echo '<h2>Добро пожаловать!</h2>';
            echo '<p>Пожалуйста, выберите папку или файл.</p>';
            echo '</header>';
        }
    ?>
</main>
<?php includeLayoutTemplate("footer.php"); ?>
