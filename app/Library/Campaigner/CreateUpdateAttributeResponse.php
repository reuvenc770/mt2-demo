<?php
namespace App\Library\Campaigner;
class CreateUpdateAttributeResponse
{

    /**
     * @var CreateUpdateAttributeResult $CreateUpdateAttributeResult
     */
    protected $CreateUpdateAttributeResult = null;

    /**
     * @param CreateUpdateAttributeResult $CreateUpdateAttributeResult
     */
    public function __construct($CreateUpdateAttributeResult)
    {
      $this->CreateUpdateAttributeResult = $CreateUpdateAttributeResult;
    }

    /**
     * @return CreateUpdateAttributeResult
     */
    public function getCreateUpdateAttributeResult()
    {
      return $this->CreateUpdateAttributeResult;
    }

    /**
     * @param CreateUpdateAttributeResult $CreateUpdateAttributeResult
     * @return CreateUpdateAttributeResponse
     */
    public function setCreateUpdateAttributeResult($CreateUpdateAttributeResult)
    {
      $this->CreateUpdateAttributeResult = $CreateUpdateAttributeResult;
      return $this;
    }

}
