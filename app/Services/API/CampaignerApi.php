<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/20/16
 * Time: 10:30 AM
 */

namespace App\Services\API;
use App\Facades\EspAccount;
use App\Library\Campaigner\CampaignManagement;
use App\Library\Campaigner\Authentication;

class CampaignerApi extends EspBaseAPI
{
    private  $auth;

    public function __construct($name, $espAccountId)
    {
        parent::__construct($name, $espAccountId);
        $creds = EspAccount::grabApiUsernameWithPassword($espAccountId);
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
     * Empty method to maintain contract with interface IApi
     * This is handled by the sdk
     */
    public function sendApiRequest() {}

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