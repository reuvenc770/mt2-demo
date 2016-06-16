<?php
namespace App\Library\Bronto;
class addHeaderFooters
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
     * @return addHeaderFooters
     */
    public function setFooters(array $footers)
    {
      $this->footers = $footers;
      return $this;
    }

}
