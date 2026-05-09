<?php

/**
 * Маршруты для виртуального раздела «Каталог товаров».
 * 
 * Физическая папка /catalog/ удалена с диска.
 * Все URL каталога обрабатываются через единую точку входа routing_index.php.
 * 
 * КОНТРОЛЛЕР: \Mibazarow\Bitrixshop\Infrastructure\Controller\Web\Catalog
 * КОМПОНЕНТ:  bitrix:catalog (рендерится через renderComponent())
 * 
 * СХЕМА МАРШРУТОВ:
 *   /catalog/                              → Catalog::indexAction  (каталог)
 *   /catalog/{sectionCode}/                → Catalog::indexAction  (раздел)
 *   /catalog/{sectionCode}/{elementCode}/  → Catalog::indexAction  (элемент)
 * 
 * ВАЖНО: маршруты используют один и тот же метод indexAction().
 * Компонент bitrix:catalog САМ разбирает {sectionCode} и {elementCode}
 * через свои внутренние SEF_URL_TEMPLATES. Параметры маршрута нужны только
 * для того, чтобы роутер не отдал 404 на «неизвестном» URL.
 * 
 * Именованные маршруты позволяют генерировать ссылки:
 *   Application::getInstance()->getRouter()->route('catalog.element', [
 *       'sectionCode' => 'odezhda',
 *       'elementCode' => 'kurtka',
 *   ]);
 *   // → /catalog/odezhda/kurtka/
 */

use Bitrix\Main\Routing\RoutingConfigurator;
use Mibazarow\Bitrixshop\Infrastructure\Controller\Web\Catalog;

return static function (RoutingConfigurator $routes) {

    // /catalog/ — главная страница каталога
    // any() — обрабатывает любые HTTP-методы (GET, POST, HEAD, ...)
    $routes
        ->any('/catalog/', [Catalog::class, 'index'])
        ->name('catalog.index');

    // /catalog/{sectionCode}/ — раздел каталога
    // get() — только GET/HEAD запросы
    // where() — валидация параметра (регулярное выражение)
    //   [a-zA-Z0-9_\-]+ — только латинские буквы, цифры, подчёркивания и дефисы
    $routes
        ->get('/catalog/{sectionCode}/', [Catalog::class, 'index'])
        ->name('catalog.section')
        ->where('sectionCode', '[a-zA-Z0-9_\-]+');

    // /catalog/{sectionCode}/{elementCode}/ — карточка товара
    $routes
        ->get('/catalog/{sectionCode}/{elementCode}/', [Catalog::class, 'index'])
        ->name('catalog.element')
        ->where('sectionCode', '[a-zA-Z0-9_\-]+')
        ->where('elementCode', '[a-zA-Z0-9_\-]+');
};
