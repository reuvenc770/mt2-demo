<?php
namespace App\Library\Campaigner;
class InvalidContactDetailData
{

    /**
     * @var ContactKey $ContactKey
     */
    protected $ContactKey = null;

    /**
     * @param ContactKey $ContactKey
     */
    public function __construct($ContactKey)
    {
      $this->ContactKey = $ContactKey;
    }

    /**
     * @return ContactKey
     */
    public function getContactKey()
    {
      return $this->ContactKey;
    }

    /**
     * @param ContactKey $ContactKey
     * @return InvalidContactDetailData
     */
    public function setContactKey($ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
    }

}
