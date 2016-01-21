<?php
namespace App\Library\Campaigner;
class ContactResultData
{

    /**
     * @var int $RowIndex
     */
    protected $RowIndex = null;

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
     * @param int $RowIndex
     * @param ContactKey $ContactKey
     * @param string $ResultCode
     * @param string $ResultDescription
     */
    public function __construct($RowIndex, $ContactKey, $ResultCode, $ResultDescription)
    {
      $this->RowIndex = $RowIndex;
      $this->ContactKey = $ContactKey;
      $this->ResultCode = $ResultCode;
      $this->ResultDescription = $ResultDescription;
    }

    /**
     * @return int
     */
    public function getRowIndex()
    {
      return $this->RowIndex;
    }

    /**
     * @param int $RowIndex
     * @return ContactResultData
     */
    public function setRowIndex($RowIndex)
    {
      $this->RowIndex = $RowIndex;
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
     * @return ContactResultData
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
     * @return ContactResultData
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
     * @return ContactResultData
     */
    public function setResultDescription($ResultDescription)
    {
      $this->ResultDescription = $ResultDescription;
      return $this;
    }

}
