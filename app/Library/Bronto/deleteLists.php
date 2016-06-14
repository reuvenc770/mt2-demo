<?php

class deleteLists
{

    /**
     * @var mailListObject[] $lists
     */
    protected $lists = null;

    /**
     * @param mailListObject[] $lists
     */
    public function __construct(array $lists)
    {
      $this->lists = $lists;
    }

    /**
     * @return mailListObject[]
     */
    public function getLists()
    {
      return $this->lists;
    }

    /**
     * @param mailListObject[] $lists
     * @return deleteLists
     */
    public function setLists(array $lists)
    {
      $this->lists = $lists;
      return $this;
    }

}
