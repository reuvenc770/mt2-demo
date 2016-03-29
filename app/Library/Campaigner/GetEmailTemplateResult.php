<?php
namespace App\Library\Campaigner;
class GetEmailTemplateResult
{

    /**
     * @var EmailTemplateData $EmailTemplateData
     */
    protected $EmailTemplateData = null;

    /**
     * @param EmailTemplateData $EmailTemplateData
     */
    public function __construct($EmailTemplateData)
    {
      $this->EmailTemplateData = $EmailTemplateData;
    }

    /**
     * @return EmailTemplateData
     */
    public function getEmailTemplateData()
    {
      return $this->EmailTemplateData;
    }

    /**
     * @param EmailTemplateData $EmailTemplateData
     * @return GetEmailTemplateResult
     */
    public function setEmailTemplateData($EmailTemplateData)
    {
      $this->EmailTemplateData = $EmailTemplateData;
      return $this;
    }

}
