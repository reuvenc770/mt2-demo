<?php
namespace App\Library\Bronto;
class readWebformsResponse
{

    /**
     * @var webformObject[] $return
     */
    protected $return = null;

    /**
     * @param webformObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return webformObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param webformObject[] $return
     * @return readWebformsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
