<?php
namespace App\Library\Bronto;
class ApiException
{

    /**
     * @var int $errorCode
     */
    protected $errorCode = null;

    
    public function __construct()
    {
    
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
     * @return ApiException
     */
    public function setErrorCode($errorCode)
    {
      $this->errorCode = $errorCode;
      return $this;
    }

}
