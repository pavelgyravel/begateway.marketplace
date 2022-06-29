<?php
$arClasses = array(
	"BemarketplaceHandler" => "classes/bemarketplace_handler.php",
  "CSocServBemarketplace" => "classes/c_soc_serv_bemarketplace.php",
);

CModule::AddAutoloadClasses("bemarketplace", $arClasses);
