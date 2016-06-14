<?php
namespace App\Library\Bronto;
class addAccounts
{

    /**
     * @var accountObject[] $accounts
     */
    protected $accounts = null;

    /**
     * @param accountObject[] $accounts
     */
    public function __construct(array $accounts)
    {
      $this->accounts = $accounts;
    }

    /**
     * @return accountObject[]
     */
    public function getAccounts()
    {
      return $this->accounts;
    }

    /**
     * @param accountObject[] $accounts
     * @return addAccounts
     */
    public function setAccounts(array $accounts)
    {
      $this->accounts = $accounts;
      return $this;
    }

}
