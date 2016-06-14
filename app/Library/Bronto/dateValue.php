<?php

class dateValue
{

    /**
     * @var filterOperator $operator
     */
    protected $operator = null;

    /**
     * @var \DateTime $value
     */
    protected $value = null;

    /**
     * @param filterOperator $operator
     * @param \DateTime $value
     */
    public function __construct($operator, \DateTime $value)
    {
      $this->operator = $operator;
      $this->value = $value->format(\DateTime::ATOM);
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
     * @return dateValue
     */
    public function setOperator($operator)
    {
      $this->operator = $operator;
      return $this;
    }

    /**
     * @return \DateTime
     */
    public function getValue()
    {
      if ($this->value == null) {
        return null;
      } else {
        try {
          return new \DateTime($this->value);
        } catch (\Exception $e) {
          return false;
        }
      }
    }

    /**
     * @param \DateTime $value
     * @return dateValue
     */
    public function setValue(\DateTime $value)
    {
      $this->value = $value->format(\DateTime::ATOM);
      return $this;
    }

}
