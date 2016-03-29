<?php
namespace App\Library\Campaigner;
class UploadResultData
{

    /**
     * @var int $Index
     */
    protected $Index = null;

    /**
     * @var ContactKey $ContactKey
     */
    protected $ContactKey = null;

    /**
     * @var string $ResultCode
     */
    protected $ResultCode = null;

    /**
     * @var string $ResultDescription
     */
    protected $ResultDescription = null;

    /**
     * @param int $Index
     * @param ContactKey $ContactKey
     * @param string $ResultCode
     * @param string $ResultDescription
     */
    public function __construct($Index, $ContactKey, $ResultCode, $ResultDescription)
    {
      $this->Index = $Index;
      $this->ContactKey = $ContactKey;
      $this->ResultCode = $ResultCode;
      $this->ResultDescription = $ResultDescription;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
      return $this->Index;
    }

    /**
     * @param int $Index
     * @return UploadResultData
     */
    public function setIndex($Index)
    {
      $this->Index = $Index;
      return $this;
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
     * @return UploadResultData
     */
    public function setContactKey($ContactKey)
    {
      $this->ContactKey = $ContactKey;
      return $this;
    }

    /**
     * @return string
     */
    public function getResultCode()
    {
      return $this->ResultCode;
    }

    /**
     * @param string $ResultCode
     * @return UploadResultData
     */
    public function setResultCode($ResultCode)
    {
      $this->ResultCode = $ResultCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getResultDescription()
    {
      return $this->ResultDescription;
    }

    /**
     * @param string $ResultDescription
     * @return UploadResultData
     */
    public function setResultDescription($ResultDescription)
    {
      $this->ResultDescription = $ResultDescription;
      return $this;
    }

}
