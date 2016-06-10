<?php
namespace App\Library\Bronto;
class readListsResponse
{

    /**
     * @var mailListObject[] $return
     */
    protected $return = null;

    /**
     * @param mailListObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return mailListObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param mailListObject[] $return
     * @return readListsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
