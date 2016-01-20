<?php
namespace App\Library\Campaigner;
class ContactManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'ImmediateUpload' => '\\ImmediateUpload',
      'Authentication' => '\\Authentication',
      'ArrayOfContactData' => '\\ArrayOfContactData',
      'ContactData' => '\\ContactData',
      'ContactKey' => '\\ContactKey',
      'NullableElement' => '\\NullableElement',
      'ArrayOfCustomAttribute' => '\\ArrayOfCustomAttribute',
      'CustomAttribute' => '\\CustomAttribute',
      'ArrayOfInt' => '\\ArrayOfInt',
      'ImmediateUploadResponse' => '\\ImmediateUploadResponse',
      'ArrayOfUploadResultData' => '\\ArrayOfUploadResultData',
      'UploadResultData' => '\\UploadResultData',
      'ResponseHeader' => '\\ResponseHeader',
      'ResubscribeContact' => '\\ResubscribeContact',
      'ResubscribeContactResponse' => '\\ResubscribeContactResponse',
      'ResubscribeContactResult' => '\\ResubscribeContactResult',
      'CreateUpdateAttribute' => '\\CreateUpdateAttribute',
      'CreateUpdateAttributeResponse' => '\\CreateUpdateAttributeResponse',
      'CreateUpdateAttributeResult' => '\\CreateUpdateAttributeResult',
      'ListAttributes' => '\\ListAttributes',
      'ListAttributesFilter' => '\\ListAttributesFilter',
      'ListAttributesResponse' => '\\ListAttributesResponse',
      'ArrayOfAttributeDescription' => '\\ArrayOfAttributeDescription',
      'AttributeDescription' => '\\AttributeDescription',
      'DeleteAttribute' => '\\DeleteAttribute',
      'DeleteAttributeResponse' => '\\DeleteAttributeResponse',
      'InitiateDoubleOptIn' => '\\InitiateDoubleOptIn',
      'ContactFilter' => '\\ContactFilter',
      'ContactKeysWrapper' => '\\ContactKeysWrapper',
      'ArrayOfContactKey' => '\\ArrayOfContactKey',
      'InitiateDoubleOptInResponse' => '\\InitiateDoubleOptInResponse',
      'ArrayOfDoubleOptInError' => '\\ArrayOfDoubleOptInError',
      'DoubleOptInError' => '\\DoubleOptInError',
      'RunReport' => '\\RunReport',
      'RunReportResponse' => '\\RunReportResponse',
      'ReportTicket' => '\\ReportTicket',
      'DownloadReport' => '\\DownloadReport',
      'DownloadReportResponse' => '\\DownloadReportResponse',
      'ArrayOfReportResult' => '\\ArrayOfReportResult',
      'ReportResult' => '\\ReportResult',
      'GetUploadMassContactsStatus' => '\\GetUploadMassContactsStatus',
      'GetUploadMassContactsStatusResponse' => '\\GetUploadMassContactsStatusResponse',
      'UploadMassContactsStatus' => '\\UploadMassContactsStatus',
      'UploadStatusData' => '\\UploadStatusData',
      'GetUploadMassContactsResult' => '\\GetUploadMassContactsResult',
      'GetUploadMassContactsResultResponse' => '\\GetUploadMassContactsResultResponse',
      'ArrayOfContactResultData' => '\\ArrayOfContactResultData',
      'ContactResultData' => '\\ContactResultData',
      'DeleteContacts' => '\\DeleteContacts',
      'DeleteContactsResponse' => '\\DeleteContactsResponse',
      'UploadMassContacts' => '\\UploadMassContacts',
      'UploadMassContactsResponse' => '\\UploadMassContactsResponse',
      'UploadMassContactsResult' => '\\UploadMassContactsResult',
      'ListTestContacts' => '\\ListTestContacts',
      'ListTestContactsResponse' => '\\ListTestContactsResponse',
      'ArrayOfTestContact' => '\\ArrayOfTestContact',
      'TestContact' => '\\TestContact',
      'GetContacts' => '\\GetContacts',
      'ContactsDataFilter' => '\\ContactsDataFilter',
      'contactInformationFilter' => '\\contactInformationFilter',
      'GetContactsResponse' => '\\GetContactsResponse',
      'ContactsData' => '\\ContactsData',
      'ArrayOfContactDetailData' => '\\ArrayOfContactDetailData',
      'ContactDetailData' => '\\ContactDetailData',
      'StaticAttributes' => '\\StaticAttributes',
      'SystemAttributes' => '\\SystemAttributes',
      'ArrayOfAttributeDetails' => '\\ArrayOfAttributeDetails',
      'AttributeDetails' => '\\AttributeDetails',
      'ArrayOfContactGroupDescription' => '\\ArrayOfContactGroupDescription',
      'ContactGroupDescription' => '\\ContactGroupDescription',
      'ArrayOfInvalidContactDetailData' => '\\ArrayOfInvalidContactDetailData',
      'InvalidContactDetailData' => '\\InvalidContactDetailData',
      'ListContactFields' => '\\ListContactFields',
      'filter' => '\\filter',
      'ListContactFieldsResponse' => '\\ListContactFieldsResponse',
      'ArrayOfAttributeData' => '\\ArrayOfAttributeData',
      'AttributeData' => '\\AttributeData',
      'FormField' => '\\FormField',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/contactmanagement.asmx?WSDL')
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
     * Upload a list of contacts immediately.
     *
     * @param ImmediateUpload $parameters
     * @return ImmediateUploadResponse
     */
    public function ImmediateUpload(ImmediateUpload $parameters)
    {
      return $this->__soapCall('ImmediateUpload', array($parameters));
    }

    /**
     * Change an active contacts status from Unsubscribe to another status.
     *
     * @param ResubscribeContact $parameters
     * @return ResubscribeContactResponse
     */
    public function ResubscribeContact(ResubscribeContact $parameters)
    {
      return $this->__soapCall('ResubscribeContact', array($parameters));
    }

    /**
     * Create or Update one attribute at a time.
     *
     * @param CreateUpdateAttribute $parameters
     * @return CreateUpdateAttributeResponse
     */
    public function CreateUpdateAttribute(CreateUpdateAttribute $parameters)
    {
      return $this->__soapCall('CreateUpdateAttribute', array($parameters));
    }

    /**
     * List all contact attributes.
     *
     * @param ListAttributes $parameters
     * @return ListAttributesResponse
     */
    public function ListAttributes(ListAttributes $parameters)
    {
      return $this->__soapCall('ListAttributes', array($parameters));
    }

    /**
     * Delete a contact attribute
     *
     * @param DeleteAttribute $parameters
     * @return DeleteAttributeResponse
     */
    public function DeleteAttribute(DeleteAttribute $parameters)
    {
      return $this->__soapCall('DeleteAttribute', array($parameters));
    }

    /**
     * Initiates the double opt-in process.
     *
     * @param InitiateDoubleOptIn $parameters
     * @return InitiateDoubleOptInResponse
     */
    public function InitiateDoubleOptIn(InitiateDoubleOptIn $parameters)
    {
      return $this->__soapCall('InitiateDoubleOptIn', array($parameters));
    }

    /**
     * Run the query given by the reportCriteria (XML) to be used in conjunction with DownloadReport
     *
     * @param RunReport $parameters
     * @return RunReportResponse
     */
    public function RunReport(RunReport $parameters)
    {
      return $this->__soapCall('RunReport', array($parameters));
    }

    /**
     * Return a report based on the data generated from RunReport
     *
     * @param DownloadReport $parameters
     * @return DownloadReportResponse
     */
    public function DownloadReport(DownloadReport $parameters)
    {
      return $this->__soapCall('DownloadReport', array($parameters));
    }

    /**
     * Returns the status of the UploadMassContacts call for a given TicketId.
     *
     * @param GetUploadMassContactsStatus $parameters
     * @return GetUploadMassContactsStatusResponse
     */
    public function GetUploadMassContactsStatus(GetUploadMassContactsStatus $parameters)
    {
      return $this->__soapCall('GetUploadMassContactsStatus', array($parameters));
    }

    /**
     * Returns the result of the UploadMassContacts call for a given TicketId.
     *
     * @param GetUploadMassContactsResult $parameters
     * @return GetUploadMassContactsResultResponse
     */
    public function GetUploadMassContactsResult(GetUploadMassContactsResult $parameters)
    {
      return $this->__soapCall('GetUploadMassContactsResult', array($parameters));
    }

    /**
     * Delete the contacts with the IDs given in the array
     *
     * @param DeleteContacts $parameters
     * @return DeleteContactsResponse
     */
    public function DeleteContacts(DeleteContacts $parameters)
    {
      return $this->__soapCall('DeleteContacts', array($parameters));
    }

    /**
     * @param UploadMassContacts $parameters
     * @return UploadMassContactsResponse
     */
    public function UploadMassContacts(UploadMassContacts $parameters)
    {
      return $this->__soapCall('UploadMassContacts', array($parameters));
    }

    /**
     * List Test Contacts
     *
     * @param ListTestContacts $parameters
     * @return ListTestContactsResponse
     */
    public function ListTestContacts(ListTestContacts $parameters)
    {
      return $this->__soapCall('ListTestContacts', array($parameters));
    }

    /**
     * Get a list of contacts
     *
     * @param GetContacts $parameters
     * @return GetContactsResponse
     */
    public function GetContacts(GetContacts $parameters)
    {
      return $this->__soapCall('GetContacts', array($parameters));
    }

    /**
     * List Contact Fields.
     *
     * @param ListContactFields $parameters
     * @return ListContactFieldsResponse
     */
    public function ListContactFields(ListContactFields $parameters)
    {
      return $this->__soapCall('ListContactFields', array($parameters));
    }

}
