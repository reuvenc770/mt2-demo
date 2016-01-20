<?php
namespace App\Library\Campaigner;
class ListEmailTemplatesResponse
{

    /**
     * @var ArrayOfEmailTemplateDescription $ListEmailTemplatesResult
     */
    protected $ListEmailTemplatesResult = null;

    /**
     * @param ArrayOfEmailTemplateDescription $ListEmailTemplatesResult
     */
    public function __construct($ListEmailTemplatesResult)
    {
      $this->ListEmailTemplatesResult = $ListEmailTemplatesResult;
    }

    /**
     * @return ArrayOfEmailTemplateDescription
     */
    public function getListEmailTemplatesResult()
    {
      return $this->ListEmailTemplatesResult;
    }

    /**
     * @param ArrayOfEmailTemplateDescription $ListEmailTemplatesResult
     * @return ListEmailTemplatesResponse
     */
    public function setListEmailTemplatesResult($ListEmailTemplatesResult)
    {
      $this->ListEmailTemplatesResult = $ListEmailTemplatesResult;
      return $this;
    }

}
