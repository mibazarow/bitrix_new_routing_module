<?php

/**
 * Точка ранней инициализации /local/.
 * 
 * Выполняется до загрузки роутинга.
 * Здесь можно регистрировать обработчики событий, агенты,
 * а также задавать конфигурацию, не трогая /bitrix/.
 */

// Настройка роутинга без правки /bitrix/.settings.php
// Используем Configuration API — значение попадёт в общий пул настроек
\Bitrix\Main\Config\Configuration::setValue('routing', [
    'config' => [
        'web.php',
        'catalog.php',
        'news.php'
    ],
]);
