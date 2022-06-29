<?
global $MESS;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarkeplace/install/index.php");

Class bemarketplace extends CModule {
  var $MODULE_ID = "bemarketplace";
  var $MODULE_VERSION;
  var $MODULE_VERSION_DATE;
  var $MODULE_NAME;
  var $MODULE_DESCRIPTION;
  var $MODULE_CSS;

  function __construct() {
    $arModuleVersion = array();
		include(__DIR__.'/version.php');
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

    $this->PARTNER_NAME = "bePaid";
    $this->PARTNER_URI = "https://bepaid.by";

    $this->MODULE_NAME = "Bemarketplace";
    $this->MODULE_DESCRIPTION = GetMessage("BEMARKETPLACE_INSTALL_DESCRIPTION");
  }

  function DoInstall() {
    global $APPLICATION, $DB, $errors;

    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/admin", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin", true);
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/tools/oauth", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/oauth", true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/themes", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);

    RegisterModuleDependences('socialservices', 'OnAuthServicesBuildList', 'bemarketplace', "BemarketplaceHandler", "GetDescription");

		$errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/db/".mb_strtolower($DB->type)."/install.sql");
		if (!empty($errors)) {
			$APPLICATION->ThrowException(implode("", $errors));
			return false;
		}

    RegisterModule($this->MODULE_ID);
    return true;
  }

  function DoUninstall()
  {
    global $APPLICATION, $DB, $errors;

    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/tools/oauth/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/oauth");
    DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");//css

    UnRegisterModuleDependences('socialservices', 'OnAuthServicesBuildList', 'bemarketplace', "BemarketplaceHandler", "GetDescription");
		
    $errors = false;
    
    if(array_key_exists("savedata", $_REQUEST) && $_REQUEST["savedata"] != "Y") {
      $errors = $DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/local/modules/bemarketplace/install/db/".mb_strtolower($DB->type)."/uninstall.sql");
    }

    if (!empty($errors)) {
      $APPLICATION->ThrowException(implode("", $errors));
      return false;
    }

    UnRegisterModule($this->MODULE_ID);
    return true;
  }
}
?>