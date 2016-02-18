<?php
namespace App\Library\Campaigner;
class CreateUpdateAttributeResult
{

    /**
     * @var int $AttributeId
     */
    protected $AttributeId = null;

    /**
     * @param int $AttributeId
     */
    public function __construct($AttributeId)
    {
      $this->AttributeId = $AttributeId;
    }

    /**
     * @return int
     */
    public function getAttributeId()
    {
      return $this->AttributeId;
    }

    /**
     * @param int $AttributeId
     * @return CreateUpdateAttributeResult
     */
    public function setAttributeId($AttributeId)
    {
      $this->AttributeId = $AttributeId;
      return $this;
    }

}
