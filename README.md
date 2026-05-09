# Пример реализации новой системы роутинга в 1С-Битрикс

Модуль демонстрирует переход **от файловой структуры разделов** к **единой точке входа** через
новую систему роутинга Bitrix Framework (появилась в версии `main` 21.400.0, полноценный
рендеринг контроллеров — с 25.700.0).

Сделан на примере типового интернет-магазина, который идёт в комплекте с
«1С-Битрикс: Управление сайтом».

### Что уже реализовано и проверено

В этом примере на новую систему роутинга переведены два раздела:

| Раздел | Было | Стало | Физическая папка |
|--------|------|-------|------------------|
| `/catalog/` | `/catalog/index.php` с `bitrix:catalog` | `Mibazarow\Bitrixshop\...\Catalog` + `renderComponent()` | ❌ удалена |
| `/news/` | `/news/index.php` с `bitrix:news` | `Mibazarow\Bitrixshop\...\News` + `renderComponent()` | ❌ удалена |

Оба раздела работают полностью: список товаров/новостей, детальные страницы, ЧПУ, хлебные крошки, 404.
Физических папок `/catalog/` и `/news/` на диске больше нет — все URL обслуживаются через
`/bitrix/routing_index.php`.

> **Важно:** роутинг — опциональная возможность. Физические файлы и старый `urlrewrite.php`
> никуда не деваются. Можно внедрять постепенно, раздел за разделом.

### Для новых проектов — сразу на роутинг

Если вы начинаете проект с нуля на Bitrix Framework — **делайте его сразу на новой
системе роутинга**. Не создавайте физические `index.php` в папках разделов.
Сразу закладывайте:

- `/local/routes/web.php` — главная и статические страницы (через `PublicPageController`)
- `/local/routes/catalog.php` — каталог (через `renderComponent`)
- `/local/routes/news.php` — новости
- Модуль-ядро проекта (как этот `mibazarow.bitrixshop`) — для контроллеров

Так вы с первого дня получаете:
- Типизированные параметры URL вместо `$_GET`
- Генерацию ссылок по именам маршрутов
- Разделение на HTTP-методы (GET/POST/PUT/DELETE)
- Чистую архитектуру без портянки в корне сайта

Старые проекты можно мигрировать постепенно: начать с каталога и новостей (где всё
равно контент идёт из инфоблоков), а статические страницы оставить на физических файлах.

---

## Требования

| Компонент | Минимальная версия |
|-----------|---------------------|
| Ядро `main` | **25.700.0** (для `renderComponent()`) |
| PHP | **8.0+** (D7, именованные аргументы) |
| Apache | mod_rewrite включён |
| Модули | `iblock`, `catalog` (для каталога), `news` (для новостей) |

---

## Установка

1. Скопировать папку `mibazarow.bitrixshop` в `/local/modules/`
2. Зайти в админку: **Marketplace → Установленные решения**
3. Найти «Битрикс-магазин (mibazarow.bitrixshop)» → **Установить**

Модуль не копирует файлы, не создаёт таблиц — только регистрируется в системе
для работы автозагрузки PSR-4.

---

## Как это работает

### Цепочка запроса

```
GET /catalog/odezhda/kurtka/
  ↓
.htaccess → /bitrix/routing_index.php      (единая точка входа)
  ↓
/bitrix/.settings_extra.php → routing.config  ['web.php', 'catalog.php', 'news.php']
  ↓
/local/routes/catalog.php                  (правила маршрутизации)
  ↓
\...\Web\Catalog::indexAction()            (контроллер)
  ↓
$this->renderComponent('bitrix:catalog')   (прямой рендеринг компонента)
  ↓
HTML-ответ
```

### Ключевое отличие от физических файлов

| Физический подход | Роутинг |
|---|---|
| `/catalog/index.php` — файл с `$APPLICATION->IncludeComponent()` | `Mibazarow\Bitrixshop\...\Catalog::indexAction()` — метод с `$this->renderComponent()` |
| URL определяется положением файла | URL задаётся правилами в `/local/routes/` |
| Параметры — `$_GET`, `$_REQUEST` | Типизированные аргументы метода: `string $sectionCode` |
| Генерация ссылок — хардкод `/catalog/123/` | `$router->route('catalog.section', ['sectionCode' => '123'])` |

---

## Структура модуля

```
local/modules/mibazarow.bitrixshop/
├── README.md                              ← этот файл
├── .settings.php                          ← defaultNamespace для AJAX-контроллеров
├── install/
│   ├── index.php                          ← class mibazarow_bitrixshop extends CModule
│   └── version.php                        ← 1.0.0
├── lang/ru/install/
│   └── index.php                          ← языковые сообщения
└── lib/
    └── Infrastructure/
        └── Controller/
            └── Web/
                ├── Catalog.php            ← /catalog/
                └── News.php               ← /news/
```

Файлы маршрутов лежат отдельно:

