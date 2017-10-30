/**
 * Установка имени из prompt.
 * param string elementId
 * param string message
 */
function setNameByPrompt(elementId, message) {
    var name = '';
    name = prompt(message,'');
    if (name != null) {
        // Данные отправлены пользователем
        document.getElementById(elementId).value = name;
    } else {
        // Отправка данных отменена пользователем
        document.getElementById(elementId).value = '@exit^';
    }
}

/**
 * Валидация имени.
 * param string formId
 * param string elementId
 * param string message
 */
function validateName(formId, elementId, message) {
    document.getElementById(formId).onsubmit = function() {
        // Проверка отправки без имени
        if (document.getElementById(elementId).value == "") {
            alert(message);
            // Остановка отправки
            return false;
        } else if (document.getElementById(elementId).value == "@exit^") {
            // Остановка отправки
            return false;
        } else {
            // Отправка
            return true;
        }
    };
}

/**
 * Вызов функций после загрузки документа.
 */
window.onload = function() {

    // Валидация имени папки при создании
    validateName(
        'formFolderCreate',
        'folderName',
        'Имя папки не может быть пустым!'
        );

    // Валидация имени файла при создании
    validateName(
        'formFileCreate',
        'fileName',
        'Имя файла не может быть пустым!'
        );
};

// Валидация имени файла при обновлении
validateName(
    'formFileUpdate',
    'currentFileName',
    'Имя файла не может быть пустым!'
    );
