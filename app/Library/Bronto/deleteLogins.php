<?php

class deleteLogins
{

    /**
     * @var loginObject[] $accounts
     */
    protected $accounts = null;

    /**
     * @param loginObject[] $accounts
     */
    public function __construct(array $accounts)
    {
      $this->accounts = $accounts;
    }

    /**
     * @return loginObject[]
     */
    public function getAccounts()
    {
      return $this->accounts;
    }

    /**
     * @param loginObject[] $accounts
     * @return deleteLogins
     */
    public function setAccounts(array $accounts)
    {
      $this->accounts = $accounts;
      return $this;
    }

}
