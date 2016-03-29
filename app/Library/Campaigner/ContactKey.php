<?php
namespace App\Library\Campaigner;
class ContactKey
{

    /**
     * @var int $ContactId
     */
    protected $ContactId = null;

    /**
     * @var string $ContactUniqueIdentifier
     */
    protected $ContactUniqueIdentifier = null;

    /**
     * @param int $ContactId
     * @param string $ContactUniqueIdentifier
     */
    public function __construct($ContactId, $ContactUniqueIdentifier)
    {
      $this->ContactId = $ContactId;
      $this->ContactUniqueIdentifier = $ContactUniqueIdentifier;
    }

    /**
     * @return int
     */
    public function getContactId()
    {
      return $this->ContactId;
    }

    /**
     * @param int $ContactId
     * @return ContactKey
     */
    public function setContactId($ContactId)
    {
      $this->ContactId = $ContactId;
      return $this;
    }

    /**
     * @return string
     */
    public function getContactUniqueIdentifier()
    {
      return $this->ContactUniqueIdentifier;
    }

    /**
     * @param string $ContactUniqueIdentifier
     * @return ContactKey
     */
    public function setContactUniqueIdentifier($ContactUniqueIdentifier)
    {
      $this->ContactUniqueIdentifier = $ContactUniqueIdentifier;
      return $this;
    }

}
