<?php
namespace App\Library\Campaigner;
class GetEmailTemplate
{

    /**
     * @var Authentication $authentication
     */
    protected $authentication = null;

    /**
     * @var int $templateId
     */
    protected $templateId = null;

    /**
     * @param Authentication $authentication
     * @param int $templateId
     */
    public function __construct($authentication, $templateId)
    {
      $this->authentication = $authentication;
      $this->templateId = $templateId;
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
     * @return GetEmailTemplate
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
      return $this->templateId;
    }

    /**
     * @param int $templateId
     * @return GetEmailTemplate
     */
    public function setTemplateId($templateId)
    {
      $this->templateId = $templateId;
      return $this;
    }

}
