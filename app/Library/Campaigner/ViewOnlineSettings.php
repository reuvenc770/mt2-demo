<?php
namespace App\Library\Campaigner;
class ViewOnlineSettings
{

    /**
     * @var string $TextBefore
     */
    protected $TextBefore = null;

    /**
     * @var string $LinkText
     */
    protected $LinkText = null;

    /**
     * @var string $TextAfter
     */
    protected $TextAfter = null;

    /**
     * @param string $TextBefore
     * @param string $LinkText
     * @param string $TextAfter
     */
    public function __construct($TextBefore, $LinkText, $TextAfter)
    {
      $this->TextBefore = $TextBefore;
      $this->LinkText = $LinkText;
      $this->TextAfter = $TextAfter;
    }

    /**
     * @return string
     */
    public function getTextBefore()
    {
      return $this->TextBefore;
    }

    /**
     * @param string $TextBefore
     * @return ViewOnlineSettings
     */
    public function setTextBefore($TextBefore)
    {
      $this->TextBefore = $TextBefore;
      return $this;
    }

    /**
     * @return string
     */
    public function getLinkText()
    {
      return $this->LinkText;
    }

    /**
     * @param string $LinkText
     * @return ViewOnlineSettings
     */
    public function setLinkText($LinkText)
    {
      $this->LinkText = $LinkText;
      return $this;
    }

    /**
     * @return string
     */
    public function getTextAfter()
    {
      return $this->TextAfter;
    }

    /**
     * @param string $TextAfter
     * @return ViewOnlineSettings
     */
    public function setTextAfter($TextAfter)
    {
      $this->TextAfter = $TextAfter;
      return $this;
    }

}
