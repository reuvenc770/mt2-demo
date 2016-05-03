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

class CampaignerApi extends EspBaseAPI
{
    private  $auth;
    const ESP_NAME = "Campaigner";

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
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
     * Empty method to maintain contract with interface IApi
     * This is handled by the sdk
     */
    public function sendApiRequest() {}

    /**
     * @param CampaignManagement $curlObject
     */
    public function parseOutResultHeader($curlObject)
    {
        try{
        $simpleXml = simplexml_load_string($curlObject->__getLastResponse());
            if ( $simpleXml === false || !$simpleXml->asXml() ) {
                $errors = libxml_get_errors();
                echo "Errors:" . PHP_EOL;
                var_dump($errors);
                throw new \Exception( 'Failed to retrieve SOAP response. tried to check headers' );
            }

            $header = $simpleXml->children("soap", true)->children('', true)->ResponseHeader;
            return array(
                "errorFlag" => (string)$header->ErrorFlag,
                "returnCode"=> (string)$header->ReturnCode,
                "returnMessage"=> (string)$header->ReturnMessage
            );
        } catch (\Exception $e){
            throw new \Exception($e->getMessage(). " ". $e->getCode());
        }
    }

    public function buildCampaignSearchQuery($campaign)
    {
        return "<contactssearchcriteria>
  <version major=\"2\" minor=\"0\" build=\"0\" revision=\"0\"/>
  <set>Partial</set>
  <evaluatedefault>True</evaluatedefault>
  <group>
    <filter>
      <filtertype>EmailAction</filtertype>
      <campaign>
        <campaignrunid>{$campaign}</campaignrunid>
      </campaign>
      <action>
        <status>Do</status>
        <operator>Sent</operator>
      </action>
    </filter>
  </group>
</contactssearchcriteria>";
    }
}
