<?php
namespace App\Library\Bronto;
class updateSMSKeywords
{

    /**
     * @var smsKeywordObject[] $keyword
     */
    protected $keyword = null;

    /**
     * @param smsKeywordObject[] $keyword
     */
    public function __construct(array $keyword)
    {
      $this->keyword = $keyword;
    }

    /**
     * @return smsKeywordObject[]
     */
    public function getKeyword()
    {
      return $this->keyword;
    }

    /**
     * @param smsKeywordObject[] $keyword
     * @return updateSMSKeywords
     */
    public function setKeyword(array $keyword)
    {
      $this->keyword = $keyword;
      return $this;
    }

}
