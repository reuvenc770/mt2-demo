<?php
namespace App\Library\Campaigner;
class ListTestContacts
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $contactCount
     */
    protected $contactCount = null;

    /**
     * @param Authentication $authentication
     * @param int $contactCount
     */
    public function __construct($authentication, $contactCount)
    {
      $this->authentication = $authentication;
      $this->contactCount = $contactCount;
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
     * @return ListTestContacts
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getContactCount()
    {
      return $this->contactCount;
    }

    /**
     * @param int $contactCount
     * @return ListTestContacts
     */
    public function setContactCount($contactCount)
    {
      $this->contactCount = $contactCount;
      return $this;
    }

}
