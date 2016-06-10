<?php
namespace App\Library\Bronto;
class BrontoSoapApiImplService extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'readLogins' => 'App\\Library\Bronto\\readLogins',
      'loginFilter' => 'App\\Library\Bronto\\loginFilter',
      'stringValue' => 'App\\Library\Bronto\\stringValue',
      'sessionHeader' => 'App\\Library\Bronto\\sessionHeader',
      'readLoginsResponse' => 'App\\Library\Bronto\\readLoginsResponse',
      'loginObject' => 'App\\Library\Bronto\\loginObject',
      'contactInformation' => 'App\\Library\Bronto\\contactInformation',
      'deleteLogins' => 'App\\Library\Bronto\\deleteLogins',
      'deleteLoginsResponse' => 'App\\Library\Bronto\\deleteLoginsResponse',
      'writeResult' => 'App\\Library\Bronto\\writeResult',
      'resultItem' => 'App\\Library\Bronto\\resultItem',
      'deleteDeliveryGroup' => 'App\\Library\Bronto\\deleteDeliveryGroup',
      'deliveryGroupObject' => 'App\\Library\Bronto\\deliveryGroupObject',
      'deleteDeliveryGroupResponse' => 'App\\Library\Bronto\\deleteDeliveryGroupResponse',
      'addContactsToWorkflow' => 'App\\Library\Bronto\\addContactsToWorkflow',
      'workflowObject' => 'App\\Library\Bronto\\workflowObject',
      'contactObject' => 'App\\Library\Bronto\\contactObject',
      'contactField' => 'App\\Library\Bronto\\contactField',
      'readOnlyContactData' => 'App\\Library\Bronto\\readOnlyContactData',
      'addContactsToWorkflowResponse' => 'App\\Library\Bronto\\addContactsToWorkflowResponse',
      'readApiTokens' => 'App\\Library\Bronto\\readApiTokens',
      'apiTokenFilter' => 'App\\Library\Bronto\\apiTokenFilter',
      'readApiTokensResponse' => 'App\\Library\Bronto\\readApiTokensResponse',
      'apiTokenObject' => 'App\\Library\Bronto\\apiTokenObject',
      'updateMessageRules' => 'App\\Library\Bronto\\updateMessageRules',
      'messageRuleObject' => 'App\\Library\Bronto\\messageRuleObject',
      'updateMessageRulesResponse' => 'App\\Library\Bronto\\updateMessageRulesResponse',
      'deleteMessageRules' => 'App\\Library\Bronto\\deleteMessageRules',
      'deleteMessageRulesResponse' => 'App\\Library\Bronto\\deleteMessageRulesResponse',
      'readLists' => 'App\\Library\Bronto\\readLists',
      'mailListFilter' => 'App\\Library\Bronto\\mailListFilter',
      'readListsResponse' => 'App\\Library\Bronto\\readListsResponse',
      'mailListObject' => 'App\\Library\Bronto\\mailListObject',
      'deleteMessages' => 'App\\Library\Bronto\\deleteMessages',
      'messageObject' => 'App\\Library\Bronto\\messageObject',
      'messageContentObject' => 'App\\Library\Bronto\\messageContentObject',
      'deleteMessagesResponse' => 'App\\Library\Bronto\\deleteMessagesResponse',
      'updateSMSDeliveries' => 'App\\Library\Bronto\\updateSMSDeliveries',
      'smsDeliveryObject' => 'App\\Library\Bronto\\smsDeliveryObject',
      'deliveryRecipientObject' => 'App\\Library\Bronto\\deliveryRecipientObject',
      'smsDeliveryContactsObject' => 'App\\Library\Bronto\\smsDeliveryContactsObject',
      'smsMessageFieldObject' => 'App\\Library\Bronto\\smsMessageFieldObject',
      'updateSMSDeliveriesResponse' => 'App\\Library\Bronto\\updateSMSDeliveriesResponse',
      'readMessageFolders' => 'App\\Library\Bronto\\readMessageFolders',
      'messageFolderFilter' => 'App\\Library\Bronto\\messageFolderFilter',
      'readMessageFoldersResponse' => 'App\\Library\Bronto\\readMessageFoldersResponse',
      'messageFolderObject' => 'App\\Library\Bronto\\messageFolderObject',
      'addUpdateOrder' => 'App\\Library\Bronto\\addUpdateOrder',
      'orderObject' => 'App\\Library\Bronto\\orderObject',
      'productObject' => 'App\\Library\Bronto\\productObject',
      'addUpdateOrderResponse' => 'App\\Library\Bronto\\addUpdateOrderResponse',
      'updateDeliveryGroup' => 'App\\Library\Bronto\\updateDeliveryGroup',
      'updateDeliveryGroupResponse' => 'App\\Library\Bronto\\updateDeliveryGroupResponse',
      'readHeaderFooters' => 'App\\Library\Bronto\\readHeaderFooters',
      'headerFooterFilter' => 'App\\Library\Bronto\\headerFooterFilter',
      'readHeaderFootersResponse' => 'App\\Library\Bronto\\readHeaderFootersResponse',
      'headerFooterObject' => 'App\\Library\Bronto\\headerFooterObject',
      'deleteApiTokens' => 'App\\Library\Bronto\\deleteApiTokens',
      'deleteApiTokensResponse' => 'App\\Library\Bronto\\deleteApiTokensResponse',
      'addFields' => 'App\\Library\Bronto\\addFields',
      'fieldObject' => 'App\\Library\Bronto\\fieldObject',
      'fieldOptionObject' => 'App\\Library\Bronto\\fieldOptionObject',
      'addFieldsResponse' => 'App\\Library\Bronto\\addFieldsResponse',
      'deleteHeaderFooters' => 'App\\Library\Bronto\\deleteHeaderFooters',
      'deleteHeaderFootersResponse' => 'App\\Library\Bronto\\deleteHeaderFootersResponse',
      'deleteWorkflows' => 'App\\Library\Bronto\\deleteWorkflows',
      'deleteWorkflowsResponse' => 'App\\Library\Bronto\\deleteWorkflowsResponse',
      'addToList' => 'App\\Library\Bronto\\addToList',
      'addToListResponse' => 'App\\Library\Bronto\\addToListResponse',
      'updateContentTags' => 'App\\Library\Bronto\\updateContentTags',
      'contentTagObject' => 'App\\Library\Bronto\\contentTagObject',
      'updateContentTagsResponse' => 'App\\Library\Bronto\\updateContentTagsResponse',
      'readActivities' => 'App\\Library\Bronto\\readActivities',
      'activityFilter' => 'App\\Library\Bronto\\activityFilter',
      'readActivitiesResponse' => 'App\\Library\Bronto\\readActivitiesResponse',
      'activityObject' => 'App\\Library\Bronto\\activityObject',
      'addSMSMessages' => 'App\\Library\Bronto\\addSMSMessages',
      'smsMessageObject' => 'App\\Library\Bronto\\smsMessageObject',
      'addSMSMessagesResponse' => 'App\\Library\Bronto\\addSMSMessagesResponse',
      'readConversions' => 'App\\Library\Bronto\\readConversions',
      'conversionFilter' => 'App\\Library\Bronto\\conversionFilter',
      'readConversionsResponse' => 'App\\Library\Bronto\\readConversionsResponse',
      'conversionObject' => 'App\\Library\Bronto\\conversionObject',
      'deleteContacts' => 'App\\Library\Bronto\\deleteContacts',
      'deleteContactsResponse' => 'App\\Library\Bronto\\deleteContactsResponse',
      'addDeliveryGroup' => 'App\\Library\Bronto\\addDeliveryGroup',
      'addDeliveryGroupResponse' => 'App\\Library\Bronto\\addDeliveryGroupResponse',
      'updateSMSKeywords' => 'App\\Library\Bronto\\updateSMSKeywords',
      'smsKeywordObject' => 'App\\Library\Bronto\\smsKeywordObject',
      'updateSMSKeywordsResponse' => 'App\\Library\Bronto\\updateSMSKeywordsResponse',
      'updateMessages' => 'App\\Library\Bronto\\updateMessages',
      'updateMessagesResponse' => 'App\\Library\Bronto\\updateMessagesResponse',
      'readUnsubscribes' => 'App\\Library\Bronto\\readUnsubscribes',
      'unsubscribeFilter' => 'App\\Library\Bronto\\unsubscribeFilter',
      'dateTime' => 'App\\Library\Bronto\\dateTimeCustom',
      'baseDateTime' => 'App\\Library\Bronto\\baseDateTime',
      'abstractDateTime' => 'App\\Library\Bronto\\abstractDateTime',
      'abstractInstant' => 'App\\Library\Bronto\\abstractInstant',
      'readUnsubscribesResponse' => 'App\\Library\Bronto\\readUnsubscribesResponse',
      'unsubscribeObject' => 'App\\Library\Bronto\\unsubscribeObject',
      'readContacts' => 'App\\Library\Bronto\\readContacts',
      'contactFilter' => 'App\\Library\Bronto\\contactFilter',
      'dateValue' => 'App\\Library\Bronto\\dateValue',
      'readContactsResponse' => 'App\\Library\Bronto\\readContactsResponse',
      'readRecentOutboundActivities' => 'App\\Library\Bronto\\readRecentOutboundActivities',
      'recentOutboundActivitySearchRequest' => 'App\\Library\Bronto\\recentOutboundActivitySearchRequest',
      'recentActivitySearchRequest' => 'App\\Library\Bronto\\recentActivitySearchRequest',
      'readRecentOutboundActivitiesResponse' => 'App\\Library\Bronto\\readRecentOutboundActivitiesResponse',
      'recentActivityObject' => 'App\\Library\Bronto\\recentActivityObject',
      'addContentTags' => 'App\\Library\Bronto\\addContentTags',
      'addContentTagsResponse' => 'App\\Library\Bronto\\addContentTagsResponse',
      'updateDeliveries' => 'App\\Library\Bronto\\updateDeliveries',
      'deliveryObject' => 'App\\Library\Bronto\\deliveryObject',
      'messageFieldObject' => 'App\\Library\Bronto\\messageFieldObject',
      'deliveryProductObject' => 'App\\Library\Bronto\\deliveryProductObject',
      'remailObject' => 'App\\Library\Bronto\\remailObject',
      'updateDeliveriesResponse' => 'App\\Library\Bronto\\updateDeliveriesResponse',
      'deleteSMSMessages' => 'App\\Library\Bronto\\deleteSMSMessages',
      'deleteSMSMessagesResponse' => 'App\\Library\Bronto\\deleteSMSMessagesResponse',
      'addSMSKeywords' => 'App\\Library\Bronto\\addSMSKeywords',
      'addSMSKeywordsResponse' => 'App\\Library\Bronto\\addSMSKeywordsResponse',
      'readWorkflows' => 'App\\Library\Bronto\\readWorkflows',
      'workflowFilter' => 'App\\Library\Bronto\\workflowFilter',
      'readWorkflowsResponse' => 'App\\Library\Bronto\\readWorkflowsResponse',
      'updateApiTokens' => 'App\\Library\Bronto\\updateApiTokens',
      'updateApiTokensResponse' => 'App\\Library\Bronto\\updateApiTokensResponse',
      'readAccounts' => 'App\\Library\Bronto\\readAccounts',
      'accountFilter' => 'App\\Library\Bronto\\accountFilter',
      'readAccountsResponse' => 'App\\Library\Bronto\\readAccountsResponse',
      'accountObject' => 'App\\Library\Bronto\\accountObject',
      'generalSettings' => 'App\\Library\Bronto\\generalSettings',
      'formatSettings' => 'App\\Library\Bronto\\formatSettings',
      'brandingSettings' => 'App\\Library\Bronto\\brandingSettings',
      'repliesSettings' => 'App\\Library\Bronto\\repliesSettings',
      'accountAllocations' => 'App\\Library\Bronto\\accountAllocations',
      'addToSMSKeyword' => 'App\\Library\Bronto\\addToSMSKeyword',
      'addToSMSKeywordResponse' => 'App\\Library\Bronto\\addToSMSKeywordResponse',
      'removeFromList' => 'App\\Library\Bronto\\removeFromList',
      'removeFromListResponse' => 'App\\Library\Bronto\\removeFromListResponse',
      'readDeliveryRecipients' => 'App\\Library\Bronto\\readDeliveryRecipients',
      'deliveryRecipientFilter' => 'App\\Library\Bronto\\deliveryRecipientFilter',
      'readDeliveryRecipientsResponse' => 'App\\Library\Bronto\\readDeliveryRecipientsResponse',
      'deliveryRecipientStatObject' => 'App\\Library\Bronto\\deliveryRecipientStatObject',
      'addLists' => 'App\\Library\Bronto\\addLists',
      'addListsResponse' => 'App\\Library\Bronto\\addListsResponse',
      'readSegments' => 'App\\Library\Bronto\\readSegments',
      'segmentFilter' => 'App\\Library\Bronto\\segmentFilter',
      'readSegmentsResponse' => 'App\\Library\Bronto\\readSegmentsResponse',
      'segmentObject' => 'App\\Library\Bronto\\segmentObject',
      'segmentRuleObject' => 'App\\Library\Bronto\\segmentRuleObject',
      'segmentCriteriaObject' => 'App\\Library\Bronto\\segmentCriteriaObject',
      'readSMSKeywords' => 'App\\Library\Bronto\\readSMSKeywords',
      'smsKeywordFilter' => 'App\\Library\Bronto\\smsKeywordFilter',
      'readSMSKeywordsResponse' => 'App\\Library\Bronto\\readSMSKeywordsResponse',
      'readRecentInboundActivities' => 'App\\Library\Bronto\\readRecentInboundActivities',
      'recentInboundActivitySearchRequest' => 'App\\Library\Bronto\\recentInboundActivitySearchRequest',
      'readRecentInboundActivitiesResponse' => 'App\\Library\Bronto\\readRecentInboundActivitiesResponse',
      'addDeliveries' => 'App\\Library\Bronto\\addDeliveries',
      'addDeliveriesResponse' => 'App\\Library\Bronto\\addDeliveriesResponse',
      'addContacts' => 'App\\Library\Bronto\\addContacts',
      'addContactsResponse' => 'App\\Library\Bronto\\addContactsResponse',
      'addContactEvent' => 'App\\Library\Bronto\\addContactEvent',
      'addContactEventResponse' => 'App\\Library\Bronto\\addContactEventResponse',
      'deleteDeliveries' => 'App\\Library\Bronto\\deleteDeliveries',
      'deleteDeliveriesResponse' => 'App\\Library\Bronto\\deleteDeliveriesResponse',
      'login' => 'App\\Library\Bronto\\login',
      'loginResponse' => 'App\\Library\Bronto\\loginResponse',
      'deleteOrders' => 'App\\Library\Bronto\\deleteOrders',
      'deleteOrdersResponse' => 'App\\Library\Bronto\\deleteOrdersResponse',
      'addOrUpdateDeliveryGroup' => 'App\\Library\Bronto\\addOrUpdateDeliveryGroup',
      'addOrUpdateDeliveryGroupResponse' => 'App\\Library\Bronto\\addOrUpdateDeliveryGroupResponse',
      'updateMessageFolders' => 'App\\Library\Bronto\\updateMessageFolders',
      'updateMessageFoldersResponse' => 'App\\Library\Bronto\\updateMessageFoldersResponse',
      'addOrUpdateOrders' => 'App\\Library\Bronto\\addOrUpdateOrders',
      'addOrUpdateOrdersResponse' => 'App\\Library\Bronto\\addOrUpdateOrdersResponse',
      'addOrUpdateContacts' => 'App\\Library\Bronto\\addOrUpdateContacts',
      'addOrUpdateContactsResponse' => 'App\\Library\Bronto\\addOrUpdateContactsResponse',
      'readDeliveries' => 'App\\Library\Bronto\\readDeliveries',
      'deliveryFilter' => 'App\\Library\Bronto\\deliveryFilter',
      'readDeliveriesResponse' => 'App\\Library\Bronto\\readDeliveriesResponse',
      'readSMSDeliveries' => 'App\\Library\Bronto\\readSMSDeliveries',
      'smsDeliveryFilter' => 'App\\Library\Bronto\\smsDeliveryFilter',
      'readSMSDeliveriesResponse' => 'App\\Library\Bronto\\readSMSDeliveriesResponse',
      'updateLists' => 'App\\Library\Bronto\\updateLists',
      'updateListsResponse' => 'App\\Library\Bronto\\updateListsResponse',
      'readContentTags' => 'App\\Library\Bronto\\readContentTags',
      'contentTagFilter' => 'App\\Library\Bronto\\contentTagFilter',
      'readContentTagsResponse' => 'App\\Library\Bronto\\readContentTagsResponse',
      'addAccounts' => 'App\\Library\Bronto\\addAccounts',
      'addAccountsResponse' => 'App\\Library\Bronto\\addAccountsResponse',
      'deleteLists' => 'App\\Library\Bronto\\deleteLists',
      'deleteListsResponse' => 'App\\Library\Bronto\\deleteListsResponse',
      'deleteContentTags' => 'App\\Library\Bronto\\deleteContentTags',
      'deleteContentTagsResponse' => 'App\\Library\Bronto\\deleteContentTagsResponse',
      'removeFromSMSKeyword' => 'App\\Library\Bronto\\removeFromSMSKeyword',
      'removeFromSMSKeywordResponse' => 'App\\Library\Bronto\\removeFromSMSKeywordResponse',
      'addMessages' => 'App\\Library\Bronto\\addMessages',
      'addMessagesResponse' => 'App\\Library\Bronto\\addMessagesResponse',
      'readFields' => 'App\\Library\Bronto\\readFields',
      'fieldsFilter' => 'App\\Library\Bronto\\fieldsFilter',
      'readFieldsResponse' => 'App\\Library\Bronto\\readFieldsResponse',
      'addHeaderFooters' => 'App\\Library\Bronto\\addHeaderFooters',
      'addHeaderFootersResponse' => 'App\\Library\Bronto\\addHeaderFootersResponse',
      'updateFields' => 'App\\Library\Bronto\\updateFields',
      'updateFieldsResponse' => 'App\\Library\Bronto\\updateFieldsResponse',
      'deleteFromDeliveryGroup' => 'App\\Library\Bronto\\deleteFromDeliveryGroup',
      'deleteFromDeliveryGroupResponse' => 'App\\Library\Bronto\\deleteFromDeliveryGroupResponse',
      'clearLists' => 'App\\Library\Bronto\\clearLists',
      'clearListsResponse' => 'App\\Library\Bronto\\clearListsResponse',
      'addMessageRules' => 'App\\Library\Bronto\\addMessageRules',
      'addMessageRulesResponse' => 'App\\Library\Bronto\\addMessageRulesResponse',
      'updateSMSMessages' => 'App\\Library\Bronto\\updateSMSMessages',
      'updateSMSMessagesResponse' => 'App\\Library\Bronto\\updateSMSMessagesResponse',
      'deleteSMSKeywords' => 'App\\Library\Bronto\\deleteSMSKeywords',
      'deleteSMSKeywordsResponse' => 'App\\Library\Bronto\\deleteSMSKeywordsResponse',
      'logout' => 'App\\Library\Bronto\\logout',
      'logoutResponse' => 'App\\Library\Bronto\\logoutResponse',
      'addMessageFolders' => 'App\\Library\Bronto\\addMessageFolders',
      'addMessageFoldersResponse' => 'App\\Library\Bronto\\addMessageFoldersResponse',
      'readMessages' => 'App\\Library\Bronto\\readMessages',
      'messageFilter' => 'App\\Library\Bronto\\messageFilter',
      'readMessagesResponse' => 'App\\Library\Bronto\\readMessagesResponse',
      'deleteAccounts' => 'App\\Library\Bronto\\deleteAccounts',
      'deleteAccountsResponse' => 'App\\Library\Bronto\\deleteAccountsResponse',
      'readMessageRules' => 'App\\Library\Bronto\\readMessageRules',
      'messageRuleFilter' => 'App\\Library\Bronto\\messageRuleFilter',
      'readMessageRulesResponse' => 'App\\Library\Bronto\\readMessageRulesResponse',
      'addWorkflows' => 'App\\Library\Bronto\\addWorkflows',
      'addWorkflowsResponse' => 'App\\Library\Bronto\\addWorkflowsResponse',
      'updateWorkflows' => 'App\\Library\Bronto\\updateWorkflows',
      'updateWorkflowsResponse' => 'App\\Library\Bronto\\updateWorkflowsResponse',
      'addConversion' => 'App\\Library\Bronto\\addConversion',
      'addConversionResponse' => 'App\\Library\Bronto\\addConversionResponse',
      'updateAccounts' => 'App\\Library\Bronto\\updateAccounts',
      'updateAccountsResponse' => 'App\\Library\Bronto\\updateAccountsResponse',
      'readBounces' => 'App\\Library\Bronto\\readBounces',
      'bounceFilter' => 'App\\Library\Bronto\\bounceFilter',
      'readBouncesResponse' => 'App\\Library\Bronto\\readBouncesResponse',
      'bounceObject' => 'App\\Library\Bronto\\bounceObject',
      'updateHeaderFooters' => 'App\\Library\Bronto\\updateHeaderFooters',
      'updateHeaderFootersResponse' => 'App\\Library\Bronto\\updateHeaderFootersResponse',
      'deleteMessageFolders' => 'App\\Library\Bronto\\deleteMessageFolders',
      'deleteMessageFoldersResponse' => 'App\\Library\Bronto\\deleteMessageFoldersResponse',
      'addLogins' => 'App\\Library\Bronto\\addLogins',
      'addLoginsResponse' => 'App\\Library\Bronto\\addLoginsResponse',
      'updateContacts' => 'App\\Library\Bronto\\updateContacts',
      'updateContactsResponse' => 'App\\Library\Bronto\\updateContactsResponse',
      'readDeliveryGroups' => 'App\\Library\Bronto\\readDeliveryGroups',
      'deliveryGroupFilter' => 'App\\Library\Bronto\\deliveryGroupFilter',
      'readDeliveryGroupsResponse' => 'App\\Library\Bronto\\readDeliveryGroupsResponse',
      'addToDeliveryGroup' => 'App\\Library\Bronto\\addToDeliveryGroup',
      'addToDeliveryGroupResponse' => 'App\\Library\Bronto\\addToDeliveryGroupResponse',
      'addSMSDeliveries' => 'App\\Library\Bronto\\addSMSDeliveries',
      'addSMSDeliveriesResponse' => 'App\\Library\Bronto\\addSMSDeliveriesResponse',
      'deleteSMSDeliveries' => 'App\\Library\Bronto\\deleteSMSDeliveries',
      'deleteSMSDeliveriesResponse' => 'App\\Library\Bronto\\deleteSMSDeliveriesResponse',
      'deleteFields' => 'App\\Library\Bronto\\deleteFields',
      'deleteFieldsResponse' => 'App\\Library\Bronto\\deleteFieldsResponse',
      'readSMSMessages' => 'App\\Library\Bronto\\readSMSMessages',
      'readSMSMessagesResponse' => 'App\\Library\Bronto\\readSMSMessagesResponse',
      'addApiTokens' => 'App\\Library\Bronto\\addApiTokens',
      'addApiTokensResponse' => 'App\\Library\Bronto\\addApiTokensResponse',
      'updateLogins' => 'App\\Library\Bronto\\updateLogins',
      'updateLoginsResponse' => 'App\\Library\Bronto\\updateLoginsResponse',
      'readWebforms' => 'App\\Library\Bronto\\readWebforms',
      'webformFilter' => 'App\\Library\Bronto\\webformFilter',
      'readWebformsResponse' => 'App\\Library\Bronto\\readWebformsResponse',
      'webformObject' => 'App\\Library\Bronto\\webformObject',
      'ApiException' => 'App\\Library\Bronto\\ApiException',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://api.bronto.com/v4?wsdl')
    {
      foreach (self::$classmap as $key => $value) {
        if (!isset($options['classmap'][$key])) {
          $options['classmap'][$key] = $value;
        }
      }
      $options = array_merge(array (
      'features' => 1,
          'compression'=> SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
          'trace' => true,
    ), $options);
      parent::__construct($wsdl, $options);
    }

    /**
     * @param readLogins $parameters
     * @return readLoginsResponse
     */
    public function readLogins(readLogins $parameters)
    {
      return $this->__soapCall('readLogins', array($parameters));
    }

    /**
     * @param deleteLogins $parameters
     * @return deleteLoginsResponse
     */
    public function deleteLogins(deleteLogins $parameters)
    {
      return $this->__soapCall('deleteLogins', array($parameters));
    }

    /**
     * @param addContactsToWorkflow $parameters
     * @return addContactsToWorkflowResponse
     */
    public function addContactsToWorkflow(addContactsToWorkflow $parameters)
    {
      return $this->__soapCall('addContactsToWorkflow', array($parameters));
    }

    /**
     * @param deleteDeliveryGroup $parameters
     * @return deleteDeliveryGroupResponse
     */
    public function deleteDeliveryGroup(deleteDeliveryGroup $parameters)
    {
      return $this->__soapCall('deleteDeliveryGroup', array($parameters));
    }

    /**
     * @param readApiTokens $parameters
     * @return readApiTokensResponse
     */
    public function readApiTokens(readApiTokens $parameters)
    {
      return $this->__soapCall('readApiTokens', array($parameters));
    }

    /**
     * @param updateMessageRules $parameters
     * @return updateMessageRulesResponse
     */
    public function updateMessageRules(updateMessageRules $parameters)
    {
      return $this->__soapCall('updateMessageRules', array($parameters));
    }

    /**
     * @param deleteMessageRules $parameters
     * @return deleteMessageRulesResponse
     */
    public function deleteMessageRules(deleteMessageRules $parameters)
    {
      return $this->__soapCall('deleteMessageRules', array($parameters));
    }

    /**
     * @param deleteMessages $parameters
     * @return deleteMessagesResponse
     */
    public function deleteMessages(deleteMessages $parameters)
    {
      return $this->__soapCall('deleteMessages', array($parameters));
    }

    /**
     * @param readLists $parameters
     * @return readListsResponse
     */
    public function readLists(readLists $parameters)
    {
      return $this->__soapCall('readLists', array($parameters));
    }

    /**
     * @param addUpdateOrder $parameters
     * @return addUpdateOrderResponse
     */
    public function addUpdateOrder(addUpdateOrder $parameters)
    {
      return $this->__soapCall('addUpdateOrder', array($parameters));
    }

    /**
     * @param readMessageFolders $parameters
     * @return readMessageFoldersResponse
     */
    public function readMessageFolders(readMessageFolders $parameters)
    {
      return $this->__soapCall('readMessageFolders', array($parameters));
    }

    /**
     * @param updateSMSDeliveries $parameters
     * @return updateSMSDeliveriesResponse
     */
    public function updateSMSDeliveries(updateSMSDeliveries $parameters)
    {
      return $this->__soapCall('updateSMSDeliveries', array($parameters));
    }

    /**
     * @param updateDeliveryGroup $parameters
     * @return updateDeliveryGroupResponse
     */
    public function updateDeliveryGroup(updateDeliveryGroup $parameters)
    {
      return $this->__soapCall('updateDeliveryGroup', array($parameters));
    }

    /**
     * @param readHeaderFooters $parameters
     * @return readHeaderFootersResponse
     */
    public function readHeaderFooters(readHeaderFooters $parameters)
    {
      return $this->__soapCall('readHeaderFooters', array($parameters));
    }

    /**
     * @param addFields $parameters
     * @return addFieldsResponse
     */
    public function addFields(addFields $parameters)
    {
      return $this->__soapCall('addFields', array($parameters));
    }

    /**
     * @param deleteApiTokens $parameters
     * @return deleteApiTokensResponse
     */
    public function deleteApiTokens(deleteApiTokens $parameters)
    {
      return $this->__soapCall('deleteApiTokens', array($parameters));
    }

    /**
     * @param deleteWorkflows $parameters
     * @return deleteWorkflowsResponse
     */
    public function deleteWorkflows(deleteWorkflows $parameters)
    {
      return $this->__soapCall('deleteWorkflows', array($parameters));
    }

    /**
     * @param deleteHeaderFooters $parameters
     * @return deleteHeaderFootersResponse
     */
    public function deleteHeaderFooters(deleteHeaderFooters $parameters)
    {
      return $this->__soapCall('deleteHeaderFooters', array($parameters));
    }

    /**
     * @param addToList $parameters
     * @return addToListResponse
     */
    public function addToList(addToList $parameters)
    {
      return $this->__soapCall('addToList', array($parameters));
    }

    /**
     * @param addSMSMessages $parameters
     * @return addSMSMessagesResponse
     */
    public function addSMSMessages(addSMSMessages $parameters)
    {
      return $this->__soapCall('addSMSMessages', array($parameters));
    }

    /**
     * @param readActivities $parameters
     * @return readActivitiesResponse
     */
    public function readActivities(readActivities $parameters)
    {
      return $this->__soapCall('readActivities', array($parameters));
    }

    /**
     * @param updateContentTags $parameters
     * @return updateContentTagsResponse
     */
    public function updateContentTags(updateContentTags $parameters)
    {
      return $this->__soapCall('updateContentTags', array($parameters));
    }

    /**
     * @param deleteContacts $parameters
     * @return deleteContactsResponse
     */
    public function deleteContacts(deleteContacts $parameters)
    {
      return $this->__soapCall('deleteContacts', array($parameters));
    }

    /**
     * @param readConversions $parameters
     * @return readConversionsResponse
     */
    public function readConversions(readConversions $parameters)
    {
      return $this->__soapCall('readConversions', array($parameters));
    }

    /**
     * @param addDeliveryGroup $parameters
     * @return addDeliveryGroupResponse
     */
    public function addDeliveryGroup(addDeliveryGroup $parameters)
    {
      return $this->__soapCall('addDeliveryGroup', array($parameters));
    }

    /**
     * @param readUnsubscribes $parameters
     * @return readUnsubscribesResponse
     */
    public function readUnsubscribes(readUnsubscribes $parameters)
    {
      return $this->__soapCall('readUnsubscribes', array($parameters));
    }

    /**
     * @param updateMessages $parameters
     * @return updateMessagesResponse
     */
    public function updateMessages(updateMessages $parameters)
    {
      return $this->__soapCall('updateMessages', array($parameters));
    }

    /**
     * @param updateSMSKeywords $parameters
     * @return updateSMSKeywordsResponse
     */
    public function updateSMSKeywords(updateSMSKeywords $parameters)
    {
      return $this->__soapCall('updateSMSKeywords', array($parameters));
    }

    /**
     * @param readContacts $parameters
     * @return readContactsResponse
     */
    public function readContacts(readContacts $parameters)
    {
      return $this->__soapCall('readContacts', array($parameters));
    }

    /**
     * @param readRecentOutboundActivities $parameters
     * @return readRecentOutboundActivitiesResponse
     */
    public function readRecentOutboundActivities(readRecentOutboundActivities $parameters)
    {
      return $this->__soapCall('readRecentOutboundActivities', array($parameters));
    }

    /**
     * @param addContentTags $parameters
     * @return addContentTagsResponse
     */
    public function addContentTags(addContentTags $parameters)
    {
      return $this->__soapCall('addContentTags', array($parameters));
    }

    /**
     * @param updateDeliveries $parameters
     * @return updateDeliveriesResponse
     */
    public function updateDeliveries(updateDeliveries $parameters)
    {
      return $this->__soapCall('updateDeliveries', array($parameters));
    }

    /**
     * @param deleteSMSMessages $parameters
     * @return deleteSMSMessagesResponse
     */
    public function deleteSMSMessages(deleteSMSMessages $parameters)
    {
      return $this->__soapCall('deleteSMSMessages', array($parameters));
    }

    /**
     * @param addSMSKeywords $parameters
     * @return addSMSKeywordsResponse
     */
    public function addSMSKeywords(addSMSKeywords $parameters)
    {
      return $this->__soapCall('addSMSKeywords', array($parameters));
    }

    /**
     * @param readWorkflows $parameters
     * @return readWorkflowsResponse
     */
    public function readWorkflows(readWorkflows $parameters)
    {
      return $this->__soapCall('readWorkflows', array($parameters));
    }

    /**
     * @param updateApiTokens $parameters
     * @return updateApiTokensResponse
     */
    public function updateApiTokens(updateApiTokens $parameters)
    {
      return $this->__soapCall('updateApiTokens', array($parameters));
    }

    /**
     * @param readAccounts $parameters
     * @return readAccountsResponse
     */
    public function readAccounts(readAccounts $parameters)
    {
      return $this->__soapCall('readAccounts', array($parameters));
    }

    /**
     * @param addToSMSKeyword $parameters
     * @return addToSMSKeywordResponse
     */
    public function addToSMSKeyword(addToSMSKeyword $parameters)
    {
      return $this->__soapCall('addToSMSKeyword', array($parameters));
    }

    /**
     * @param addLists $parameters
     * @return addListsResponse
     */
    public function addLists(addLists $parameters)
    {
      return $this->__soapCall('addLists', array($parameters));
    }

    /**
     * @param readDeliveryRecipients $parameters
     * @return readDeliveryRecipientsResponse
     */
    public function readDeliveryRecipients(readDeliveryRecipients $parameters)
    {
      return $this->__soapCall('readDeliveryRecipients', array($parameters));
    }

    /**
     * @param removeFromList $parameters
     * @return removeFromListResponse
     */
    public function removeFromList(removeFromList $parameters)
    {
      return $this->__soapCall('removeFromList', array($parameters));
    }

    /**
     * @param readRecentInboundActivities $parameters
     * @return readRecentInboundActivitiesResponse
     */
    public function readRecentInboundActivities(readRecentInboundActivities $parameters)
    {
      return $this->__soapCall('readRecentInboundActivities', array($parameters));
    }

    /**
     * @param readSMSKeywords $parameters
     * @return readSMSKeywordsResponse
     */
    public function readSMSKeywords(readSMSKeywords $parameters)
    {
      return $this->__soapCall('readSMSKeywords', array($parameters));
    }

    /**
     * @param readSegments $parameters
     * @return readSegmentsResponse
     */
    public function readSegments(readSegments $parameters)
    {
      return $this->__soapCall('readSegments', array($parameters));
    }

    /**
     * @param addDeliveries $parameters
     * @return addDeliveriesResponse
     */
    public function addDeliveries(addDeliveries $parameters)
    {
      return $this->__soapCall('addDeliveries', array($parameters));
    }

    /**
     * @param addContactEvent $parameters
     * @return addContactEventResponse
     */
    public function addContactEvent(addContactEvent $parameters)
    {
      return $this->__soapCall('addContactEvent', array($parameters));
    }

    /**
     * @param addContacts $parameters
     * @return addContactsResponse
     */
    public function addContacts(addContacts $parameters)
    {
      return $this->__soapCall('addContacts', array($parameters));
    }

    /**
     * @param deleteDeliveries $parameters
     * @return deleteDeliveriesResponse
     */
    public function deleteDeliveries(deleteDeliveries $parameters)
    {
      return $this->__soapCall('deleteDeliveries', array($parameters));
    }

    /**
     * @param login $parameters
     * @return loginResponse
     */
    public function login(login $parameters)
    {
      return $this->__soapCall('login', array($parameters));
    }

    /**
     * @param addOrUpdateDeliveryGroup $parameters
     * @return addOrUpdateDeliveryGroupResponse
     */
    public function addOrUpdateDeliveryGroup(addOrUpdateDeliveryGroup $parameters)
    {
      return $this->__soapCall('addOrUpdateDeliveryGroup', array($parameters));
    }

    /**
     * @param deleteOrders $parameters
     * @return deleteOrdersResponse
     */
    public function deleteOrders(deleteOrders $parameters)
    {
      return $this->__soapCall('deleteOrders', array($parameters));
    }

    /**
     * @param addOrUpdateOrders $parameters
     * @return addOrUpdateOrdersResponse
     */
    public function addOrUpdateOrders(addOrUpdateOrders $parameters)
    {
      return $this->__soapCall('addOrUpdateOrders', array($parameters));
    }

    /**
     * @param updateMessageFolders $parameters
     * @return updateMessageFoldersResponse
     */
    public function updateMessageFolders(updateMessageFolders $parameters)
    {
      return $this->__soapCall('updateMessageFolders', array($parameters));
    }

    /**
     * @param addOrUpdateContacts $parameters
     * @return addOrUpdateContactsResponse
     */
    public function addOrUpdateContacts(addOrUpdateContacts $parameters)
    {
      return $this->__soapCall('addOrUpdateContacts', array($parameters));
    }

    /**
     * @param readDeliveries $parameters
     * @return readDeliveriesResponse
     */
    public function readDeliveries(readDeliveries $parameters)
    {
      return $this->__soapCall('readDeliveries', array($parameters));
    }

    /**
     * @param readSMSDeliveries $parameters
     * @return readSMSDeliveriesResponse
     */
    public function readSMSDeliveries(readSMSDeliveries $parameters)
    {
      return $this->__soapCall('readSMSDeliveries', array($parameters));
    }

    /**
     * @param readContentTags $parameters
     * @return readContentTagsResponse
     */
    public function readContentTags(readContentTags $parameters)
    {
      return $this->__soapCall('readContentTags', array($parameters));
    }

    /**
     * @param updateLists $parameters
     * @return updateListsResponse
     */
    public function updateLists(updateLists $parameters)
    {
      return $this->__soapCall('updateLists', array($parameters));
    }

    /**
     * @param addAccounts $parameters
     * @return addAccountsResponse
     */
    public function addAccounts(addAccounts $parameters)
    {
      return $this->__soapCall('addAccounts', array($parameters));
    }

    /**
     * @param deleteContentTags $parameters
     * @return deleteContentTagsResponse
     */
    public function deleteContentTags(deleteContentTags $parameters)
    {
      return $this->__soapCall('deleteContentTags', array($parameters));
    }

    /**
     * @param deleteLists $parameters
     * @return deleteListsResponse
     */
    public function deleteLists(deleteLists $parameters)
    {
      return $this->__soapCall('deleteLists', array($parameters));
    }

    /**
     * @param addMessages $parameters
     * @return addMessagesResponse
     */
    public function addMessages(addMessages $parameters)
    {
      return $this->__soapCall('addMessages', array($parameters));
    }

    /**
     * @param removeFromSMSKeyword $parameters
     * @return removeFromSMSKeywordResponse
     */
    public function removeFromSMSKeyword(removeFromSMSKeyword $parameters)
    {
      return $this->__soapCall('removeFromSMSKeyword', array($parameters));
    }

    /**
     * @param addHeaderFooters $parameters
     * @return addHeaderFootersResponse
     */
    public function addHeaderFooters(addHeaderFooters $parameters)
    {
      return $this->__soapCall('addHeaderFooters', array($parameters));
    }

    /**
     * @param readFields $parameters
     * @return readFieldsResponse
     */
    public function readFields(readFields $parameters)
    {
      return $this->__soapCall('readFields', array($parameters));
    }

    /**
     * @param deleteFromDeliveryGroup $parameters
     * @return deleteFromDeliveryGroupResponse
     */
    public function deleteFromDeliveryGroup(deleteFromDeliveryGroup $parameters)
    {
      return $this->__soapCall('deleteFromDeliveryGroup', array($parameters));
    }

    /**
     * @param updateFields $parameters
     * @return updateFieldsResponse
     */
    public function updateFields(updateFields $parameters)
    {
      return $this->__soapCall('updateFields', array($parameters));
    }

    /**
     * @param addMessageRules $parameters
     * @return addMessageRulesResponse
     */
    public function addMessageRules(addMessageRules $parameters)
    {
      return $this->__soapCall('addMessageRules', array($parameters));
    }

    /**
     * @param clearLists $parameters
     * @return clearListsResponse
     */
    public function clearLists(clearLists $parameters)
    {
      return $this->__soapCall('clearLists', array($parameters));
    }

    /**
     * @param updateSMSMessages $parameters
     * @return updateSMSMessagesResponse
     */
    public function updateSMSMessages(updateSMSMessages $parameters)
    {
      return $this->__soapCall('updateSMSMessages', array($parameters));
    }

    /**
     * @param deleteSMSKeywords $parameters
     * @return deleteSMSKeywordsResponse
     */
    public function deleteSMSKeywords(deleteSMSKeywords $parameters)
    {
      return $this->__soapCall('deleteSMSKeywords', array($parameters));
    }

    /**
     * @param logout $parameters
     * @return logoutResponse
     */
    public function logout(logout $parameters)
    {
      return $this->__soapCall('logout', array($parameters));
    }

    /**
     * @param addMessageFolders $parameters
     * @return addMessageFoldersResponse
     */
    public function addMessageFolders(addMessageFolders $parameters)
    {
      return $this->__soapCall('addMessageFolders', array($parameters));
    }

    /**
     * @param deleteAccounts $parameters
     * @return deleteAccountsResponse
     */
    public function deleteAccounts(deleteAccounts $parameters)
    {
      return $this->__soapCall('deleteAccounts', array($parameters));
    }

    /**
     * @param readMessages $parameters
     * @return readMessagesResponse
     */
    public function readMessages(readMessages $parameters)
    {
      return $this->__soapCall('readMessages', array($parameters));
    }

    /**
     * @param addWorkflows $parameters
     * @return addWorkflowsResponse
     */
    public function addWorkflows(addWorkflows $parameters)
    {
      return $this->__soapCall('addWorkflows', array($parameters));
    }

    /**
     * @param readMessageRules $parameters
     * @return readMessageRulesResponse
     */
    public function readMessageRules(readMessageRules $parameters)
    {
      return $this->__soapCall('readMessageRules', array($parameters));
    }

    /**
     * @param addConversion $parameters
     * @return addConversionResponse
     */
    public function addConversion(addConversion $parameters)
    {
      return $this->__soapCall('addConversion', array($parameters));
    }

    /**
     * @param updateWorkflows $parameters
     * @return updateWorkflowsResponse
     */
    public function updateWorkflows(updateWorkflows $parameters)
    {
      return $this->__soapCall('updateWorkflows', array($parameters));
    }

    /**
     * @param updateAccounts $parameters
     * @return updateAccountsResponse
     */
    public function updateAccounts(updateAccounts $parameters)
    {
      return $this->__soapCall('updateAccounts', array($parameters));
    }

    /**
     * @param readBounces $parameters
     * @return readBouncesResponse
     */
    public function readBounces(readBounces $parameters)
    {
      return $this->__soapCall('readBounces', array($parameters));
    }

    /**
     * @param addLogins $parameters
     * @return addLoginsResponse
     */
    public function addLogins(addLogins $parameters)
    {
      return $this->__soapCall('addLogins', array($parameters));
    }

    /**
     * @param deleteMessageFolders $parameters
     * @return deleteMessageFoldersResponse
     */
    public function deleteMessageFolders(deleteMessageFolders $parameters)
    {
      return $this->__soapCall('deleteMessageFolders', array($parameters));
    }

    /**
     * @param updateHeaderFooters $parameters
     * @return updateHeaderFootersResponse
     */
    public function updateHeaderFooters(updateHeaderFooters $parameters)
    {
      return $this->__soapCall('updateHeaderFooters', array($parameters));
    }

    /**
     * @param updateContacts $parameters
     * @return updateContactsResponse
     */
    public function updateContacts(updateContacts $parameters)
    {
      return $this->__soapCall('updateContacts', array($parameters));
    }

    /**
     * @param readDeliveryGroups $parameters
     * @return readDeliveryGroupsResponse
     */
    public function readDeliveryGroups(readDeliveryGroups $parameters)
    {
      return $this->__soapCall('readDeliveryGroups', array($parameters));
    }

    /**
     * @param addToDeliveryGroup $parameters
     * @return addToDeliveryGroupResponse
     */
    public function addToDeliveryGroup(addToDeliveryGroup $parameters)
    {
      return $this->__soapCall('addToDeliveryGroup', array($parameters));
    }

    /**
     * @param addSMSDeliveries $parameters
     * @return addSMSDeliveriesResponse
     */
    public function addSMSDeliveries(addSMSDeliveries $parameters)
    {
      return $this->__soapCall('addSMSDeliveries', array($parameters));
    }

    /**
     * @param deleteSMSDeliveries $parameters
     * @return deleteSMSDeliveriesResponse
     */
    public function deleteSMSDeliveries(deleteSMSDeliveries $parameters)
    {
      return $this->__soapCall('deleteSMSDeliveries', array($parameters));
    }

    /**
     * @param deleteFields $parameters
     * @return deleteFieldsResponse
     */
    public function deleteFields(deleteFields $parameters)
    {
      return $this->__soapCall('deleteFields', array($parameters));
    }

    /**
     * @param addApiTokens $parameters
     * @return addApiTokensResponse
     */
    public function addApiTokens(addApiTokens $parameters)
    {
      return $this->__soapCall('addApiTokens', array($parameters));
    }

    /**
     * @param readSMSMessages $parameters
     * @return readSMSMessagesResponse
     */
    public function readSMSMessages(readSMSMessages $parameters)
    {
      return $this->__soapCall('readSMSMessages', array($parameters));
    }

    /**
     * @param updateLogins $parameters
     * @return updateLoginsResponse
     */
    public function updateLogins(updateLogins $parameters)
    {
      return $this->__soapCall('updateLogins', array($parameters));
    }

    /**
     * @param readWebforms $parameters
     * @return readWebformsResponse
     */
    public function readWebforms(readWebforms $parameters)
    {
      return $this->__soapCall('readWebforms', array($parameters));
    }

}
