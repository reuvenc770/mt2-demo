<?php
namespace App\Library\Campaigner;
class ArrayOfDomain
{

    /**
     * @var Domain[] $Domain
     */
    protected $Domain = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return Domain[]
     */
    public function getDomain()
    {
      return $this->Domain;
    }

    /**
     * @param Domain[] $Domain
     * @return ArrayOfDomain
     */
    public function setDomain(array $Domain)
    {
      $this->Domain = $Domain;
      return $this;
    }

}
