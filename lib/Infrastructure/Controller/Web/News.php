<?php

namespace Mibazarow\Bitrixshop\Infrastructure\Controller\Web;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Render\Component;

final class News extends Controller
{
    protected function getDefaultPreFilters(): array { return []; }

    public function indexAction(): Component
    {
        return $this->renderComponent('bitrix:news', 'bootstrap_v4', [
            'IBLOCK_TYPE' => 'news', 'IBLOCK_ID' => 1,
            'NEWS_COUNT' => '10', 'USE_SEARCH' => 'N', 'USE_RSS' => 'Y',
            'NUM_NEWS' => '20', 'NUM_DAYS' => '180',
            'SORT_BY1' => 'ACTIVE_FROM', 'SORT_ORDER1' => 'DESC',
            'SORT_BY2' => 'SORT', 'SORT_ORDER2' => 'ASC',
            'CHECK_DATES' => 'Y', 'SEF_MODE' => 'Y', 'SEF_FOLDER' => '/news/',
            'CACHE_TYPE' => 'A', 'CACHE_TIME' => '36000000',
            'CACHE_FILTER' => 'N', 'CACHE_GROUPS' => 'Y',
            'SET_TITLE' => 'Y', 'SET_STATUS_404' => 'Y',
            'ADD_ELEMENT_CHAIN' => 'Y',
            'SEF_URL_TEMPLATES' => [
                'news' => '', 'section' => '',
                'detail' => '#ELEMENT_CODE#/',
                'search' => 'search/', 'rss' => 'rss/',
                'rss_section' => '#SECTION_ID#/rss/',
            ],
        ]);
    }
}
