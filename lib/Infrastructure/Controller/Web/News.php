<?php

/**
 * Контроллер виртуального раздела «Новости».
 * 
 * Заменяет физический файл /news/index.php.
 * Папка /news/ удалена с диска — все URL обслуживаются через роутинг.
 * 
 * Пространство имён:  \Mibazarow\Bitrixshop\Infrastructure\Controller\Web
 * Модуль:             mibazarow.bitrixshop
 * Файл маршрутов:     /local/routes/news.php
 * 
 * ЦЕПОЧКА ЗАПРОСА:
 *   GET /news/pervaya-novost/
 *     → .htaccess → routing_index.php
 *     → /local/routes/news.php → News::class
 *     → indexAction()
 *     → renderComponent('bitrix:news', 'bootstrap_v4', [...])
 *     → компонент news сам разбирает /pervaya-novost/ через SEF_URL_TEMPLATES
 *     → HTML-ответ
 * 
 * Так же, как и каталог: контроллер не разбирает URL сам —
 * это делает компонент bitrix:news через внутренний SEF-роутинг.
 */

namespace Mibazarow\Bitrixshop\Infrastructure\Controller\Web;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Render\Component;

/**
 * Контроллер новостей.
 * 
 * Работает по тому же принципу, что и Catalog:
 * renderComponent() передаёт управление компоненту, компонент сам разбирает URL.
 * 
 * Шаблон bootstrap_v4 — стандартный шаблон новостей на Bootstrap 4
 * (идёт в комплекте с типовым магазином).
 */
final class News extends Controller
{
    /**
     * Отключаем префильтры для публичного доступа.
     * Без этого страница новостей требовала бы авторизацию и CSRF-токен.
     */
    protected function getDefaultPreFilters(): array
    {
        return [];
    }

    /**
     * Главная страница новостей и все SEF-подстраницы.
     * 
     * Компонент bitrix:news разбирает URL:
     *   /news/                     — список новостей (news)
     *   /news/{elementCode}/       — детальная новость (detail)
     *   /news/search/              — поиск по новостям (search)
     *   /news/rss/                 — RSS-лента (rss)
     *   /news/{sectionId}/rss/     — RSS раздела (rss_section)
     * 
     * @return Component
     */
    public function indexAction(): Component
    {
        return $this->renderComponent('bitrix:news', 'bootstrap_v4', [
            // === Основные настройки ===
            'IBLOCK_TYPE' => 'news',            // Тип инфоблока «Новости»
            'IBLOCK_ID' => 1,                   // ID инфоблока с новостями (подставьте свой)
            'SEF_MODE' => 'Y',
            'SEF_FOLDER' => '/news/',

            // === SEF-шаблоны ===
            // В отличие от каталога, здесь detail = #ELEMENT_CODE#/
            // (новости обычно без разделов, сразу элемент)
            'SEF_URL_TEMPLATES' => [
                'news' => '',                     // /news/
                'section' => '',                  // разделы не используются
                'detail' => '#ELEMENT_CODE#/',    // /news/pervaya-novost/
                'search' => 'search/',            // /news/search/
                'rss' => 'rss/',                  // /news/rss/
                'rss_section' => '#SECTION_ID#/rss/',
            ],

            // === Отображение ===
            'NEWS_COUNT' => '10',               // Количество новостей на странице
            'USE_SEARCH' => 'N',                 // Поиск по новостям (N — выключен)
            'USE_RSS' => 'Y',                    // RSS-лента
            'NUM_NEWS' => '20',                  // Количество элементов в RSS
            'NUM_DAYS' => '180',                 // Глубина RSS в днях

            // === Сортировка ===
            'SORT_BY1' => 'ACTIVE_FROM',         // Первичная сортировка — по дате
            'SORT_ORDER1' => 'DESC',             // Сначала новые
            'SORT_BY2' => 'SORT',                // Вторичная — по индексу сортировки
            'SORT_ORDER2' => 'ASC',
            'CHECK_DATES' => 'Y',                // Показывать только новости в заданный период

            // === Кеширование ===
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '36000000',
            'CACHE_FILTER' => 'N',               // Фильтр по новостям не кешируется
            'CACHE_GROUPS' => 'Y',

            // === SEO ===
            'SET_TITLE' => 'Y',
            'SET_STATUS_404' => 'Y',
            'ADD_ELEMENT_CHAIN' => 'Y',          // Добавлять элемент в хлебные крошки
        ]);
    }
}
