<?php
namespace App\Library\Campaigner;
class ResubscribeContactResult
{

    /**
     * @var ResubscribeResult $ResubscribeResultCode
     */
    protected $ResubscribeResultCode = null;

    /**
     * @param ResubscribeResult $ResubscribeResultCode
     */
    public function __construct($ResubscribeResultCode)
    {
      $this->ResubscribeResultCode = $ResubscribeResultCode;
    }

    /**
     * @return ResubscribeResult
     */
    public function getResubscribeResultCode()
    {
      return $this->ResubscribeResultCode;
    }

    /**
     * @param ResubscribeResult $ResubscribeResultCode
     * @return ResubscribeContactResult
     */
    public function setResubscribeResultCode($ResubscribeResultCode)
    {
      $this->ResubscribeResultCode = $ResubscribeResultCode;
      return $this;
    }

}
