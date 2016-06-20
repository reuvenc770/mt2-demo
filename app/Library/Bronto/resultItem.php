<?php
namespace App\Library\Bronto;
class resultItem
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var boolean $isNew
     */
    protected $isNew = null;

    /**
     * @var boolean $isError
     */
    protected $isError = null;

    /**
     * @var int $errorCode
     */
    protected $errorCode = null;

    /**
     * @var string $errorString
     */
    protected $errorString = null;

    /**
     * @param string $id
     * @param boolean $isNew
     * @param boolean $isError
     * @param int $errorCode
     * @param string $errorString
     */
    public function __construct($id, $isNew, $isError, $errorCode, $errorString)
    {
      $this->id = $id;
      $this->isNew = $isNew;
      $this->isError = $isError;
      $this->errorCode = $errorCode;
      $this->errorString = $errorString;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return resultItem
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsNew()
    {
      return $this->isNew;
    }

    /**
     * @param boolean $isNew
     * @return resultItem
     */
    public function setIsNew($isNew)
    {
      $this->isNew = $isNew;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsError()
    {
      return $this->isError;
    }

    /**
     * @param boolean $isError
     * @return resultItem
     */
    public function setIsError($isError)
    {
      $this->isError = $isError;
      return $this;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
      return $this->errorCode;
    }

    /**
     * @param int $errorCode
     * @return resultItem
     */
    public function setErrorCode($errorCode)
    {
      $this->errorCode = $errorCode;
      return $this;
    }

    /**
     * @return string
     */
    public function getErrorString()
    {
      return $this->errorString;
    }

    /**
     * @param string $errorString
     * @return resultItem
     */
    public function setErrorString($errorString)
    {
      $this->errorString = $errorString;
      return $this;
    }

}
