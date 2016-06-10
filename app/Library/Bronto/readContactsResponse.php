<?php
namespace App\Library\Bronto;
class readContactsResponse
{

    /**
     * @var contactObject[] $return
     */
    protected $return = null;

    /**
     * @param contactObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return contactObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param contactObject[] $return
     * @return readContactsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
