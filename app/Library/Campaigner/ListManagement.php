<?php
namespace App\Library\Campaigner;
class ListManagement extends \SoapClient
{

    /**
     * @var array $classmap The defined classes
     */
    private static $classmap = array (
      'ListContactGroups' => '\\ListContactGroups',
      'Authentication' => '\\Authentication',
      'ListContactGroupsResponse' => '\\ListContactGroupsResponse',
      'ArrayOfContactGroupDescription' => '\\ArrayOfContactGroupDescription',
      'ContactGroupDescription' => '\\ContactGroupDescription',
      'ResponseHeader' => '\\ResponseHeader',
      'CreateUpdateContactGroups' => '\\CreateUpdateContactGroups',
      'CreateUpdateContactGroupsResponse' => '\\CreateUpdateContactGroupsResponse',
      'CreateUpdateContactGroupsResult' => '\\CreateUpdateContactGroupsResult',
      'DeleteContactGroups' => '\\DeleteContactGroups',
      'ArrayOfInt' => '\\ArrayOfInt',
      'DeleteContactGroupsResponse' => '\\DeleteContactGroupsResponse',
    );

    /**
     * @param array $options A array of config values
     * @param string $wsdl The wsdl file to use
     */
    public function __construct(array $options = array(), $wsdl = 'https://ws.campaigner.com/2013/01/listmanagement.asmx?WSDL')
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
     * Returns a list of all mailing and segment descriptions
     *
     * @param ListContactGroups $parameters
     * @return ListContactGroupsResponse
     */
    public function ListContactGroups(ListContactGroups $parameters)
    {
      return $this->__soapCall('ListContactGroups', array($parameters));
    }

    /**
     * Creates or updates mailing lists and segments
     *
     * @param CreateUpdateContactGroups $parameters
     * @return CreateUpdateContactGroupsResponse
     */
    public function CreateUpdateContactGroups(CreateUpdateContactGroups $parameters)
    {
      return $this->__soapCall('CreateUpdateContactGroups', array($parameters));
    }

    /**
     * Deletes contact group by id
     *
     * @param DeleteContactGroups $parameters
     * @return DeleteContactGroupsResponse
     */
    public function DeleteContactGroups(DeleteContactGroups $parameters)
    {
      return $this->__soapCall('DeleteContactGroups', array($parameters));
    }

}
