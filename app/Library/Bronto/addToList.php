<?php
namespace App\Library\Bronto;
class addToList
{

    /**
     * @var mailListObject $list
     */
    protected $list = null;

    /**
     * @var contactObject[] $contacts
     */
    protected $contacts = null;

    /**
     * @param mailListObject $list
     * @param contactObject[] $contacts
     */
    public function __construct($list, array $contacts)
    {
      $this->list = $list;
      $this->contacts = $contacts;
    }

    /**
     * @return mailListObject
     */
    public function getList()
    {
      return $this->list;
    }

    /**
     * @param mailListObject $list
     * @return addToList
     */
    public function setList($list)
    {
      $this->list = $list;
      return $this;
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
     * @return addToList
     */
    public function setContacts(array $contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

}
