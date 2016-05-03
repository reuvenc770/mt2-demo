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
use App\Library\Campaigner\RunReport;
use App\Library\Campaigner\ContactManagement;
use App\Library\Campaigner\DownloadReport;

class CampaignerApi extends EspBaseAPI
{
    private  $auth;
    const ESP_NAME = "Campaigner";

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
        $creds = EspApiAccount::grabApiUsernameWithPassword($espAccountId);
        $this->auth =  new Authentication($creds['userName'], $creds['password']);
        $this->contactManager= new ContactManagement();
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

    public function startUnsubReport($daysBack) {
        $xmlQuery = $this->buildUnsubSearchQuery($daysBack);
        $report = new RunReport($this->getAuth(), $xmlQuery);
        $result = $this->contactManager->RunReport($report);

        if ('SoapFault' === get_class($result)) {
            throw new \Exception("RunReport has returned a SoapFault.");
        }
        else {
            $reportResult = $result->getRunReportResult();

            if (empty($reportResult)) {
                echo $this->contactManager->__getLastResponse(). "\n";
                throw new \Exception("RunReport returned an empty result" . PHP_EOL . PHP_EOL);
            }
            $data = array(
                "ticketId" => $reportResult->getReportTicketId(),
                "totalRows" => $reportResult->getRowCount()
            );
            return $data;
        }
    }

    public function downloadUnsubs($ticket) {
        $ticketId = $ticket['ticketId'];
        $count = $ticket['totalRows'];

        $downloadReport = new DownloadReport($this->auth, $ticketId, 0, $count, "rpt_Contact_Details");
        $this->contactManager->DownloadReport($downloadReport);

        $rawXML = $this->contactManager->__getLastResponse();

        return $rawXML;

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


      private function buildUnsubSearchQuery($daysBack) {
    return "<contactssearchcriteria>
<version major=\"2\" minor=\"0\" build=\"0\" revision=\"0\" />
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
        <value>$daysBack</value>
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
</contactssearchcriteria>";
    }
}


