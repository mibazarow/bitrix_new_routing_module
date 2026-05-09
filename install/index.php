<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class mibazarow_bitrixshop extends CModule
{
    public $MODULE_ID = 'mibazarow.bitrixshop';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        include __DIR__ . '/version.php';
        if (isset($arModuleVersion['VERSION'], $arModuleVersion['VERSION_DATE'])) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_NAME = Loc::getMessage('MIB_BXSHOP_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MIB_BXSHOP_MODULE_DESCRIPTION');
    }

    public function DoInstall(): void
    {
        global $USER;
        if (!$USER->IsAdmin()) return;
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall(): void
    {
        global $USER;
        if (!$USER->IsAdmin()) return;
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}
