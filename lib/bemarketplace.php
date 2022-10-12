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
        $this->getBmOauthToken($_REQUEST["access_code"]);
        $user_id = $this->getUserId();

        $arFields = array(
          'EXTERNAL_AUTH_ID' => self::ID,
          'XML_ID' => $user_id,
          'LOGIN' => $user_id,
          'OATOKEN' => $this->oauthToken['access_token'],
          'OATOKEN_EXPIRES' => time() + $this->oauthToken['expires_in'],
          'REFRESH_TOKEN' => $this->oauthToken['refresh_token'],
          'SITE_ID' => $this->oauthApp['SITE_ID'],
        );

        $authResult = $this->AuthorizeUser($arFields);

        if ($authResult === true) {
          $userApplication = \BeGateway\Module\Marketplace\ApplicationUserTable::getList(array(
            'filter' => array(
              "=APPLICATION_ID" => $this->oauthApp['ID'],
              "=USER_ID" => $USER->GetID()
            )
          ));

          if (!$userApplication->fetch()) {
            \BeGateway\Module\Marketplace\ApplicationUserTable::add(array(
              "APPLICATION_ID" => $this->oauthApp['ID'], 
              "USER_ID" => $USER->GetID()
            ));
          }
        }

        $bSuccess = $authResult === true;
        ?>
          <?=GetMessage("BEGATEWAY_MARKETPLACE_USPERNAA_AVTORIZACIA") ?><script type="text/javascript">
            window.location = '/';
          </script>
          <?

      } else {
        echo "Application not found";
      }
    }
    else {
      echo "Required params access_code client_id is missing";
    }
    die();
  }
  
  public function getStorageToken()
	{
		$accessToken = null;
		$userId = intval($this->userId);
		if($userId > 0)
		{
			$dbSocservUser = \Bitrix\Socialservices\UserTable::getList([
				'filter' => ['=USER_ID' => $userId, "=EXTERNAL_AUTH_ID" => static::ID],
				'select' => ["OATOKEN", "REFRESH_TOKEN", "OATOKEN_EXPIRES"]
			]);
			if($arOauth = $dbSocservUser->fetch()) {
				$accessToken = $arOauth["OATOKEN"];

				if(empty($accessToken) || ((intval($arOauth["OATOKEN_EXPIRES"]) > 0) && (intval($arOauth["OATOKEN_EXPIRES"] < intval(time())))))
				{
					if(isset($arOauth['REFRESH_TOKEN'])) {
            $accessToken = $this->getNewAccessToken($arOauth['REFRESH_TOKEN'], $userId);
          }
				}
			}
		}

		return $accessToken;
	}

  public function getNewAccessToken($refreshToken, $userId)
	{
    $this->oauthToken = false;
		$userApplication = \BeGateway\Module\Marketplace\ApplicationUserTable::getList(array(
      'filter' => array(
        "=USER_ID" => $userId
      )
    ));
    
    if ($ua = $userApplication->fetch()) {
      $bmApplication = \BeGateway\Module\Marketplace\ApplicationsTable::getList(array(
        'filter' => array(
          "=ID" => $ua["APPLICATION_ID"],
        )
      ));

      if ($this->oauthApp = $bmApplication->fetch()) {
        $request = new \Bitrix\Main\Web\HttpClient();
        $request->setAuthorization($this->oauthApp['CLIENT_ID'], $this->oauthApp['CLIENT_SECRET']);
        $request->setHeader("Accept", "text/json");
        $request->setCharset("utf-8");
        $response = $request->post($this->oauthApp['HOST'] . '/api/v1/m/auth_marketplace', array(
          'refresh_token' => $refreshToken
        ));
        $this->oauthToken = json_decode($response, true);

        if ($error_message = $this->getErrorResponseMessage($this->oauthToken)) {
          AddMessage2Log("Could not refresh token. Error: $error_message", self::ID);
          return false;
        } else {
          $dbSocservUser = \Bitrix\Socialservices\UserTable::getList([
            'filter' => ['=USER_ID' => intval($userId), "=EXTERNAL_AUTH_ID" => self::ID],
            'select' => ["ID"]
          ]);
          if($arOauth = $dbSocservUser->fetch()) {
            \Bitrix\Socialservices\UserTable::update($arOauth["ID"], array(
              "OATOKEN" => $this->oauthToken['access_token'],
              "OATOKEN_EXPIRES" => time() + $this->oauthToken['expires_in'],
              "REFRESH_TOKEN" => $this->oauthToken['refresh_token']
            ));
            return $this->oauthToken['access_token'];
          }
          AddMessage2Log("Could not find User record in Bitrix\Socialservices\UserTable", self::ID);
          return false;
        }
      }
      AddMessage2Log("Could not find application record ID=".$ua["APPLICATION_ID"]." in \BeGateway\Module\Marketplace\ApplicationsTable", self::ID);
      return false;
    }
    AddMessage2Log("Could not find association user application record USER_ID=".$userId." in \BeGateway\Module\Marketplace\ApplicationUserTable", self::ID);
    return false;
	}

  private function getBmOauthToken($code = false)
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
        AddMessage2Log("Could not get access token. Error: $error_message", self::ID);
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
      AddMessage2Log("Could not get user_id. Error: $error_message", self::ID);
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

