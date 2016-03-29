<?php
namespace App\Library\Campaigner;
class DoubleOptInError
{

    /**
     * @var ContactKey $ContactKey
     */
    protected $ContactKey = null;

    /**
     * @var DoubleOptInErrorEnum $ResultCode
     */
    protected $ResultCode = null;

    /**
     * @param ContactKey $ContactKey
     * @param DoubleOptInErrorEnum $ResultCode
     */
    public function __construct($ContactKey, $ResultCode)
    {
      $this->ContactKey = $ContactKey;
      $this->ResultCode = $ResultCode;
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
     * @return DoubleOptInError
     */
    public function setContactKey($ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
    }

    /**
     * @return DoubleOptInErrorEnum
     */
    public function getResultCode()
    {
      return $this->ResultCode;
    }

    /**
     * @param DoubleOptInErrorEnum $ResultCode
     * @return DoubleOptInError
     */
    public function setResultCode($ResultCode)
    {
      $this->ResultCode = $ResultCode;
      return $this;
    }

}
