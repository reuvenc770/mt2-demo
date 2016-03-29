<?php
namespace App\Library\Campaigner;
class ResubscribeContactResponse
{

    /**
     * @var ResubscribeContactResult $ResubscribeContactResult
     */
    protected $ResubscribeContactResult = null;

    /**
     * @param ResubscribeContactResult $ResubscribeContactResult
     */
    public function __construct($ResubscribeContactResult)
    {
      $this->ResubscribeContactResult = $ResubscribeContactResult;
    }

    /**
     * @return ResubscribeContactResult
     */
    public function getResubscribeContactResult()
    {
      return $this->ResubscribeContactResult;
    }

    /**
     * @param ResubscribeContactResult $ResubscribeContactResult
     * @return ResubscribeContactResponse
     */
    public function setResubscribeContactResult($ResubscribeContactResult)
    {
      $this->ResubscribeContactResult = $ResubscribeContactResult;
      return $this;
    }

}
