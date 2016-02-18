<?php
namespace App\Library\Campaigner;
class ArrayOfAttributeData
{

    /**
     * @var AttributeData[] $AttributeData
     */
    protected $AttributeData = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return AttributeData[]
     */
    public function getAttributeData()
    {
      return $this->AttributeData;
    }

    /**
     * @param AttributeData[] $AttributeData
     * @return ArrayOfAttributeData
     */
    public function setAttributeData(array $AttributeData)
    {
      $this->AttributeData = $AttributeData;
      return $this;
    }

}
