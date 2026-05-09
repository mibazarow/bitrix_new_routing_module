<?php

/**
 * Установщик модуля mibazarow.bitrixshop.
 * 
 * Партнёрский модуль (ID с точкой). При установке только регистрируется
 * в системе — не копирует файлы и не создаёт таблиц.
 * 
 * Класс установщика: mibazarow_bitrixshop (точка в ID → подчёркивание)
 * Namespace модуля:   Mibazarow\Bitrixshop
 * 
 * Установка: Marketplace → Установленные решения → «Битрикс-магазин» → Установить
 * Удаление:  там же → «Битрикс-магазин» → Удалить
 * 
 * После установки становится доступна PSR-4 автозагрузка классов модуля.
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

// Подгружаем языковые сообщения для названия и описания модуля
Loc::loadMessages(__FILE__);

/**
 * Class mibazarow_bitrixshop
 * 
 * Наследует CModule — стандартный класс установщика Битрикс.
 * 
 * Обязательные поля:
 *   $MODULE_ID           — строка, идентификатор модуля (mibazarow.bitrixshop)
 *   $MODULE_VERSION      — строка, версия из version.php
 *   $MODULE_VERSION_DATE — строка, дата версии
 *   $MODULE_NAME         — строка, человекочитаемое название (из lang-файла)
 *   $MODULE_DESCRIPTION  — строка, описание (из lang-файла)
 */
class mibazarow_bitrixshop extends CModule
{
    /** @var string Идентификатор модуля — должен совпадать с именем папки */
    public $MODULE_ID = 'mibazarow.bitrixshop';
    
    /** @var string Версия модуля (подгружается из version.php) */
    public $MODULE_VERSION;
    
    /** @var string Дата версии (подгружается из version.php) */
    public $MODULE_VERSION_DATE;
    
    /** @var string Название модуля в админке */
    public $MODULE_NAME;
    
    /** @var string Описание модуля в админке */
    public $MODULE_DESCRIPTION;

    /**
     * Конструктор.
     * Загружает version.php и языковые сообщения.
     */
    public function __construct()
    {
        // Подгружаем версию из version.php (массив $arModuleVersion)
        include __DIR__ . '/version.php';

        if (isset($arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE'])) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        // Человекочитаемые название и описание из языкового файла
        $this->MODULE_NAME = Loc::getMessage('MIB_BXSHOP_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MIB_BXSHOP_MODULE_DESCRIPTION');
    }

    /**
     * Установка модуля.
     * 
     * Вызывается при нажатии «Установить» в админке.
     * Регистрирует модуль в БД (b_module), после чего начинает работать
     * PSR-4 автозагрузка классов из lib/.
     * 
     * Никакие файлы не копируются, таблицы не создаются.
     */
    public function DoInstall(): void
    {
        global $USER;

        // Только администратор может устанавливать модули
        if (!$USER->IsAdmin()) {
            return;
        }

        // Регистрация в БД — ключевое действие для включения автозагрузки
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Удаление модуля.
     * 
     * Вызывается при нажатии «Удалить» в админке.
     * Снимает регистрацию модуля в БД.
     * Файлы модуля остаются на диске — их нужно удалять вручную при необходимости.
     */
    public function DoUninstall(): void
    {
        global $USER;

        if (!$USER->IsAdmin()) {
            return;
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
