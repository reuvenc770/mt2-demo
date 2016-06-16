<?php
namespace App\Library\Bronto;
class smsDeliveryContactsObject
{

    /**
     * @var string $keyword
     */
    protected $keyword = null;

    /**
     * @var string[] $contactIds
     */
    protected $contactIds = null;

    /**
     * @param string $keyword
     */
    public function __construct($keyword)
    {
      $this->keyword = $keyword;
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
     * @return smsDeliveryContactsObject
     */
    public function setKeyword($keyword)
    {
      $this->keyword = $keyword;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getContactIds()
    {
      return $this->contactIds;
    }

    /**
     * @param string[] $contactIds
     * @return smsDeliveryContactsObject
     */
    public function setContactIds(array $contactIds)
    {
      $this->contactIds = $contactIds;
      return $this;
    }

}
