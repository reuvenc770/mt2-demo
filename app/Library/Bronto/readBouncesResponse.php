<?php
namespace App\Library\Bronto;
class readBouncesResponse
{

    /**
     * @var bounceObject[] $return
     */
    protected $return = null;

    /**
     * @param bounceObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return bounceObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param bounceObject[] $return
     * @return readBouncesResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