```
local/routes/
├── web.php                                ← главная, about, auth
├── catalog.php                            ← маршруты каталога
└── news.php                               ← маршруты новостей
```

Готовые примеры файлов маршрутов есть в `examples/local/routes/` — можно скопировать
к себе в `/local/routes/` и адаптировать под свой проект.

---

## Как добавить новый виртуальный раздел

### Пример: раздел `/blog/`

**1. Контроллер** → `lib/Infrastructure/Controller/Web/Blog.php`:

```php
namespace Mibazarow\Bitrixshop\Infrastructure\Controller\Web;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\Render\Component;

final class Blog extends Controller
{
    protected function getDefaultPreFilters(): array { return []; }

    public function indexAction(): Component
    {
        return $this->renderComponent('bitrix:news', '.default', [
            'IBLOCK_TYPE' => 'blog',
            'IBLOCK_ID' => 3,
            'SEF_MODE' => 'Y',
            'SEF_FOLDER' => '/blog/',
            'SEF_URL_TEMPLATES' => [
                'news' => '',
                'detail' => '#ELEMENT_CODE#/',
            ],
        ]);
    }
}
```

**2. Маршруты** → `local/routes/blog.php`:

```php
<?php
use Bitrix\Main\Routing\RoutingConfigurator;
use Mibazarow\Bitrixshop\Infrastructure\Controller\Web\Blog;

return static function (RoutingConfigurator $routes) {
    $routes->any('/blog/', [Blog::class, 'index'])->name('blog.index');
    $routes->get('/blog/{elementCode}/', [Blog::class, 'index'])
        ->name('blog.detail')
        ->where('elementCode', '[a-zA-Z0-9_\-]+');
};
```

**3. Конфиг** → `bitrix/.settings_extra.php`, секция `routing`:

```php
'config' => ['web.php', 'catalog.php', 'news.php', 'blog.php'],
```

**4. Физическая папка** — удалить `/blog/` (если была).

---

## Что можно и нужно удалить

После переноса раздела на роутинг физическая папка больше не нужна:

```bash
rm -rf /catalog    # ← удалена
rm -rf /news       # ← удалена
```

Системные файлы вроде `.section.php` и `sidebar.php` переносятся в настройки компонента
или создаются заново через админку при необходимости.

> `.htaccess` остаётся стандартным — условие `!-d` (пропускать существующие директории)
> теперь не мешает, потому что директорий больше нет.

---

## Ограничения и нюансы

### Режим правки и админ-панель

| Функция | Чистый контроллер | PublicPageController |
|---|---|---|
| Верхняя админ-панель | ✅ | ✅ |
| Шестерёнки на компонентах | ✅ | ✅ |
| Режим правки инфоблоков | ✅ | ✅ |
| «Управление структурой» (дерево) | ❌ | ✅ |
| Визуальный редактор страницы | ❌ | ✅ |

**Вывод:** для страниц, которые правят контент-менеджеры (например, «О магазине»),
используйте `PublicPageController` + физический файл. Для динамических разделов
(каталог, новости, блог) — чистые контроллеры.

### Автозагрузка

Модуль — партнёрский (ID с точкой: `mibazarow.bitrixshop`), поэтому namespace
двухуровневый: `Mibazarow\Bitrixshop`. Подпространства (`Infrastructure\Controller\Web`)
работают корректно.

> Однословные модули (без точки, например `myshop`) — только для системных модулей Bitrix.
> Пользовательские модули всегда делаются с точкой (партнёрский формат).

### ЧПУ инфоблоков

ЧПУ каталога и новостей (`/catalog/odezhda/kurtka/`) настраивается **внутри компонента**
через `SEF_URL_TEMPLATES`. Роутинг передаёт управление компоненту, а компонент уже сам
разбирает SEF-пути. Это два независимых механизма, они не конфликтуют.

### Переименование модуля под свой проект

Всегда используйте партнёрский формат (с точкой): `vendor.module`.

- ID модуля: `mycompany.myshop` → папка `mycompany.myshop`
- Namespace: `Mycompany\Myshop\...` (точка → обратный слеш, первая буква заглавная)
- Класс установщика: `mycompany_myshop` (точка → подчёркивание)

Однословные ID (без точки) зарезервированы за системными модулями Bitrix.

---

## Генерация ссылок

Роутинг позволяет генерировать URL по имени маршрута:

```php
$url = \Bitrix\Main\Application::getInstance()
    ->getRouter()
    ->route('catalog.element', [
        'sectionCode' => 'odezhda',
        'elementCode' => 'kurtka',
    ]);
// Результат: /catalog/odezhda/kurtka/
```

---

## Полезные ссылки

- [Документация: Роутинг](https://docs.1c-bitrix.ru/pages/framework/routing.html)
- [Документация: Контроллеры](https://docs.1c-bitrix.ru/pages/framework/controllers.html)
- [Документация: Создание модуля](https://docs.1c-bitrix.ru/pages/get-started/create-module.html)
