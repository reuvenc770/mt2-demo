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

use App\Library\Campaigner\ArrayOfInt;
use App\Library\Campaigner\ContactKey;
use App\Library\Campaigner\CustomAttribute;
use App\Library\Campaigner\ArrayOfCustomAttribute;
use App\Library\Campaigner\ContactData;
use App\Library\Campaigner\ImmediateUpload;
use App\Library\Campaigner\ContactManagement;

use Log;

class CampaignerApi extends EspBaseAPI
{
    CONST NO_CAMPAIGNS = 'M_4.1.1.1_NO-CAMPAIGNRUNS-FOUND';
    CONST NO_UNSUBS = 'M_4.1.4.8_XMLCONTACTQUERY-NO-RESULTS-FOUND';
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

    public function checkforHeaderFail($manager, $jobName){
        try {
            $header = $this->parseOutResultHeader($manager);
        } catch (\Exception $e){
            throw new \Exception ($e->getMessage());
        }
        if ($header['errorFlag'] !== "false" ) {
            if  ($header['returnCode'] == self::NO_UNSUBS){
                return true;
            }
            throw new \Exception("{$header['errorFlag']} - {$this->getApiName()}::{$this->getEspAccountId()} Failed {$jobName} because {$header['returnMessage']} - {$header['returnCode']}");
        } else if ($header['returnCode'] == self::NO_CAMPAIGNS) {
            Log::info("{$this->getApiName()}::{$this->getEspAccountId()} had no campaigns");
            return true;
        }
        return false;
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

    public function buildUnsubSearchQuery($lookback){
      return "<contactssearchcriteria>
   <version major=\"2\" minor=\"0\" build=\"0\" revision=\"0\" />
   <set>Partial</set>
   <evaluatedefault>True</evaluatedefault>
   <group>
      <filter>
         <filtertype>SearchAttributeValue</filtertype>
         <systemattributeid>11</systemattributeid>
         <action>
            <type>DDMMYY</type>
            <operator>WithinLastNDays</operator>
            <value>{$lookback}</value>
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
   </group>
</contactssearchcriteria>";
    }

    public function pushRecords($records, $targetId) {
        $contactManager = new ContactManagement();
        $total = 0;

        foreach ($records as $record) {

            $fax = "";
            // These domains are yahoo.com and ymail.com
            if (($record->domainId == 9) or ($record->domainId == 342774)) {

                $key = new ContactKey(0, $record->emailAddress);
                $attribute = new CustomAttribute($record->emailId, 3932683, false);
                $customAttributes = new ArrayOfCustomAttribute();
                $customAttributes->setCustomAttribute([$attribute]);

                $contactArray[] = new ContactData($key, $record->emailAddress, $record->firstName, $record->lastName, $record->phone, $fax, $customAttributes, null, null);
                $contactData = new ArrayOfContactData();

                $arrayOfData = $contactData->setContactData($contactArray);
                $updateExistingContacts = "false";
                $triggerWorkflow = "false";
                $groupIds[] = $targetId;

                $total++;
            }
        }

        $contactList = new ImmediateUpload($this->auth, $updateExistingContacts, $triggerWorkflow, $arrayOfData, new ArrayOfInt($groupIds), null);
        $result = $contactManager->ImmediateUpload($contactList);

        return $total;
    }

    public function addToSuppression($emailAddress, $suppressionLists) {
        $key = new ContactKey(0, $emailAddress);
        $emailId = 0;
        $attribute = new CustomAttribute($emailId, 3932683, false); // Not sure what these are

        $attributesArray = new ArrayOfCustomAttribute();
        $attributesArray->setCustomAttribute(array($attribute));
        
        $contactArray[] = new ContactData($key, $emailAddress, '', '', '', '', $attributesArray, null, null); // see method above for fields
        $contactData = new ArrayOfContactData();

        $arrayOfData = $contactData->setContactData($contactArray);
        $updateExistingContacts = "false";
        $triggerWorkflow = "false";

        $contactList = new ImmediateUpload($this->auth, $updateExistingContacts, $triggerWorkflow, $arrayOfData, new ArrayOfInt($suppressionLists), null);
        $result = $contactManager->ImmediateUpload($contactList);
    }
}
