<?php
namespace App\Library\Bronto;
class segmentRuleObject
{

    /**
     * @var boolean $canMatchAny
     */
    protected $canMatchAny = null;

    /**
     * @var segmentCriteriaObject[] $criteria
     */
    protected $criteria = null;

    /**
     * @param boolean $canMatchAny
     */
    public function __construct($canMatchAny)
    {
      $this->canMatchAny = $canMatchAny;
    }

    /**
     * @return boolean
     */
    public function getCanMatchAny()
    {
      return $this->canMatchAny;
    }

    /**
     * @param boolean $canMatchAny
     * @return segmentRuleObject
     */
    public function setCanMatchAny($canMatchAny)
    {
      $this->canMatchAny = $canMatchAny;
      return $this;
    }

    /**
     * @return segmentCriteriaObject[]
     */
    public function getCriteria()
    {
      return $this->criteria;
    }

    /**
     * @param segmentCriteriaObject[] $criteria
     * @return segmentRuleObject
     */
    public function setCriteria(array $criteria)
    {
      $this->criteria = $criteria;
      return $this;
    }

}
