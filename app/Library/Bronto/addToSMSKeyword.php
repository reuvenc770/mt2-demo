<?php
namespace App\Library\Bronto;
class addToSMSKeyword
{

    /**
     * @var smsKeywordObject $keyword
     */
    protected $keyword = null;

    /**
     * @var contactObject[] $contacts
     */
    protected $contacts = null;

    /**
     * @param smsKeywordObject $keyword
     * @param contactObject[] $contacts
     */
    public function __construct($keyword, array $contacts)
    {
      $this->keyword = $keyword;
      $this->contacts = $contacts;
    }

    /**
     * @return smsKeywordObject
     */
    public function getKeyword()
    {
      return $this->keyword;
    }

    /**
     * @param smsKeywordObject $keyword
     * @return addToSMSKeyword
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
     * @return addToSMSKeyword
     */
    public function setContacts(array $contacts)
    {
      $this->contacts = $contacts;
      return $this;
    }

}
