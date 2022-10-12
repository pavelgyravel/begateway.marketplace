<?php
$classes = array(
  "\BeGateway\Module\Marketplace\ApplicationUserTable"  => "lib/applicationusertable.php",
  "\BeGateway\Module\Marketplace\ApplicationsTable"     => "lib/applicationstable.php",
  "\BeGateway\Module\Marketplace\CSocServDescription"   => "lib/description.php",
  "\BeGateway\Module\Marketplace\CSocServBemarketplace" => "lib/bemarketplace.php",
);

CModule::AddAutoloadClasses("begateway.marketplace", $classes);
