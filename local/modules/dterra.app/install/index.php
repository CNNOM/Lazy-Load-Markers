<?php

class dterra_app extends CModule
{
    public $MODULE_ID = 'dterra.app';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $this->MODULE_VERSION = '0.0.1';
        $this->MODULE_VERSION_DATE = '25.06.2025';
        $this->MODULE_NAME = $this->MODULE_ID;
        $this->MODULE_DESCRIPTION = 'Модуль для тестового сайта DTerra';
        $this->PARTNER_NAME = 'dterra';
        $this->PARTNER_URI = 'https://dterra.eu';
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        return true;
    }

    public function DoUninstall()
    {
        UnRegisterModule($this->MODULE_ID);
        return true;
    }
}