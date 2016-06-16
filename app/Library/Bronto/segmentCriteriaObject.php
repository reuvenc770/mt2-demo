<?php
namespace App\Library\Bronto;
class segmentCriteriaObject
{

    /**
     * @var string $operator
     */
    protected $operator = null;

    /**
     * @var string $condition
     */
    protected $condition = null;

    /**
     * @var string $value
     */
    protected $value = null;

    /**
     * @param string $operator
     * @param string $condition
     * @param string $value
     */
    public function __construct($operator, $condition, $value)
    {
      $this->operator = $operator;
      $this->condition = $condition;
      $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
      return $this->operator;
    }

    /**
     * @param string $operator
     * @return segmentCriteriaObject
     */
    public function setOperator($operator)
    {
      $this->operator = $operator;
      return $this;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
      return $this->condition;
    }

    /**
     * @param string $condition
     * @return segmentCriteriaObject
     */
    public function setCondition($condition)
    {
      $this->condition = $condition;
      return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
      return $this->value;
    }

    /**
     * @param string $value
     * @return segmentCriteriaObject
     */
    public function setValue($value)
    {
      $this->value = $value;
      return $this;
    }

}
