<?php
namespace App\Library\Campaigner;
class ResponseHeader
{

    /**
     * @var boolean $ErrorFlag
     */
    protected $ErrorFlag = null;

    /**
     * @var string $ReturnCode
     */
    protected $ReturnCode = null;

    /**
     * @var string $ReturnMessage
     */
    protected $ReturnMessage = null;

    /**
     * @param boolean $ErrorFlag
     * @param string $ReturnCode
     * @param string $ReturnMessage
     */
    public function __construct($ErrorFlag, $ReturnCode, $ReturnMessage)
    {
      $this->ErrorFlag = $ErrorFlag;
      $this->ReturnCode = $ReturnCode;
      $this->ReturnMessage = $ReturnMessage;
    }

    /**
     * @return boolean
     */
    public function getErrorFlag()
    {
      return $this->ErrorFlag;
    }

    /**
     * @param boolean $ErrorFlag
     * @return ResponseHeader
     */
    public function setErrorFlag($ErrorFlag)
    {
      $this->ErrorFlag = $ErrorFlag;
      return $this;
    }

    /**
     * @return string
     */
    public function getReturnCode()
    {
      return $this->ReturnCode;
    }

    /**
     * @param string $ReturnCode
     * @return ResponseHeader
     */
    public function setReturnCode($ReturnCode)
    {
      $this->ReturnCode = $ReturnCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getReturnMessage()
    {
      return $this->ReturnMessage;
    }

    /**
     * @param string $ReturnMessage
     * @return ResponseHeader
     */
    public function setReturnMessage($ReturnMessage)
    {
      $this->ReturnMessage = $ReturnMessage;
      return $this;
    }

}
