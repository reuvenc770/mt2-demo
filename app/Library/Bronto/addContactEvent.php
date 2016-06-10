<?php
namespace App\Library\Bronto;
class addContactEvent
{

    /**
     * @var string $keyword
     */
    protected $keyword = null;

    /**
     * @var contactObject[] $contacts
     */
    protected $contacts = null;

    /**
     * @param string $keyword
     * @param contactObject[] $contacts
     */
    public function __construct($keyword, array $contacts)
    {
      $this->keyword = $keyword;
      $this->contacts = $contacts;
    }

    /**
     * @return string
     */
    public function getKeyword()
    {
      return $this->keyword;
    }

    /**
     * @param string $keyword
     * @return addContactEvent
     */
    public function setKeyword($keyword)
    {
      $this->keyword = $keyword;
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
     * @return addContactEvent
     */
    public function setContacts(array $contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

}
