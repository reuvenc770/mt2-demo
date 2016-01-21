<?php
namespace App\Library\Campaigner;
class GetEmailTemplateResponse
{

    /**
     * @var GetEmailTemplateResult $GetEmailTemplateResult
     */
    protected $GetEmailTemplateResult = null;

    /**
     * @param GetEmailTemplateResult $GetEmailTemplateResult
     */
    public function __construct($GetEmailTemplateResult)
    {
      $this->GetEmailTemplateResult = $GetEmailTemplateResult;
    }

    /**
     * @return GetEmailTemplateResult
     */
    public function getGetEmailTemplateResult()
    {
      return $this->GetEmailTemplateResult;
    }

    /**
     * @param GetEmailTemplateResult $GetEmailTemplateResult
     * @return GetEmailTemplateResponse
     */
    public function setGetEmailTemplateResult($GetEmailTemplateResult)
    {
      $this->GetEmailTemplateResult = $GetEmailTemplateResult;
      return $this;
    }

}
