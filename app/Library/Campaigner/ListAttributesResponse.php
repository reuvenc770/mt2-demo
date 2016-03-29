<?php
namespace App\Library\Campaigner;
class ListAttributesResponse
{

    /**
     * @var ArrayOfAttributeDescription $ListAttributesResult
     */
    protected $ListAttributesResult = null;

    /**
     * @param ArrayOfAttributeDescription $ListAttributesResult
     */
    public function __construct($ListAttributesResult)
    {
      $this->ListAttributesResult = $ListAttributesResult;
    }

    /**
     * @return ArrayOfAttributeDescription
     */
    public function getListAttributesResult()
    {
      return $this->ListAttributesResult;
    }

    /**
     * @param ArrayOfAttributeDescription $ListAttributesResult
     * @return ListAttributesResponse
     */
    public function setListAttributesResult($ListAttributesResult)
    {
      $this->ListAttributesResult = $ListAttributesResult;
      return $this;
    }

}
