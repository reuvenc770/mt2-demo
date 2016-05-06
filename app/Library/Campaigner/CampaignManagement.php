<?php
namespace App\Library\Campaigner;
class CampaignManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'ListCampaigns' => 'App\\Library\\Campaigner\\ListCampaigns',
      'Authentication' => 'App\\Library\\Campaigner\\Authentication',
      'CampaignFilter' => 'App\\Library\\Campaigner\\CampaignFilter',
      'ArrayOfInt' => 'App\\Library\\Campaigner\\ArrayOfInt',
      'ArrayOfString' => 'App\\Library\\Campaigner\\ArrayOfString',
      'DateTimeFilter' => 'App\\Library\\Campaigner\\DateTimeFilter',
      'ListCampaignsResponse' => 'App\\Library\\Campaigner\\ListCampaignsResponse',
      'ArrayOfCampaignDescription' => 'App\\Library\\Campaigner\\ArrayOfCampaignDescription',
      'CampaignDescription' => 'App\\Library\\Campaigner\\CampaignDescription',
      'ResponseHeader' => 'App\\Library\\Campaigner\\ResponseHeader',
      'GetCampaignRunsSummaryReport' => 'App\\Library\\Campaigner\\GetCampaignRunsSummaryReport',
      'GetCampaignRunsSummaryReportResponse' => 'App\\Library\\Campaigner\\GetCampaignRunsSummaryReportResponse',
      'ArrayOfCampaign' => 'App\\Library\\Campaigner\\ArrayOfCampaign',
      'Campaign' => 'App\\Library\\Campaigner\\Campaign',
      'ArrayOfCampaignRun' => 'App\\Library\\Campaigner\\ArrayOfCampaignRun',
      'CampaignRun' => 'App\\Library\\Campaigner\\CampaignRun',
      'ArrayOfDomain' => 'App\\Library\\Campaigner\\ArrayOfDomain',
      'Domain' => 'App\\Library\\Campaigner\\Domain',
      'DeliveryResult' => 'App\\Library\\Campaigner\\DeliveryResult',
      'ActivityResult' => 'App\\Library\\Campaigner\\ActivityResult',
      'GetTrackedLinkSummaryReport' => 'App\\Library\\Campaigner\\GetTrackedLinkSummaryReport',
      'GetTrackedLinkSummaryReportResponse' => 'App\\Library\\Campaigner\\GetTrackedLinkSummaryReportResponse',
      'ArrayOfTrackedLinkSummaryData' => 'App\\Library\\Campaigner\\ArrayOfTrackedLinkSummaryData',
      'TrackedLinkSummaryData' => 'App\\Library\\Campaigner\\TrackedLinkSummaryData',
      'GetCampaignSummary' => 'App\\Library\\Campaigner\\GetCampaignSummary',
      'GetCampaignSummaryResponse' => 'App\\Library\\Campaigner\\GetCampaignSummaryResponse',
      'CampaignSummary' => 'App\\Library\\Campaigner\\CampaignSummary',
      'CampaignData' => 'App\\Library\\Campaigner\\CampaignData',
      'SubscriptionSettings' => 'App\\Library\\Campaigner\\SubscriptionSettings',
      'MailingAddressSettings' => 'App\\Library\\Campaigner\\MailingAddressSettings',
      'SocialSharingSettings' => 'App\\Library\\Campaigner\\SocialSharingSettings',
      'ViewOnlineSettings' => 'App\\Library\\Campaigner\\ViewOnlineSettings',
      'CampaignRecipientsData' => 'App\\Library\\Campaigner\\CampaignRecipientsData',
      'CampaignScheduleData' => 'App\\Library\\Campaigner\\CampaignScheduleData',
      'CreateUpdateCampaign' => 'App\\Library\\Campaigner\\CreateUpdateCampaign',
      'CreateUpdateCampaignResponse' => 'App\\Library\\Campaigner\\CreateUpdateCampaignResponse',
      'CreateUpdateCampaignResult' => 'App\\Library\\Campaigner\\CreateUpdateCampaignResult',
      'DeleteCampaign' => 'App\\Library\\Campaigner\\DeleteCampaign',
      'DeleteCampaignResponse' => 'App\\Library\\Campaigner\\DeleteCampaignResponse',
      'ScheduleCampaign' => 'App\\Library\\Campaigner\\ScheduleCampaign',
      'ScheduleCampaignResponse' => 'App\\Library\\Campaigner\\ScheduleCampaignResponse',
      'SendTestCampaign' => 'App\\Library\\Campaigner\\SendTestCampaign',
      'ContactKey' => 'App\\Library\\Campaigner\\ContactKey',
      'SendTestCampaignResponse' => 'App\\Library\\Campaigner\\SendTestCampaignResponse',
      'StopCampaign' => 'App\\Library\\Campaigner\\StopCampaign',
      'StopCampaignResponse' => 'App\\Library\\Campaigner\\StopCampaignResponse',
      'ListFromEmails' => 'App\\Library\\Campaigner\\ListFromEmails',
      'ListFromEmailsResponse' => 'App\\Library\\Campaigner\\ListFromEmailsResponse',
      'ArrayOfFromEmailDescription' => 'App\\Library\\Campaigner\\ArrayOfFromEmailDescription',
      'FromEmailDescription' => 'App\\Library\\Campaigner\\FromEmailDescription',
      'SetCampaignRecipients' => 'App\\Library\\Campaigner\\SetCampaignRecipients',
      'SetCampaignRecipientsResponse' => 'App\\Library\\Campaigner\\SetCampaignRecipientsResponse',
      'ValidateFromEmail' => 'App\\Library\\Campaigner\\ValidateFromEmail',
      'ValidateFromEmailResponse' => 'App\\Library\\Campaigner\\ValidateFromEmailResponse',
      'GetUnsubscribeMessages' => 'App\\Library\\Campaigner\\GetUnsubscribeMessages',
      'GetUnsubscribeMessagesResponse' => 'App\\Library\\Campaigner\\GetUnsubscribeMessagesResponse',
      'ArrayOfUnsubscribeMessageData' => 'App\\Library\\Campaigner\\ArrayOfUnsubscribeMessageData',
      'UnsubscribeMessageData' => 'App\\Library\\Campaigner\\UnsubscribeMessageData',
      'DeleteFromEmail' => 'App\\Library\\Campaigner\\DeleteFromEmail',
      'DeleteFromEmailResponse' => 'App\\Library\\Campaigner\\DeleteFromEmailResponse',
      'ListTrackedLinksByCampaign' => 'App\\Library\\Campaigner\\ListTrackedLinksByCampaign',
      'ListTrackedLinksByCampaignResponse' => 'App\\Library\\Campaigner\\ListTrackedLinksByCampaignResponse',
      'ArrayOfTrackedLinkDescription' => 'App\\Library\\Campaigner\\ArrayOfTrackedLinkDescription',
      'TrackedLinkDescription' => 'App\\Library\\Campaigner\\TrackedLinkDescription',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @throws \Exception
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/campaignmanagement.asmx?WSDL')
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
        $options = array_merge(array (
            'exceptions' => false,
            'compression'=> SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
            'soap_version'=> 'SOAP_1_1',
            'trace' => true,
            'connection_timeout' => 300
        ), $options);
        try {
            parent::__construct($wsdl, $options);
        } catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * List all existing Campaigns on the account.
     *
     * @param ListCampaigns $parameters
     * @return ListCampaignsResponse
     */
    public function ListCampaigns(ListCampaigns $parameters)
    {
      return $this->__soapCall('ListCampaigns', array($parameters));
    }

    /**
     * Returns the summary of a specific campaign run.
     *
     * @param GetCampaignRunsSummaryReport $parameters
     * @return GetCampaignRunsSummaryReportResponse
     */
    public function GetCampaignRunsSummaryReport(GetCampaignRunsSummaryReport $parameters)
    {
      return $this->__soapCall('GetCampaignRunsSummaryReport', array($parameters));
    }

    /**
     * Returns data about trackable links in a campaign.
     *
     * @param GetTrackedLinkSummaryReport $parameters
     * @return GetTrackedLinkSummaryReportResponse
     */
    public function GetTrackedLinkSummaryReport(GetTrackedLinkSummaryReport $parameters)
    {
      return $this->__soapCall('GetTrackedLinkSummaryReport', array($parameters));
    }

    /**
     * Returns data about a specific campaign.
     *
     * @param GetCampaignSummary $parameters
     * @return GetCampaignSummaryResponse
     */
    public function GetCampaignSummary(GetCampaignSummary $parameters)
    {
      return $this->__soapCall('GetCampaignSummary', array($parameters));
    }

    /**
     * Creates or updates an existing campaign with the given campaign data
     *
     * @param CreateUpdateCampaign $parameters
     * @return CreateUpdateCampaignResponse
     */
    public function CreateUpdateCampaign(CreateUpdateCampaign $parameters)
    {
      return $this->__soapCall('CreateUpdateCampaign', array($parameters));
    }

    /**
     * Deletes the specified campaign and the associated reports if deleteReports is true
     *
     * @param DeleteCampaign $parameters
     * @return DeleteCampaignResponse
     */
    public function DeleteCampaign(DeleteCampaign $parameters)
    {
      return $this->__soapCall('DeleteCampaign', array($parameters));
    }

    /**
     * Schedules the specified campaign either with the provided campaignScheduleData or 2 minutes from now if sendNow is true
     *
     * @param ScheduleCampaign $parameters
     * @return ScheduleCampaignResponse
     */
    public function ScheduleCampaign(ScheduleCampaign $parameters)
    {
      return $this->__soapCall('ScheduleCampaign', array($parameters));
    }

    /**
     * Sends a test of the provided campaign, to the specified recipients, using the given merge field values.
     *
     * @param SendTestCampaign $parameters
     * @return SendTestCampaignResponse
     */
    public function SendTestCampaign(SendTestCampaign $parameters)
    {
      return $this->__soapCall('SendTestCampaign', array($parameters));
    }

    /**
     * Removes the campaign schedule from the scheduling engine and changes the status of the campaign.
     *
     * @param StopCampaign $parameters
     * @return StopCampaignResponse
     */
    public function StopCampaign(StopCampaign $parameters)
    {
      return $this->__soapCall('StopCampaign', array($parameters));
    }

    /**
     * Lists all the validated (can be used as from/reply-to) and pending (can not be used) email addresses associated with the account
     *
     * @param ListFromEmails $parameters
     * @return ListFromEmailsResponse
     */
    public function ListFromEmails(ListFromEmails $parameters)
    {
      return $this->__soapCall('ListFromEmails', array($parameters));
    }

    /**
     * Lists all the validated (can be used as from/reply-to) and pending (can not be used) email addresses associated with the account
     *
     * @param SetCampaignRecipients $parameters
     * @return SetCampaignRecipientsResponse
     */
    public function SetCampaignRecipients(SetCampaignRecipients $parameters)
    {
      return $this->__soapCall('SetCampaignRecipients', array($parameters));
    }

    /**
     * Validate From Email Address
     *
     * @param ValidateFromEmail $parameters
     * @return ValidateFromEmailResponse
     */
    public function ValidateFromEmail(ValidateFromEmail $parameters)
    {
      return $this->__soapCall('ValidateFromEmail', array($parameters));
    }

    /**
     * Returns unsubscribe messages.
     *
     * @param GetUnsubscribeMessages $parameters
     * @return GetUnsubscribeMessagesResponse
     */
    public function GetUnsubscribeMessages(GetUnsubscribeMessages $parameters)
    {
      return $this->__soapCall('GetUnsubscribeMessages', array($parameters));
    }

    /**
     * Delete From Email Address
     *
     * @param DeleteFromEmail $parameters
     * @return DeleteFromEmailResponse
     */
    public function DeleteFromEmail(DeleteFromEmail $parameters)
    {
      return $this->__soapCall('DeleteFromEmail', array($parameters));
    }

    /**
     * Return a list of all tracked links with ID and LinkName associated with the list of campaignIDs
     *
     * @param ListTrackedLinksByCampaign $parameters
     * @return ListTrackedLinksByCampaignResponse
     */
    public function ListTrackedLinksByCampaign(ListTrackedLinksByCampaign $parameters)
    {
      return $this->__soapCall('ListTrackedLinksByCampaign', array($parameters));
    }

}
