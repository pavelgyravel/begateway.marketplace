<?php
namespace BeGateway\Module\Marketplace;

class CSocServDescription
{
  public static function GetDescription()
  {

    $GLOBALS["APPLICATION"]->SetAdditionalCSS("/bitrix/themes/.default/bemarketplace.css");

    return array(
      array(
        'ID' => 'Bemarketplace',
        'CLASS' => 'BeGateway\Module\Marketplace\CSocServBemarketplace',
        'NAME' => 'Begateway marketplace',
        'ICON' => 'bemarketplace-icon'
      )
    );
  }
}
