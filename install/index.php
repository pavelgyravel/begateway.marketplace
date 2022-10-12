<?
global $MESS;
IncludeModuleLangFile(__FILE__);

class begateway_marketplace extends CModule
{
  var $MODULE_ID = "begateway.marketplace";
  var $MODULE_VERSION;
  var $MODULE_VERSION_DATE;
  var $MODULE_NAME;
  var $MODULE_DESCRIPTION;
  var $MODULE_CSS;

  function __construct()
  {
    $arModuleVersion = array();
    include (__DIR__ . '/version.php');
    $this->MODULE_VERSION = $arModuleVersion["VERSION"];
    $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

    $this->PARTNER_NAME = "bePaid";
    $this->PARTNER_URI = "https://bepaid.by";

    $this->MODULE_NAME = GetMessage("BEMARKETPLACE_INSTALL_DESCRIPTION");
    $this->MODULE_DESCRIPTION = GetMessage("BEMARKETPLACE_INSTALL_DESCRIPTION");
    $this->MODULE_PATH = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/" . $this->MODULE_ID;
  }

  function DoInstall()
  {
    global $APPLICATION, $DB, $errors;

    CopyDirFiles($this->MODULE_PATH . "/install/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin", true);
    CopyDirFiles($this->MODULE_PATH . "/install/tools/oauth", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools/oauth", true);
    CopyDirFiles($this->MODULE_PATH . "/install/themes", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes", true, true);

    RegisterModuleDependences('socialservices', 'OnAuthServicesBuildList', $this->MODULE_ID, "\\BeGateway\\Module\\Marketplace\\CSocServDescription", "GetDescription");

    $errors = $DB->RunSQLBatch($this->MODULE_PATH . "/install/db/" . mb_strtolower($DB->type) . "/install.sql");
    if (!empty($errors))
    {
      $APPLICATION->ThrowException(implode("", $errors));
      return false;
    }

    RegisterModule($this->MODULE_ID);
    return true;
  }

  function DoUninstall()
  {
    global $APPLICATION, $DB, $errors;

    DeleteDirFiles($this->MODULE_PATH . "/install/admin/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
    DeleteDirFiles($this->MODULE_PATH . "/install/tools/oauth/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/tools/oauth");
    DeleteDirFiles($this->MODULE_PATH . "/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/themes/.default"); //css
    UnRegisterModuleDependences('socialservices', 'OnAuthServicesBuildList', $this->MODULE_ID, "\\BeGateway\\Module\\Marketplace\\CSocServDescription", "GetDescription");

    $errors = false;

    if (!array_key_exists("savedata", $_REQUEST) || $_REQUEST["savedata"] !== "Y")
    {
      $errors = $DB->RunSQLBatch($this->MODULE_PATH . "/install/db/" . mb_strtolower($DB->type) . "/uninstall.sql");
    }

    if (!empty($errors))
    {
      $APPLICATION->ThrowException(implode("", $errors));
      return false;
    }

    UnRegisterModule($this->MODULE_ID);
    return true;
  }
}
?>
