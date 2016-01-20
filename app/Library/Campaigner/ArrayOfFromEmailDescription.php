<?php
namespace App\Library\Campaigner;
class ArrayOfFromEmailDescription
{

    /**
     * @var FromEmailDescription[] $FromEmailDescription
     */
    protected $FromEmailDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return FromEmailDescription[]
     */
    public function getFromEmailDescription()
    {
      return $this->FromEmailDescription;
    }

    /**
     * @param FromEmailDescription[] $FromEmailDescription
     * @return ArrayOfFromEmailDescription
     */
    public function setFromEmailDescription(array $FromEmailDescription)
    {
      $this->FromEmailDescription = $FromEmailDescription;
      return $this;
    }

}
