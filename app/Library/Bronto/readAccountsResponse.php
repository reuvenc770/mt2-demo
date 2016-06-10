<?php
namespace App\Library\Bronto;
class readAccountsResponse
{

    /**
     * @var accountObject[] $return
     */
    protected $return = null;

    /**
     * @param accountObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return accountObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param accountObject[] $return
     * @return readAccountsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
