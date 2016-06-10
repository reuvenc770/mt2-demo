<?php
namespace App\Library\Bronto;
class readFieldsResponse
{

    /**
     * @var fieldObject[] $return
     */
    protected $return = null;

    /**
     * @param fieldObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return fieldObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param fieldObject[] $return
     * @return readFieldsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
