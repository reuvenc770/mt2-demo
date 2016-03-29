<?php
namespace App\Library\Campaigner;
class ContactFilter extends ContactKeysWrapper
{

    /**
     * @var string $xmlContactQuery
     */
    protected $xmlContactQuery = null;

    /**
     * @param ArrayOfContactKey $ContactKeys
     * @param string $xmlContactQuery
     */
    public function __construct($ContactKeys, $xmlContactQuery)
    {
      parent::__construct($ContactKeys);
      $this->xmlContactQuery = $xmlContactQuery;
    }

    /**
     * @return string
     */
    public function getXmlContactQuery()
    {
      return $this->xmlContactQuery;
    }

    /**
     * @param string $xmlContactQuery
     * @return ContactFilter
     */
    public function setXmlContactQuery($xmlContactQuery)
    {
      $this->xmlContactQuery = $xmlContactQuery;
      return $this;
    }

}
