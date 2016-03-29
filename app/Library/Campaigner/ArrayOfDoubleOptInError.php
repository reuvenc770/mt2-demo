<?php
namespace App\Library\Campaigner;
class ArrayOfDoubleOptInError
{

    /**
     * @var DoubleOptInError[] $DoubleOptInError
     */
    protected $DoubleOptInError = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return DoubleOptInError[]
     */
    public function getDoubleOptInError()
    {
      return $this->DoubleOptInError;
    }

    /**
     * @param DoubleOptInError[] $DoubleOptInError
     * @return ArrayOfDoubleOptInError
     */
    public function setDoubleOptInError(array $DoubleOptInError)
    {
      $this->DoubleOptInError = $DoubleOptInError;
      return $this;
    }

}
