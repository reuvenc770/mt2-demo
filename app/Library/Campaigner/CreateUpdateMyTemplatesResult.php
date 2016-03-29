<?php
namespace App\Library\Campaigner;
class CreateUpdateMyTemplatesResult
{

    /**
     * @var int $TemplateId
     */
    protected $TemplateId = null;

    /**
     * @param int $TemplateId
     */
    public function __construct($TemplateId)
    {
      $this->TemplateId = $TemplateId;
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
     * @return CreateUpdateMyTemplatesResult
     */
    public function setTemplateId($TemplateId)
    {
      $this->TemplateId = $TemplateId;
      return $this;
    }

}
