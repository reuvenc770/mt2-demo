<?php
namespace App\Library\Bronto;
class updateAccounts
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
     * @return updateAccounts
     */
    public function setAccounts(array $accounts)
    {
      $this->accounts = $accounts;
      return $this;
    }

}
