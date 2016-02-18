<?php
namespace App\Library\Campaigner;
class ArrayOfAttributeDetails
{

    /**
     * @var AttributeDetails[] $AttributeDetails
     */
    protected $AttributeDetails = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return AttributeDetails[]
     */
    public function getAttributeDetails()
    {
      return $this->AttributeDetails;
    }

    /**
     * @param AttributeDetails[] $AttributeDetails
     * @return ArrayOfAttributeDetails
     */
    public function setAttributeDetails(array $AttributeDetails)
    {
      $this->AttributeDetails = $AttributeDetails;
      return $this;
    }

}
