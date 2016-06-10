<?php
namespace App\Library\Bronto;
class readSMSKeywordsResponse
{

    /**
     * @var smsKeywordObject[] $return
     */
    protected $return = null;

    /**
     * @param smsKeywordObject[] $return
     */
    public function __construct(array $return)
    {
      $this->return = $return;
    }

    /**
     * @return smsKeywordObject[]
     */
    public function getReturn()
    {
      return $this->return;
    }

    /**
     * @param smsKeywordObject[] $return
     * @return readSMSKeywordsResponse
     */
    public function setReturn(array $return)
    {
      $this->return = $return;
      return $this;
    }

}
