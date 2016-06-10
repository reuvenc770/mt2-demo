<?php
namespace App\Library\Bronto;
class messageRuleFilter
{

    /**
     * @var filterType $type
     */
    protected $type = null;

    /**
     * @var string[] $id
     */
    protected $id = null;

    /**
     * @var stringValue[] $name
     */
    protected $name = null;

    /**
     * @var string[] $ruleType
     */
    protected $ruleType = null;

    /**
     * @param filterType $type
     */
    public function __construct($type)
    {
      $this->type = $type;
    }

    /**
     * @return filterType
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param filterType $type
     * @return messageRuleFilter
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string[] $id
     * @return messageRuleFilter
     */
    public function setId(array $id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return stringValue[]
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param stringValue[] $name
     * @return messageRuleFilter
     */
    public function setName(array $name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string[]
     */
    public function getRuleType()
    {
      return $this->ruleType;
    }

    /**
     * @param string[] $ruleType
     * @return messageRuleFilter
     */
    public function setRuleType(array $ruleType)
    {
      $this->ruleType = $ruleType;
      return $this;
    }

}
