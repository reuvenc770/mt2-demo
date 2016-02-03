<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/20/16
 * Time: 10:30 AM
 */

namespace App\Services\API;
use App\Facades\EspApiAccount;
use App\Library\Campaigner\CampaignManagement;
use App\Library\Campaigner\Authentication;
class CampaignerApi extends BaseAPI
{
    private  $auth;

    public function __construct($name, $espAccountId)
    {
        parent::__construct($name, $espAccountId);
        $creds = EspApiAccount::grabApiUsernameWithPassword($espAccountId);
        $this->auth =  new Authentication($creds['userName'], $creds['password']);
    }

    /**
     * @return Authentication
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param CampaignManagement $curlObject
     */
    public function parseOutResultHeader($curlObject)
    {
        $simpleXml = simplexml_load_string($curlObject->__getLastResponse());
        $header = $simpleXml->children("soap", true)->children('', true)->ResponseHeader;
        return array(
            "errorFlag" => (string)$header->ErrorFlag,
            "returnCode"=> (string)$header->ReturnCode,
            "returnMessage"=> (string)$header->ReturnMessage
        );

    }
}
