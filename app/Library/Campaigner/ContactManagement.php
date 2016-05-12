<?php
namespace App\Library\Campaigner;
class ContactManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'ImmediateUpload' => 'App\\Library\\Campaigner\\ImmediateUpload',
      'Authentication' => 'App\\Library\\Campaigner\\Authentication',
      'ArrayOfContactData' => 'App\\Library\\Campaigner\\ArrayOfContactData',
      'ContactData' => 'App\\Library\\Campaigner\\ContactData',
      'ContactKey' => 'App\\Library\\Campaigner\\ContactKey',
      'NullableElement' => 'App\\Library\\Campaigner\\NullableElement',
      'ArrayOfCustomAttribute' => 'App\\Library\\Campaigner\\ArrayOfCustomAttribute',
      'CustomAttribute' => 'App\\Library\\Campaigner\\CustomAttribute',
      'ArrayOfInt' => 'App\\Library\\Campaigner\\ArrayOfInt',
      'ImmediateUploadResponse' => 'App\\Library\\Campaigner\\ImmediateUploadResponse',
      'ArrayOfUploadResultData' => 'App\\Library\\Campaigner\\ArrayOfUploadResultData',
      'UploadResultData' => 'App\\Library\\Campaigner\\UploadResultData',
      'ResponseHeader' => 'App\\Library\\Campaigner\\ResponseHeader',
      'ResubscribeContact' => 'App\\Library\\Campaigner\\ResubscribeContact',
      'ResubscribeContactResponse' => 'App\\Library\\Campaigner\\ResubscribeContactResponse',
      'ResubscribeContactResult' => 'App\\Library\\Campaigner\\ResubscribeContactResult',
      'CreateUpdateAttribute' => 'App\\Library\\Campaigner\\CreateUpdateAttribute',
      'CreateUpdateAttributeResponse' => 'App\\Library\\Campaigner\\CreateUpdateAttributeResponse',
      'CreateUpdateAttributeResult' => 'App\\Library\\Campaigner\\CreateUpdateAttributeResult',
      'ListAttributes' => 'App\\Library\\Campaigner\\ListAttributes',
      'ListAttributesFilter' => 'App\\Library\\Campaigner\\ListAttributesFilter',
      'ListAttributesResponse' => 'App\\Library\\Campaigner\\ListAttributesResponse',
      'ArrayOfAttributeDescription' => 'App\\Library\\Campaigner\\ArrayOfAttributeDescription',
      'AttributeDescription' => 'App\\Library\\Campaigner\\AttributeDescription',
      'DeleteAttribute' => 'App\\Library\\Campaigner\\DeleteAttribute',
      'DeleteAttributeResponse' => 'App\\Library\\Campaigner\\DeleteAttributeResponse',
      'InitiateDoubleOptIn' => 'App\\Library\\Campaigner\\InitiateDoubleOptIn',
      'ContactFilter' => 'App\\Library\\Campaigner\\ContactFilter',
      'ContactKeysWrapper' => 'App\\Library\\Campaigner\\ContactKeysWrapper',
      'ArrayOfContactKey' => 'App\\Library\\Campaigner\\ArrayOfContactKey',
      'InitiateDoubleOptInResponse' => 'App\\Library\\Campaigner\\InitiateDoubleOptInResponse',
      'ArrayOfDoubleOptInError' => 'App\\Library\\Campaigner\\ArrayOfDoubleOptInError',
      'DoubleOptInError' => 'App\\Library\\Campaigner\\DoubleOptInError',
      'RunReport' => 'App\\Library\\Campaigner\\RunReport',
      'RunReportResponse' => 'App\\Library\\Campaigner\\RunReportResponse',
      'ReportTicket' => 'App\\Library\\Campaigner\\ReportTicket',
      'DownloadReport' => 'App\\Library\\Campaigner\\DownloadReport',
      'DownloadReportResponse' => 'App\\Library\\Campaigner\\DownloadReportResponse',
      'ArrayOfReportResult' => 'App\\Library\\Campaigner\\ArrayOfReportResult',
      'ReportResult' => 'App\\Library\\Campaigner\\ReportResult',
      'GetUploadMassContactsStatus' => 'App\\Library\\Campaigner\\GetUploadMassContactsStatus',
      'GetUploadMassContactsStatusResponse' => 'App\\Library\\Campaigner\\GetUploadMassContactsStatusResponse',
      'UploadMassContactsStatus' => 'App\\Library\\Campaigner\\UploadMassContactsStatus',
      'UploadStatusData' => 'App\\Library\\Campaigner\\UploadStatusData',
      'GetUploadMassContactsResult' => 'App\\Library\\Campaigner\\GetUploadMassContactsResult',
      'GetUploadMassContactsResultResponse' => 'App\\Library\\Campaigner\\GetUploadMassContactsResultResponse',
      'ArrayOfContactResultData' => 'App\\Library\\Campaigner\\ArrayOfContactResultData',
      'ContactResultData' => 'App\\Library\\Campaigner\\ContactResultData',
      'DeleteContacts' => 'App\\Library\\Campaigner\\DeleteContacts',
      'DeleteContactsResponse' => 'App\\Library\\Campaigner\\DeleteContactsResponse',
      'UploadMassContacts' => 'App\\Library\\Campaigner\\UploadMassContacts',
      'UploadMassContactsResponse' => 'App\\Library\\Campaigner\\UploadMassContactsResponse',
      'UploadMassContactsResult' => 'App\\Library\\Campaigner\\UploadMassContactsResult',
      'ListTestContacts' => 'App\\Library\\Campaigner\\ListTestContacts',
      'ListTestContactsResponse' => 'App\\Library\\Campaigner\\ListTestContactsResponse',
      'ArrayOfTestContact' => 'App\\Library\\Campaigner\\ArrayOfTestContact',
      'TestContact' => 'App\\Library\\Campaigner\\TestContact',
      'GetContacts' => 'App\\Library\\Campaigner\\GetContacts',
      'ContactsDataFilter' => 'App\\Library\\Campaigner\\ContactsDataFilter',
      'contactInformationFilter' => 'App\\Library\\Campaigner\\contactInformationFilter',
      'GetContactsResponse' => 'App\\Library\\Campaigner\\GetContactsResponse',
      'ContactsData' => 'App\\Library\\Campaigner\\ContactsData',
      'ArrayOfContactDetailData' => 'App\\Library\\Campaigner\\ArrayOfContactDetailData',
      'ContactDetailData' => 'App\\Library\\Campaigner\\ContactDetailData',
      'StaticAttributes' => 'App\\Library\\Campaigner\\StaticAttributes',
      'SystemAttributes' => 'App\\Library\\Campaigner\\SystemAttributes',
      'ArrayOfAttributeDetails' => 'App\\Library\\Campaigner\\ArrayOfAttributeDetails',
      'AttributeDetails' => 'App\\Library\\Campaigner\\AttributeDetails',
      'ArrayOfContactGroupDescription' => 'App\\Library\\Campaigner\\ArrayOfContactGroupDescription',
      'ContactGroupDescription' => 'App\\Library\\Campaigner\\ContactGroupDescription',
      'ArrayOfInvalidContactDetailData' => 'App\\Library\\Campaigner\\ArrayOfInvalidContactDetailData',
      'InvalidContactDetailData' => 'App\\Library\\Campaigner\\InvalidContactDetailData',
      'ListContactFields' => 'App\\Library\\Campaigner\\ListContactFields',
      'filter' => 'App\\Library\\Campaigner\\filter',
      'ListContactFieldsResponse' => 'App\\Library\\Campaigner\\ListContactFieldsResponse',
      'ArrayOfAttributeData' => 'App\\Library\\Campaigner\\ArrayOfAttributeData',
      'AttributeData' => 'App\\Library\\Campaigner\\AttributeData',
      'FormField' => 'App\\Library\\Campaigner\\FormField',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     * @throws \Exception
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/contactmanagement.asmx?WSDL')
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
            'connection_timeout' => 600,
            'default_socket_timeout' => 600
        ), $options);
        try {
            parent::__construct($wsdl, $options);
        } catch (\Exception $e){
            throw $e;
        }
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
