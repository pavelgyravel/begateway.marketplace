<?php
namespace BeGateway\Module\Marketplace;

class CSocServBemarketplace extends \CSocServAuth
{
  const ID = 'BegatewayMarketplace';

  private $oauthApp;
  private $oauthToken;

  public function GetSettings()
  {
    return array();
  }

  public function Authorize()
  {
    global $APPLICATION;
    global $USER;

    $APPLICATION->RestartBuffer();

    if (!defined('SITE_ID') || !SITE_ID) die('SITE_ID did not set.');

    if ($USER->IsAuthorized())
    {
      $USER->Logout();
    }

    if (isset($_REQUEST["access_code"]) && $_REQUEST["access_code"] <> '' && isset($_REQUEST["client_id"]) && $_REQUEST["client_id"] <> '')
    {

      $result = \BeGateway\Module\Marketplace\ApplicationsTable::getList(array(
        'filter' => array(
          "=CLIENT_ID" => $_REQUEST["client_id"],
          "=SITE_ID" => SITE_ID
        )
      ));

      if ($this->oauthApp = $result->fetch())
      {
        $this->getOauthToken($_REQUEST["access_code"]);
        $user_id = $this->getUserId();

        $arFields = array(
          'EXTERNAL_AUTH_ID' => self::ID,
          'XML_ID' => $user_id,
          'LOGIN' => "bm-" . $user_id,
          'OATOKEN' => $this->oauthToken['access_token'],
          'OATOKEN_EXPIRES' => time() + $this->oauthToken['expires_in'],
          'REFRESH_TOKEN' => $this->oauthToken['refresh_token'],
          'SITE_ID' => $this->oauthApp['SITE_ID'],
        );

        $authError = $this->AuthorizeUser($arFields);

        $bSuccess = $authError === true;
?>
          <?=GetMessage("BEGATEWAY_MARKETPLACE_USPERNAA_AVTORIZACIA") ?><script type="text/javascript">
            window.location = '/';
          </script>
          <?

      }
      else
      {
        echo "Application not found";
      }
    }
    else
    {
      echo "Required params access_code client_id is missing";
    }
    die();
  }

  private function getOauthToken($code = false)
  {
    if ($code)
    {
      $request = new \Bitrix\Main\Web\HttpClient();
      $request->setAuthorization($this->oauthApp['CLIENT_ID'], $this->oauthApp['CLIENT_SECRET']);
      $request->setHeader("Accept", "text/json");
      $request->setCharset("utf-8");
      $response = $request->post($this->oauthApp['HOST'] . '/api/v1/m/auth_marketplace', array(
        'code' => $code
      ));
      $this->oauthToken = json_decode($response, true);

      if ($error_message = $this->getErrorResponseMessage($this->oauthToken))
      {
        echo $error_message;
        die();
      }
    }
    return $this->oauthToken;
  }

  private function getUserId()
  {
    $request = new \Bitrix\Main\Web\HttpClient();
    $request->setHeader("Authorization", 'Bearer ' . $this->oauthToken['access_token']);
    $request->setCharset("utf-8");
    $response = $request->get($this->oauthApp['HOST'] . '/api/v1/m/get_user');
    $get_user_result = json_decode($response, true);
    if ($error_message = $this->getErrorResponseMessage($get_user_result))
    {
      echo $error_message;
      die();
    }
    return $get_user_result['data']['user_id'];
  }

  private function getErrorResponseMessage($response)
  {
    if (isset($response['errors']))
    {
      return $this->oauthToken['errors'][0]['code'] . " " . $this->oauthToken['errors'][0]['title'];
    }
    else
    {
      return false;
    }
  }
}

