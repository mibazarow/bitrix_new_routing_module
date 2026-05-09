<?php
return array(
    'cache' => array(
        'value' => array(
            'type' => 'memcache',
            'memcache' => array(
                'host' => '127.0.0.1',
                'port' => '11211',
            ),
            'sid' => $_SERVER["DOCUMENT_ROOT"]."#bx"
        ),
    ),
    // Роутинг: список файлов с маршрутами.
    // Файлы ищутся в /local/routes/, затем в /bitrix/routes/.
    // Добавляйте новые файлы при создании новых виртуальных разделов.
    'routing' => array(
        'value' => array(
            'config' => array('web.php', 'catalog.php', 'news.php'),
        ),
        'readonly' => true,
    ),
);