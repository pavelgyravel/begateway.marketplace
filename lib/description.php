<?php
namespace BeGateway\Module\Marketplace;

// class BemarketplaceHandler {
class CSocServDescription {
  public static function GetDescription() {
    return array(array(
      'ID' => 'Bemarketplace',
      'CLASS' => 'BeGateway\Module\Marketplace\CSocServBemarketplace',
      'NAME' => 'Begateway marketplace',
      'ICON' => 'bemarketplace-icon'
    ));
  }
}