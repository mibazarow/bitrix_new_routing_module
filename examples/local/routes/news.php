<?php

/**
 * Маршруты для виртуального раздела «Новости».
 * 
 * Физическая папка /news/ удалена с диска.
 * 
 * КОНТРОЛЛЕР: \Mibazarow\Bitrixshop\Infrastructure\Controller\Web\News
 * КОМПОНЕНТ:  bitrix:news (рендерится через renderComponent())
 * 
 * СХЕМА МАРШРУТОВ:
 *   /news/                  → News::indexAction  (список новостей)
 *   /news/{elementCode}/    → News::indexAction  (детальная)
 * 
 * Новости проще каталога — нет разделов, фильтров, сравнения.
 * Детальный URL формируется напрямую: /news/{elementCode}/
 */

use Bitrix\Main\Routing\RoutingConfigurator;
use Mibazarow\Bitrixshop\Infrastructure\Controller\Web\News;

return static function (RoutingConfigurator $routes) {

    // /news/ — список новостей
    $routes
        ->any('/news/', [News::class, 'index'])
        ->name('news.index');

    // /news/{elementCode}/ — детальная новость
    $routes
        ->get('/news/{elementCode}/', [News::class, 'index'])
        ->name('news.detail')
        ->where('elementCode', '[a-zA-Z0-9_\-]+');
};
