<?php
namespace App\Library\Bronto;
class readUnsubscribesResponse
{

    /**
     * @var unsubscribeObject[] $return
     */
    protected $return = null;

    /**
     * @param unsubscribeObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return unsubscribeObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param unsubscribeObject[] $return
     * @return readUnsubscribesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
