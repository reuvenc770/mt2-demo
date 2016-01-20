<?php
namespace App\Library\Campaigner;
class UnsubscribeMessageData
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $MessageText
     */
    protected $MessageText = null;

    /**
     * @var string $MessageHtml
     */
    protected $MessageHtml = null;

    /**
     * @param int $Id
     * @param string $MessageText
     * @param string $MessageHtml
     */
    public function __construct($Id, $MessageText, $MessageHtml)
    {
      $this->Id = $Id;
      $this->MessageText = $MessageText;
      $this->MessageHtml = $MessageHtml;
    }

    /**
     * @return int
     */
    public function getId()
    {
      return $this->Id;
    }

    /**
     * @param int $Id
     * @return UnsubscribeMessageData
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageText()
    {
      return $this->MessageText;
    }

    /**
     * @param string $MessageText
     * @return UnsubscribeMessageData
     */
    public function setMessageText($MessageText)
    {
      $this->MessageText = $MessageText;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageHtml()
    {
      return $this->MessageHtml;
    }

    /**
     * @param string $MessageHtml
     * @return UnsubscribeMessageData
     */
    public function setMessageHtml($MessageHtml)
    {
      $this->MessageHtml = $MessageHtml;
      return $this;
    }

}
