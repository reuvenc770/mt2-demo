<?php
namespace App\Library\Campaigner;
class ArrayOfAttributeDescription
{

    /**
     * @var AttributeDescription[] $AttributeDescription
     */
    protected $AttributeDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return AttributeDescription[]
     */
    public function getAttributeDescription()
    {
      return $this->AttributeDescription;
    }

    /**
     * @param AttributeDescription[] $AttributeDescription
     * @return ArrayOfAttributeDescription
     */
    public function setAttributeDescription(array $AttributeDescription)
    {
      $this->AttributeDescription = $AttributeDescription;
      return $this;
    }

}
