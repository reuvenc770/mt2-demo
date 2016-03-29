<?php
namespace App\Library\Campaigner;
class EmailTemplateData
{

    /**
     * @var int $Id
     */
    protected $Id = null;

    /**
     * @var string $Name
     */
    protected $Name = null;

    /**
     * @var string $HtmlContent
     */
    protected $HtmlContent = null;

    /**
     * @var string $TextContent
     */
    protected $TextContent = null;

    /**
     * @param int $Id
     * @param string $Name
     * @param string $HtmlContent
     * @param string $TextContent
     */
    public function __construct($Id, $Name, $HtmlContent, $TextContent)
    {
      $this->Id = $Id;
      $this->Name = $Name;
      $this->HtmlContent = $HtmlContent;
      $this->TextContent = $TextContent;
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
     * @return EmailTemplateData
     */
    public function setId($Id)
    {
      $this->Id = $Id;
      return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
      return $this->Name;
    }

    /**
     * @param string $Name
     * @return EmailTemplateData
     */
    public function setName($Name)
    {
      $this->Name = $Name;
      return $this;
    }

    /**
     * @return string
     */
    public function getHtmlContent()
    {
      return $this->HtmlContent;
    }

    /**
     * @param string $HtmlContent
     * @return EmailTemplateData
     */
    public function setHtmlContent($HtmlContent)
    {
      $this->HtmlContent = $HtmlContent;
      return $this;
    }

    /**
     * @return string
     */
    public function getTextContent()
    {
      return $this->TextContent;
    }

    /**
     * @param string $TextContent
     * @return EmailTemplateData
     */
    public function setTextContent($TextContent)
    {
      $this->TextContent = $TextContent;
      return $this;
    }

}
