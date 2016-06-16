<?php

class clearLists
{

    /**
     * @var mailListObject[] $list
     */
    protected $list = null;

    /**
     * @param mailListObject[] $list
     */
    public function __construct(array $list)
    {
      $this->list = $list;
    }

    /**
     * @return mailListObject[]
     */
    public function getList()
    {
      return $this->list;
    }

    /**
     * @param mailListObject[] $list
     * @return clearLists
     */
    public function setList(array $list)
    {
      $this->list = $list;
      return $this;
    }

}
