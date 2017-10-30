<?php

/**
 * Перенаправление на адрес.
 */
function redirectTo( $location = NULL )
{
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}

/**
 * Включение шаблона документа.
 */
function includeLayoutTemplate($template="")
{
    include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

/**
 * Вывод размера файла текстом.
 */
function sizeAsText($size)
{
    if($size < 1024) {
        return "{$size} ".getNumEnding($size, ['байт', 'байта', 'байт']);
    } elseif($size < 1048576) {
        $sizeKb = round($size/1024);
        return "{$sizeKb} KБ";
    } else {
        $sizeMb = round($size/1048576, 1);
        return "{$sizeMb} MБ";
    }
}

/**
 * Объединение массивов.
 * 
 * почти array_merge(), но без учета дублирования ключей
 * @param array $arr1   первый массив
 * @param array $arr2   второй массив
 * @return array
 */
function arraySum($arr1, $arr2)
{
    $result = [];

    // Cчитывание первого массива
    foreach($arr1 as $val) {
        $result[] = $val;
    }

    // Cчитывание второго массива
    foreach($arr2 as $val) {
        $result[] = $val;
    }
     
    return $result;
}

/**
 * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний.
 * @param  $number Integer Число на основе которого нужно сформировать окончание
 * @param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
 *         например array ['яблоко', 'яблока', 'яблок']
 * @return String
 */
function getNumEnding($number, $endingArray)
{
    $number = $number % 100;
    if ($number>=11 && $number<=19) {
        $ending=$endingArray[2];
    }
    else {
        $i = $number % 10;
        switch ($i)
        {
            case (1): $ending = $endingArray[0]; break;
            case (2):
            case (3):
            case (4): $ending = $endingArray[1]; break;
            default: $ending=$endingArray[2];
        }
    }
    return $ending;
}
?>
