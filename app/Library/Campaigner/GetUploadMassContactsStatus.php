<?php
namespace App\Library\Campaigner;
class GetUploadMassContactsStatus
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var string $uploadTicketId
     */
    protected $uploadTicketId = null;

    /**
     * @param Authentication $authentication
     * @param string $uploadTicketId
     */
    public function __construct($authentication, $uploadTicketId)
    {
      $this->authentication = $authentication;
      $this->uploadTicketId = $uploadTicketId;
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
     * @return GetUploadMassContactsStatus
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return string
     */
    public function getUploadTicketId()
    {
      return $this->uploadTicketId;
    }

    /**
     * @param string $uploadTicketId
     * @return GetUploadMassContactsStatus
     */
    public function setUploadTicketId($uploadTicketId)
    {
      $this->uploadTicketId = $uploadTicketId;
      return $this;
    }

}
