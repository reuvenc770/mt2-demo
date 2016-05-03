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
            if ( !$simpleXml->asXml() ) {
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

    public function runUnsubReport() {
        $xmlQuery = $this->buildUnsubSearchQuery();
        $report = new RunReport($this->api->getAuth(), $xmlQuery);
        $result = $this->contactManager->RunReport($report)->getRunReportResult();

        if (empty($result)) {
            echo $this->contactManager->__getLastResponse(). "\n";
            throw new \Exception("Something went wrong getting Creating Report\n\n");
        }
        $data = array(
            "reportID" => $result->getReportTicketId(),
            "totalRows" => $result->getRowCount()
        );
        return $data;
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


      private function buildUnsubSearchQuery(){
    return '<contactssearchcriteria>
<version major="2" minor="0" build="0" revision="0" />
<accountid>254360</accountid>
<set>Partial</set>
<evaluatedefault>True</evaluatedefault>
<group>
  <filter>
     <filtertype>SearchAttributeValue</filtertype>
     <systemattributeid>11</systemattributeid>
     <action>
        <type>DDMMYY</type>
        <operator>WithinLastNDays</operator>
        <value>5</value>
     </action>
  </filter>
</group>
<group>
  <relation>And</relation>
  <filter>
     <filtertype>SearchAttributeValue</filtertype>
     <systemattributeid>1</systemattributeid>
     <action>
        <type>Numeric</type>
        <operator>EqualTo</operator>
        <value>1</value>
     </action>
  </filter>
  <filter>
     <relation>Or</relation>
     <filtertype>SearchAttributeValue</filtertype>
     <systemattributeid>1</systemattributeid>
     <action>
        <type>Numeric</type>
        <operator>EqualTo</operator>
        <value>3</value>
     </action>
  </filter>
</group>
</contactssearchcriteria>';
    }
}


