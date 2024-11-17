<?php

/**
 * @throws Exception
 */
$callback = function ($className) {
    $path = sprintf('%s/%s.php', __DIR__, $className);

    if (!file_exists($path)) {
        throw new Exception("Класс {$className} не найден в пути {$path}");
    }

    require_once $path;
};

// Автоматическая загрузка классов
spl_autoload_register($callback);