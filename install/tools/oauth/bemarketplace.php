<?
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");

if(CModule::IncludeModule("socialservices")){
	$oAuthManager = new CSocServAuthManager();
	$oAuthManager->Authorize("Bemarketplace");
} else {
	echo 'Модуль socialservices не найден.';
}

require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/epilog_after.php");

?>