<?php
namespace App\Library\Campaigner;
class ArrayOfCustomAttribute
{

    /**
     * @var CustomAttribute[] $CustomAttribute
     */
    protected $CustomAttribute = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return CustomAttribute[]
     */
    public function getCustomAttribute()
    {
      return $this->CustomAttribute;
    }

    /**
     * @param CustomAttribute[] $CustomAttribute
     * @return ArrayOfCustomAttribute
     */
    public function setCustomAttribute(array $CustomAttribute)
    {
      $this->CustomAttribute = $CustomAttribute;
      return $this;
    }

}
