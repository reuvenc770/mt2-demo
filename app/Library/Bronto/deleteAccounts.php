<?php

class deleteAccounts
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
     * @return deleteAccounts
     */
    public function setAccounts(array $accounts)
    {
      $this->accounts = $accounts;
      return $this;
    }

}
