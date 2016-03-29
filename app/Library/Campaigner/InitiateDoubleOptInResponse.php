<?php
namespace App\Library\Campaigner;
class InitiateDoubleOptInResponse
{

    /**
     * @var ArrayOfDoubleOptInError $InitiateDoubleOptInResult
     */
    protected $InitiateDoubleOptInResult = null;

    /**
     * @param ArrayOfDoubleOptInError $InitiateDoubleOptInResult
     */
    public function __construct($InitiateDoubleOptInResult)
    {
      $this->InitiateDoubleOptInResult = $InitiateDoubleOptInResult;
    }

    /**
     * @return ArrayOfDoubleOptInError
     */
    public function getInitiateDoubleOptInResult()
    {
      return $this->InitiateDoubleOptInResult;
    }

    /**
     * @param ArrayOfDoubleOptInError $InitiateDoubleOptInResult
     * @return InitiateDoubleOptInResponse
     */
    public function setInitiateDoubleOptInResult($InitiateDoubleOptInResult)
    {
      $this->InitiateDoubleOptInResult = $InitiateDoubleOptInResult;
      return $this;
    }

}
