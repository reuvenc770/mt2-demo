<?php

class deleteHeaderFooters
{

    /**
     * @var headerFooterObject[] $footers
     */
    protected $footers = null;

    /**
     * @param headerFooterObject[] $footers
     */
    public function __construct(array $footers)
    {
      $this->footers = $footers;
    }

    /**
     * @return headerFooterObject[]
     */
    public function getFooters()
    {
      return $this->footers;
    }

    /**
     * @param headerFooterObject[] $footers
     * @return deleteHeaderFooters
     */
    public function setFooters(array $footers)
    {
      $this->footers = $footers;
      return $this;
    }

}
