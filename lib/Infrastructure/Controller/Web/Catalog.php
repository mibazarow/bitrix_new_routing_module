<?php

/**
 * Контроллер виртуального раздела «Каталог товаров».
 * 
 * Заменяет физический файл /catalog/index.php.
 * Папка /catalog/ удалена с диска — все URL обслуживаются через роутинг.
 * 
 * Пространство имён:  \Mibazarow\Bitrixshop\Infrastructure\Controller\Web
 * Модуль:             mibazarow.bitrixshop
 * Файл маршрутов:     /local/routes/catalog.php
 * 
 * ЦЕПОЧКА ЗАПРОСА:
 *   GET /catalog/odezhda/kurtka/
 *     → .htaccess → routing_index.php
 *     → /local/routes/catalog.php → Catalog::class
 *     → indexAction()
 *     → renderComponent('bitrix:catalog', '.default', [...])
 *     → компонент catalog сам разбирает /odezhda/kurtka/ через SEF_URL_TEMPLATES
 *     → HTML-ответ
 * 
 * ВАЖНО: контроллер НЕ разбирает SEF-пути сам — это делает компонент bitrix:catalog.
 * Контроллер только передаёт управление компоненту. Все подстраницы каталога
 * (разделы, элементы, фильтр, сравнение) идут через этот же indexAction().
 */

namespace Mibazarow\Bitrixshop\Infrastructure\Controller\Web;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Render\Component;

/**
 * Контроллер каталога.
 * 
 * Наследует \Bitrix\Main\Engine\Controller — базовый класс контроллеров D7.
 * Метод indexAction() возвращает Component — это специальный тип ответа,
 * который рендерит компонент Битрикс и оборачивает результат в HTML.
 * 
 * renderComponent() доступен с версии main 25.700.0.
 */
final class Catalog extends Controller
{
    /**
     * Отключаем префильтры по умолчанию.
     * 
     * По умолчанию Controller требует:
     *   - CSRF-токен для POST-запросов
     *   - Авторизацию пользователя
     *   - Только методы GET и POST
     * 
     * Для публичной страницы каталога всё это не нужно.
     * Возвращаем пустой массив — отключаем все предфильтры.
     */
    protected function getDefaultPreFilters(): array
    {
        return [];
    }

    /**
     * Главная страница каталога (и все SEF-подстраницы).
     * 
     * Компонент bitrix:catalog — стандартный компонент каталога Битрикс.
     * Он сам разруливает URL на основе SEF_URL_TEMPLATES:
     *   /catalog/                          — список разделов (sections)
     *   /catalog/{sectionCode}/            — раздел с товарами (section)
     *   /catalog/{sectionCode}/{elemCode}/ — карточка товара (element)
     *   /catalog/{sectionCode}/filter/...  — умный фильтр (smart_filter)
     *   /catalog/compare/                  — сравнение товаров (compare)
     * 
     * Шаблон .default — стандартный шаблон компонента.
     * Для кастомизации создаётся свой шаблон в шаблоне сайта.
     * 
     * @return Component  Объект ответа, который Битрикс преобразует в HTML
     */
    public function indexAction(): Component
    {
        // renderComponent() — прямой рендеринг компонента без физического файла.
        // Первый аргумент — имя компонента (bitrix:catalog)
        // Второй — имя шаблона (.default)
        // Третий — массив параметров (те же, что и в $APPLICATION->IncludeComponent)
        return $this->renderComponent('bitrix:catalog', '.default', [
            // === Основные настройки ===
            'IBLOCK_TYPE' => 'catalog',       // Тип инфоблока (должен существовать)
            'IBLOCK_ID' => 2,                  // ID инфоблока с товарами (подставьте свой)
            'SEF_MODE' => 'Y',                 // Включаем ЧПУ (SEF)
            'SEF_FOLDER' => '/catalog/',       // Корневая папка для SEF-путей

            // === SEF-шаблоны URL ===
            // Компонент сам сопоставляет URL с этими шаблонами
            'SEF_URL_TEMPLATES' => [
                'sections' => '',                             // /catalog/
                'section' => '#SECTION_CODE#/',               // /catalog/odezhda/
                'element' => '#SECTION_CODE#/#ELEMENT_CODE#/',// /catalog/odezhda/kurtka/
                'compare' => 'compare/',                      // /catalog/compare/
                'smart_filter' => '#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/',
            ],

            // === Цены ===
            'USE_FILTER' => 'Y',
            'FILTER_NAME' => 'arrFilter',
            'PRICE_CODE' => ['BASE'],           // Тип цены (BASE — базовая цена)
            'USE_PRICE_COUNT' => 'N',
            'SHOW_PRICE_COUNT' => 1,
            'PRICE_VAT_INCLUDE' => 'Y',

            // === Кеширование ===
            // A — авто (включается на production, выключается при debug=true)
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '36000000',         // Время кеша в секундах
            'CACHE_FILTER' => 'Y',              // Кешировать с учётом фильтра
            'CACHE_GROUPS' => 'Y',              // Раздельный кеш для групп пользователей

            // === Сравнение товаров ===
            'USE_COMPARE' => 'Y',
            'COMPARE_NAME' => 'CATALOG_COMPARE_LIST',

            // === Корзина ===
            'BASKET_URL' => '/personal/cart/',

            // === SEO и заголовки ===
            'SET_TITLE' => 'Y',
            'SET_BROWSER_TITLE' => 'Y',
            'SET_META_KEYWORDS' => 'Y',
            'SET_META_DESCRIPTION' => 'Y',
            'ADD_SECTIONS_CHAIN' => 'Y',
            'ADD_ELEMENT_CHAIN' => 'Y',

            // === 404 ===
            'SET_STATUS_404' => 'Y',
            'SHOW_404' => 'Y',
            'FILE_404' => '/404.php',

            // === Производительность ===
            'SET_LAST_MODIFIED' => 'Y',
            'USE_ELEMENT_COUNTER' => 'Y',

            // === Совместимость ===
            'COMPATIBLE_MODE' => 'N',           // Отключаем режим совместимости со старыми шаблонами
        ]);
    }
}
