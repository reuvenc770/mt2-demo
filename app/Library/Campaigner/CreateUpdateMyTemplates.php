<?php
namespace App\Library\Campaigner;
class CreateUpdateMyTemplates
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $TemplateId
     */
    protected $TemplateId = null;

    /**
     * @var string $TemplateName
     */
    protected $TemplateName = null;

    /**
     * @var string $Description
     */
    protected $Description = null;

    /**
     * @var int $CategoryId
     */
    protected $CategoryId = null;

    /**
     * @var TemplateEditorType $EditorType
     */
    protected $EditorType = null;

    /**
     * @var string $Tags
     */
    protected $Tags = null;

    /**
     * @var boolean $IsVisible
     */
    protected $IsVisible = null;

    /**
     * @var boolean $IsResponsive
     */
    protected $IsResponsive = null;

    /**
     * @var TemplateContent $templateContent
     */
    protected $templateContent = null;

    /**
     * @param Authentication $authentication
     * @param int $TemplateId
     * @param string $TemplateName
     * @param string $Description
     * @param int $CategoryId
     * @param TemplateEditorType $EditorType
     * @param string $Tags
     * @param boolean $IsVisible
     * @param boolean $IsResponsive
     * @param TemplateContent $templateContent
     */
    public function __construct($authentication, $TemplateId, $TemplateName, $Description, $CategoryId, $EditorType, $Tags, $IsVisible, $IsResponsive, $templateContent)
    {
      $this->authentication = $authentication;
      $this->TemplateId = $TemplateId;
      $this->TemplateName = $TemplateName;
      $this->Description = $Description;
      $this->CategoryId = $CategoryId;
      $this->EditorType = $EditorType;
      $this->Tags = $Tags;
      $this->IsVisible = $IsVisible;
      $this->IsResponsive = $IsResponsive;
      $this->templateContent = $templateContent;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
      return $this->authentication;
    }

    /**
     * @param Authentication $authentication
     * @return CreateUpdateMyTemplates
     */
    public function setAuthentication($authentication)
    {
      $this->authentication = $authentication;
      return $this;
    }

    /**
     * @return int
     */
    public function getTemplateId()
    {
      return $this->TemplateId;
    }

    /**
     * @param int $TemplateId
     * @return CreateUpdateMyTemplates
     */
    public function setTemplateId($TemplateId)
    {
      $this->TemplateId = $TemplateId;
      return $this;
    }

    /**
     * @return string
     */
    public function getTemplateName()
    {
      return $this->TemplateName;
    }

    /**
     * @param string $TemplateName
     * @return CreateUpdateMyTemplates
     */
    public function setTemplateName($TemplateName)
    {
      $this->TemplateName = $TemplateName;
      return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
      return $this->Description;
    }

    /**
     * @param string $Description
     * @return CreateUpdateMyTemplates
     */
    public function setDescription($Description)
    {
      $this->Description = $Description;
      return $this;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
      return $this->CategoryId;
    }

    /**
     * @param int $CategoryId
     * @return CreateUpdateMyTemplates
     */
    public function setCategoryId($CategoryId)
    {
      $this->CategoryId = $CategoryId;
      return $this;
    }

    /**
     * @return TemplateEditorType
     */
    public function getEditorType()
    {
      return $this->EditorType;
    }

    /**
     * @param TemplateEditorType $EditorType
     * @return CreateUpdateMyTemplates
     */
    public function setEditorType($EditorType)
    {
      $this->EditorType = $EditorType;
      return $this;
    }

    /**
     * @return string
     */
    public function getTags()
    {
      return $this->Tags;
    }

    /**
     * @param string $Tags
     * @return CreateUpdateMyTemplates
     */
    public function setTags($Tags)
    {
      $this->Tags = $Tags;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsVisible()
    {
      return $this->IsVisible;
    }

    /**
     * @param boolean $IsVisible
     * @return CreateUpdateMyTemplates
     */
    public function setIsVisible($IsVisible)
    {
      $this->IsVisible = $IsVisible;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getIsResponsive()
    {
      return $this->IsResponsive;
    }

    /**
     * @param boolean $IsResponsive
     * @return CreateUpdateMyTemplates
     */
    public function setIsResponsive($IsResponsive)
    {
      $this->IsResponsive = $IsResponsive;
      return $this;
    }

    /**
     * @return TemplateContent
     */
    public function getTemplateContent()
    {
      return $this->templateContent;
    }

    /**
     * @param TemplateContent $templateContent
     * @return CreateUpdateMyTemplates
     */
    public function setTemplateContent($templateContent)
    {
      $this->templateContent = $templateContent;
      return $this;
    }

}
