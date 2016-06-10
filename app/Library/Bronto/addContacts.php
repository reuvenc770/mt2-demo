<?php
namespace App\Library\Bronto;
class addContacts
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
     * @return addContacts
     */
    public function setContacts(array $contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

}
