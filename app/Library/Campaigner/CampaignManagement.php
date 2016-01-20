<?php
namespace App\Library\Campaigner;
class CampaignManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'ListCampaigns' => '\\ListCampaigns',
      'Authentication' => '\\Authentication',
      'CampaignFilter' => '\\CampaignFilter',
      'ArrayOfInt' => '\\ArrayOfInt',
      'ArrayOfString' => '\\ArrayOfString',
      'DateTimeFilter' => '\\DateTimeFilter',
      'ListCampaignsResponse' => '\\ListCampaignsResponse',
      'ArrayOfCampaignDescription' => '\\ArrayOfCampaignDescription',
      'CampaignDescription' => '\\CampaignDescription',
      'ResponseHeader' => '\\ResponseHeader',
      'GetCampaignRunsSummaryReport' => '\\GetCampaignRunsSummaryReport',
      'GetCampaignRunsSummaryReportResponse' => '\\GetCampaignRunsSummaryReportResponse',
      'ArrayOfCampaign' => '\\ArrayOfCampaign',
      'Campaign' => '\\Campaign',
      'ArrayOfCampaignRun' => '\\ArrayOfCampaignRun',
      'CampaignRun' => '\\CampaignRun',
      'ArrayOfDomain' => '\\ArrayOfDomain',
      'Domain' => '\\Domain',
      'DeliveryResult' => '\\DeliveryResult',
      'ActivityResult' => '\\ActivityResult',
      'GetTrackedLinkSummaryReport' => '\\GetTrackedLinkSummaryReport',
      'GetTrackedLinkSummaryReportResponse' => '\\GetTrackedLinkSummaryReportResponse',
      'ArrayOfTrackedLinkSummaryData' => '\\ArrayOfTrackedLinkSummaryData',
      'TrackedLinkSummaryData' => '\\TrackedLinkSummaryData',
      'GetCampaignSummary' => '\\GetCampaignSummary',
      'GetCampaignSummaryResponse' => '\\GetCampaignSummaryResponse',
      'CampaignSummary' => '\\CampaignSummary',
      'CampaignData' => '\\CampaignData',
      'SubscriptionSettings' => '\\SubscriptionSettings',
      'MailingAddressSettings' => '\\MailingAddressSettings',
      'SocialSharingSettings' => '\\SocialSharingSettings',
      'ViewOnlineSettings' => '\\ViewOnlineSettings',
      'CampaignRecipientsData' => '\\CampaignRecipientsData',
      'CampaignScheduleData' => '\\CampaignScheduleData',
      'CreateUpdateCampaign' => '\\CreateUpdateCampaign',
      'CreateUpdateCampaignResponse' => '\\CreateUpdateCampaignResponse',
      'CreateUpdateCampaignResult' => '\\CreateUpdateCampaignResult',
      'DeleteCampaign' => '\\DeleteCampaign',
      'DeleteCampaignResponse' => '\\DeleteCampaignResponse',
      'ScheduleCampaign' => '\\ScheduleCampaign',
      'ScheduleCampaignResponse' => '\\ScheduleCampaignResponse',
      'SendTestCampaign' => '\\SendTestCampaign',
      'ContactKey' => '\\ContactKey',
      'SendTestCampaignResponse' => '\\SendTestCampaignResponse',
      'StopCampaign' => '\\StopCampaign',
      'StopCampaignResponse' => '\\StopCampaignResponse',
      'ListFromEmails' => '\\ListFromEmails',
      'ListFromEmailsResponse' => '\\ListFromEmailsResponse',
      'ArrayOfFromEmailDescription' => '\\ArrayOfFromEmailDescription',
      'FromEmailDescription' => '\\FromEmailDescription',
      'SetCampaignRecipients' => '\\SetCampaignRecipients',
      'SetCampaignRecipientsResponse' => '\\SetCampaignRecipientsResponse',
      'ValidateFromEmail' => '\\ValidateFromEmail',
      'ValidateFromEmailResponse' => '\\ValidateFromEmailResponse',
      'GetUnsubscribeMessages' => '\\GetUnsubscribeMessages',
      'GetUnsubscribeMessagesResponse' => '\\GetUnsubscribeMessagesResponse',
      'ArrayOfUnsubscribeMessageData' => '\\ArrayOfUnsubscribeMessageData',
      'UnsubscribeMessageData' => '\\UnsubscribeMessageData',
      'DeleteFromEmail' => '\\DeleteFromEmail',
      'DeleteFromEmailResponse' => '\\DeleteFromEmailResponse',
      'ListTrackedLinksByCampaign' => '\\ListTrackedLinksByCampaign',
      'ListTrackedLinksByCampaignResponse' => '\\ListTrackedLinksByCampaignResponse',
      'ArrayOfTrackedLinkDescription' => '\\ArrayOfTrackedLinkDescription',
      'TrackedLinkDescription' => '\\TrackedLinkDescription',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/campaignmanagement.asmx?WSDL')
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
    ), $options);
      parent::__construct($wsdl, $options);
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
