<?php
namespace App\Library\Bronto;
class smsMessageFieldObject
{

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $content
     */
    protected $content = null;

    /**
     * @param string $name
     * @param string $content
     */
    public function __construct($name, $content)
    {
      $this->name = $name;
      $this->content = $content;
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
     * @return smsMessageFieldObject
     */
    public function setName($name)
    {
      $this->name = $name;
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
     * @return smsMessageFieldObject
     */
    public function setContent($content)
    {
      $this->content = $content;
      return $this;
    }

}
