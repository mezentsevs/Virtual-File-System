<?php

/**
 * Общие установки.
 */
    // Установка временной зоны:
    date_default_timezone_set("Europe/Samara");

/**
 * Установка основных путей.
 */
    // Установка разделителя директорий:
    defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);

    // Установка пути корневой директории сайта:
    defined("SITE_ROOT") ? null : define(
        "SITE_ROOT",
        $_SERVER["DOCUMENT_ROOT"].DS.
            "my-site".DS."test_river_city"
        );
?>
