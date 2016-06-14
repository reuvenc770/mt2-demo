<?php
namespace App\Library\Bronto;
class smsMessageObject
{

    /**
     * @var string $id
     */
    protected $id = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $status
     */
    protected $status = null;

    /**
     * @var string $messageFolderId
     */
    protected $messageFolderId = null;

    /**
     * @var boolean $shortenUrls
     */
    protected $shortenUrls = null;

    /**
     * @var string $content
     */
    protected $content = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $status
     * @param string $messageFolderId
     * @param boolean $shortenUrls
     * @param string $content
     */
    public function __construct($id, $name, $status, $messageFolderId, $shortenUrls, $content)
    {
      $this->id = $id;
      $this->name = $name;
      $this->status = $status;
      $this->messageFolderId = $messageFolderId;
      $this->shortenUrls = $shortenUrls;
      $this->content = $content;
    }

    /**
     * @return string
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param string $id
     * @return smsMessageObject
     */
    public function setId($id)
    {
      $this->id = $id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param string $name
     * @return smsMessageObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
      return $this->status;
    }

    /**
     * @param string $status
     * @return smsMessageObject
     */
    public function setStatus($status)
    {
      $this->status = $status;
      return $this;
    }

    /**
     * @return string
     */
    public function getMessageFolderId()
    {
      return $this->messageFolderId;
    }

    /**
     * @param string $messageFolderId
     * @return smsMessageObject
     */
    public function setMessageFolderId($messageFolderId)
    {
      $this->messageFolderId = $messageFolderId;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getShortenUrls()
    {
      return $this->shortenUrls;
    }

    /**
     * @param boolean $shortenUrls
     * @return smsMessageObject
     */
    public function setShortenUrls($shortenUrls)
    {
      $this->shortenUrls = $shortenUrls;
      return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * @param string $content
     * @return smsMessageObject
     */
    public function setContent($content)
    {
      $this->content = $content;
      return $this;
    }

}
