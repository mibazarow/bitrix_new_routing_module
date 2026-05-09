<?php

/**
 * Основные маршруты сайта.
 * 
 * Главная страница и статические разделы.
 * Для статических страниц используется PublicPageController —
 * он подключает физический файл и сохраняет совместимость
 * с визуальным редактором и «Управлением структурой».
 * 
 * Динамические разделы (каталог, новости) вынесены
 * в отдельные файлы: catalog.php, news.php
 */

use Bitrix\Main\Routing\RoutingConfigurator;
use Bitrix\Main\Routing\Controllers\PublicPageController;

return static function (RoutingConfigurator $routes) {

    // Главная страница: /
    // PublicPageController подключает физический /index.php
    // Это сохраняет работу визуального редактора и админ-панели
    $routes
        ->any('', new PublicPageController('/index.php'))
        ->name('home');

    // Статическая страница «О магазине»: /about/
    // Физический файл /about/index.php остаётся на месте —
    // контент-менеджеры могут править его через Эрмитаж
    $routes
        ->any('about/', new PublicPageController('/about/index.php'))
        ->name('about');

    // Статическая страница авторизации: /auth/
    $routes
        ->any('auth/', new PublicPageController('/auth/index.php'))
        ->name('auth');
};
