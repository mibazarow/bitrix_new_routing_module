<?php

namespace Mibazarow\Bitrixshop\Infrastructure\Controller\Web;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Render\Component;

final class Catalog extends Controller
{
    protected function getDefaultPreFilters(): array { return []; }

    public function indexAction(): Component
    {
        return $this->renderComponent('bitrix:catalog', '.default', [
            'IBLOCK_TYPE' => 'catalog', 'IBLOCK_ID' => 2,
            'SEF_MODE' => 'Y', 'SEF_FOLDER' => '/catalog/',
            'SEF_URL_TEMPLATES' => [
                'sections' => '', 'section' => '#SECTION_CODE#/',
                'element' => '#SECTION_CODE#/#ELEMENT_CODE#/',
                'compare' => 'compare/',
                'smart_filter' => '#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/',
            ],
            'USE_FILTER' => 'Y', 'FILTER_NAME' => 'arrFilter',
            'PRICE_CODE' => ['BASE'], 'CACHE_TYPE' => 'A', 'CACHE_TIME' => '36000000',
            'CACHE_FILTER' => 'Y', 'CACHE_GROUPS' => 'Y',
            'USE_COMPARE' => 'Y', 'COMPARE_NAME' => 'CATALOG_COMPARE_LIST',
            'BASKET_URL' => '/personal/cart/',
            'SET_TITLE' => 'Y', 'SET_STATUS_404' => 'Y', 'SHOW_404' => 'Y',
            'FILE_404' => '/404.php', 'COMPATIBLE_MODE' => 'N',
        ]);
    }
}
