<?php
namespace App\Library\Campaigner;
class ResubscribeContact
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var ContactKey $contactKey
     */
    protected $contactKey = null;

    /**
     * @var ContactStatus $status
     */
    protected $status = null;

    /**
     * @param Authentication $authentication
     * @param ContactKey $contactKey
     * @param ContactStatus $status
     */
    public function __construct($authentication, $contactKey, $status)
    {
      $this->authentication = $authentication;
      $this->contactKey = $contactKey;
      $this->status = $status;
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
     * @return ResubscribeContact
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return ContactKey
     */
    public function getContactKey()
    {
      return $this->contactKey;
    }

    /**
     * @param ContactKey $contactKey
     * @return ResubscribeContact
     */
    public function setContactKey($contactKey)
    {
      $this->contactKey = $contactKey;
      return $this;
    }

    /**
     * @return ContactStatus
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param ContactStatus $status
     * @return ResubscribeContact
     */
    public function setStatus($status)
    {
      $this->status = $status;
      return $this;
    }

}
