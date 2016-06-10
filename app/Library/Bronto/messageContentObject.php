<?php

class messageContentObject
{

    /**
     * @var string $type
     */
    protected $type = null;

    /**
     * @var string $subject
     */
    protected $subject = null;

    /**
     * @var string $content
     */
    protected $content = null;

    /**
     * @param string $type
     * @param string $subject
     * @param string $content
     */
    public function __construct($type, $subject, $content)
    {
      $this->type = $type;
      $this->subject = $subject;
      $this->content = $content;
    }

    /**
     * @return string
     */
    public function getType()
    {
      return $this->type;
    }

    /**
     * @param string $type
     * @return messageContentObject
     */
    public function setType($type)
    {
      $this->type = $type;
      return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
      return $this->subject;
    }

    /**
     * @param string $subject
     * @return messageContentObject
     */
    public function setSubject($subject)
    {
      $this->subject = $subject;
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
     * @return messageContentObject
     */
    public function setContent($content)
    {
      $this->content = $content;
      return $this;
    }

}
