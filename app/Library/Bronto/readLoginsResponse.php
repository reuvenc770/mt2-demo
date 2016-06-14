<?php
namespace App\Library\Bronto;
class readLoginsResponse
{

    /**
     * @var loginObject[] $return
     */
    protected $return = null;

    /**
     * @param loginObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return loginObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param loginObject[] $return
     * @return readLoginsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
