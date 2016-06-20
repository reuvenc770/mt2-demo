<?php
namespace App\Library\Bronto;
class addOrUpdateContacts
{

    /**
     * @var contactObject[] $contacts
     */
    protected $contacts = null;

    /**
     * @param contactObject[] $contacts
     */
    public function __construct(array $contacts)
    {
      $this->contacts = $contacts;
    }

    /**
     * @return contactObject[]
     */
    public function getContacts()
    {
      return $this->contacts;
    }

    /**
     * @param contactObject[] $contacts
     * @return addOrUpdateContacts
     */
    public function setContacts(array $contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

}
