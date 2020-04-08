<?
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class dmbgeo_neworderstatus extends CModule
{
    public $MODULE_ID = 'dmbgeo.neworderstatus';
    public $COMPANY_ID = 'dmbgeo';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function dmbgeo_neworderstatus()
    {
        $arModuleVersion = array();
        include __DIR__ . "/version.php";
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage("DMBGEO_NEWORDERSTATUS_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("DMBGEO_NEWORDERSTATUS_MODULE_DESC");
        $this->PARTNER_NAME = getMessage("DMBGEO_PARTNER_NAME");
        $this->PARTNER_URI = getMessage("DMBGEO_PARTNER_URI");
        $this->exclusionAdminFiles = array(
            '..',
            '.',
            'menu.php',
            'operation_description.php',
            'task_description.php',
        );
    }


   

  
    public function isVersionD7()
    {
        return CheckVersion(\Bitrix\Main\ModuleManager::getVersion('main'), '14.00.00');
    }

    public function GetPath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

   
    public function UnInstallOptions()
    {
        \Bitrix\Main\Config\Option::delete($this->MODULE_ID);
    }



    public function InstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler("sale", "OnBeforeOrderAdd", $this->MODULE_ID, '\NewOrderStatus', "eventOrder");

    }

    public function UnInstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler("sale", "OnBeforeOrderAdd", $this->MODULE_ID, '\NewOrderStatus', "eventOrder");

    }

    public function DoInstall()
    {

        global $APPLICATION;
        if ($this->isVersionD7()) {
            \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallEvents();
        } else {
            $APPLICATION->ThrowException(Loc::getMessage("DMBGEO_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("DMBGEO_INSTALL"), $this->GetPath() . "/install/step.php");
    }

    public function DoUninstall()
    {

        global $APPLICATION;

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        $this->UnInstallOptions();
        $this->UnInstallEvents();
        \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(Loc::getMessage("DMBGEO_UNINSTALL"), $this->GetPath() . "/install/unstep.php");
    }
}
