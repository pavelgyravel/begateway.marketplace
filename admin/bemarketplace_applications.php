<?
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/form/prolog.php";
$module_id = "begateway.marketplace";

CModule::IncludeModule($module_id);

$action = htmlspecialcharsbx($_REQUEST['action']);

$result = '{"result":"error"}';
if (check_bitrix_sessid())
{
  switch ($action)
  {
    case 'get_applications':
      $site_id = $_REQUEST['SITE_ID'];
      $application_id = intval($_REQUEST['ID']);

      if ($site_id || $application_id)
      {
        $filter = array();

        if ($application_id)
        {
          $filter["=ID"] = $application_id;
        }

        if ($site_id)
        {
          $filter["=SITE_ID"] = $site_id;
        }

        $res = BeGateway\Module\Marketplace\ApplicationsTable::getList(array(
          'filter' => $filter,
        ));
        $arFields = $res->fetchAll();
        foreach ($arFields as $key => $val)
        {
          $arFields[$key]['RETURN_URL'] = "{{SHOP_DOMAIN}}/bitrix/tools/oauth/bemarketplace.php?application_id=" . $arFields[$key]['ID'];
        }
        $result = '{"result":"ok","applications":' . CUtil::PhpToJsObject($arFields) . '}';
      }
      else
      {
        $result = '{"result":"error","error":"SITE_ID missing"}';
      }
    break;
    case 'delete_application':
      $application_id = intval($_REQUEST['ID']);

      if ($application_id)
      {
        $application = BeGateway\Module\Marketplace\ApplicationsTable::getByPrimary($application_id)->fetchObject();
        if ($application)
        {
          $deletion_result = $application->delete();

          if (!$deletion_result->isSuccess())
          {
            $result = '{"result":"error","error":"' . implode(", ", $deletion_result->getErrorMessages()) . '"}';
          }
          else
          {
            $result = '{"result":"ok"}';
          }
        }
        else
        {
          $result = '{"result":"error","error":"Application not found"}';
        }
      }
    break;
    case 'save_application':
      $data = $_POST;

      if ($_POST['ID'])
      {
        $id = $data['ID'];
        unset($data['ID']);
        $result = BeGateway\Module\Marketplace\ApplicationsTable::update($id, $data);
      }
      elseif (!isset($_POST['ID']))
      {
        BeGateway\Module\Marketplace\ApplicationsTable::add($data);
      }

      $result = '{"result":"ok"}';

    break;
  }
}
else
{
  $result = '{"result":"error","error":"session_expired"}';
}

if ($result)
{
  $APPLICATION->RestartBuffer();
  echo $result;
}

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_after.php";

