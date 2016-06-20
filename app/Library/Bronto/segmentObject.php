<?php
namespace App\Library\Bronto;
class segmentObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var boolean $matchAnyRule
     */
    protected $matchAnyRule = null;

    /**
     * @var segmentRuleObject[] $rules
     */
    protected $rules = null;

    /**
     * @var \DateTime $lastUpdated
     */
    protected $lastUpdated = null;

    /**
     * @var int $activeCount
     */
    protected $activeCount = null;

    /**
     * @param string $id
     * @param string $name
     * @param boolean $matchAnyRule
     * @param \DateTime $lastUpdated
     * @param int $activeCount
     */
    public function __construct($id, $name, $matchAnyRule, \DateTime $lastUpdated, $activeCount)
    {
      $this->id = $id;
      $this->name = $name;
      $this->matchAnyRule = $matchAnyRule;
      $this->lastUpdated = $lastUpdated->format(\DateTime::ATOM);
      $this->activeCount = $activeCount;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return segmentObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return segmentObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getMatchAnyRule()
    {
      return $this->matchAnyRule;
    }

    /**
     * @param boolean $matchAnyRule
     * @return segmentObject
     */
    public function setMatchAnyRule($matchAnyRule)
    {
      $this->matchAnyRule = $matchAnyRule;
      return $this;
    }

    /**
     * @return segmentRuleObject[]
     */
    public function getRules()
    {
      return $this->rules;
    }

    /**
     * @param segmentRuleObject[] $rules
     * @return segmentObject
     */
    public function setRules(array $rules)
    {
      $this->rules = $rules;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
      if ($this->lastUpdated == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->lastUpdated);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $lastUpdated
     * @return segmentObject
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
      $this->lastUpdated = $lastUpdated->format(\DateTime::ATOM);
      return $this;
    }

    /**
     * @return int
     */
    public function getActiveCount()
    {
      return $this->activeCount;
    }

    /**
     * @param int $activeCount
     * @return segmentObject
     */
    public function setActiveCount($activeCount)
    {
      $this->activeCount = $activeCount;
      return $this;
    }

}
