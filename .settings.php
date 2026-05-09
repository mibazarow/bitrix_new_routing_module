<?php

/**
 * Конфигурация модуля mibazarow.bitrixshop.
 * 
 * Задаёт пространство имён по умолчанию для AJAX-контроллеров.
 * Используется при вызове действий через /bitrix/services/main/ajax.php
 * (например, BX.ajax.runAction('mibazarow:bitrixshop.Catalog.index')).
 * 
 * Для роутинговых контроллеров (через routing_index.php) эта настройка
 * не обязательна — роутинг использует полное имя класса из маршрутов.
 */

return [
    'controllers' => [
        'value' => [
            // Пространство имён для AJAX-контроллеров.
            // При вызове BX.ajax.runAction('mibazarow:bitrixshop.SomeClass.method')
            // система ищет класс \Mibazarow\Bitrixshop\Infrastructure\Controller\SomeClass
            'defaultNamespace' => '\\Mibazarow\\Bitrixshop\\Infrastructure\\Controller',
        ],
        'readonly' => true,
    ]
];
