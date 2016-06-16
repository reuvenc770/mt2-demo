<?php
namespace App\Library\Bronto;
class stringValue
{

    /**
     * @var filterOperator $operator
     */
    protected $operator = null;

    /**
     * @var string $value
     */
    protected $value = null;

    /**
     * @param filterOperator $operator
     * @param string $value
     */
    public function __construct($operator, $value)
    {
      $this->operator = $operator;
      $this->value = $value;
    }

    /**
     * @return filterOperator
     */
    public function getOperator()
    {
      return $this->operator;
    }

    /**
     * @param filterOperator $operator
     * @return stringValue
     */
    public function setOperator($operator)
    {
      $this->operator = $operator;
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
     * @return stringValue
     */
    public function setValue($value)
    {
      $this->value = $value;
      return $this;
    }

}
