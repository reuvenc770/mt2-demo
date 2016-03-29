<?php
namespace App\Library\Campaigner;
class GetContacts
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ContactsDataFilter $contactFilter
     */
    protected $contactFilter = null;

    /**
     * @var contactInformationFilter $contactInformationFilter
     */
    protected $contactInformationFilter = null;

    /**
     * @param Authentication $authentication
     * @param ContactsDataFilter $contactFilter
     * @param contactInformationFilter $contactInformationFilter
     */
    public function __construct($authentication, $contactFilter, $contactInformationFilter)
    {
      $this->authentication = $authentication;
      $this->contactFilter = $contactFilter;
      $this->contactInformationFilter = $contactInformationFilter;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return GetContacts
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ContactsDataFilter
     */
    public function getContactFilter()
    {
      return $this->contactFilter;
    }

    /**
     * @param ContactsDataFilter $contactFilter
     * @return GetContacts
     */
    public function setContactFilter($contactFilter)
    {
      $this->contactFilter = $contactFilter;
      return $this;
    }

    /**
     * @return contactInformationFilter
     */
    public function getContactInformationFilter()
    {
      return $this->contactInformationFilter;
    }

    /**
     * @param contactInformationFilter $contactInformationFilter
     * @return GetContacts
     */
    public function setContactInformationFilter($contactInformationFilter)
    {
      $this->contactInformationFilter = $contactInformationFilter;
      return $this;
    }

}
