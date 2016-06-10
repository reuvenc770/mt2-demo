<?php

class headerFooterObject
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
     * @var string $html
     */
    protected $html = null;

    /**
     * @var string $text
     */
    protected $text = null;

    /**
     * @var boolean $isHeader
     */
    protected $isHeader = null;

    /**
     * @param string $id
     * @param string $name
     * @param string $html
     * @param string $text
     * @param boolean $isHeader
     */
    public function __construct($id, $name, $html, $text, $isHeader)
    {
      $this->id = $id;
      $this->name = $name;
      $this->html = $html;
      $this->text = $text;
      $this->isHeader = $isHeader;
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
     * @return headerFooterObject
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
     * @return headerFooterObject
     */
    public function setName($name)
    {
      $this->name = $name;
      return $this;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
      return $this->html;
    }

    /**
     * @param string $html
     * @return headerFooterObject
     */
    public function setHtml($html)
    {
      $this->html = $html;
      return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
      return $this->text;
    }

    /**
     * @param string $text
     * @return headerFooterObject
     */
    public function setText($text)
    {
      $this->text = $text;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsHeader()
    {
      return $this->isHeader;
    }

    /**
     * @param boolean $isHeader
     * @return headerFooterObject
     */
    public function setIsHeader($isHeader)
    {
      $this->isHeader = $isHeader;
      return $this;
    }

}
