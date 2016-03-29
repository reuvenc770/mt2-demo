<?php
namespace App\Library\Campaigner;
class CreateUpdateMyTemplatesResponse
{

    /**
     * @var CreateUpdateMyTemplatesResult $CreateUpdateMyTemplatesResult
     */
    protected $CreateUpdateMyTemplatesResult = null;

    /**
     * @param CreateUpdateMyTemplatesResult $CreateUpdateMyTemplatesResult
     */
    public function __construct($CreateUpdateMyTemplatesResult)
    {
      $this->CreateUpdateMyTemplatesResult = $CreateUpdateMyTemplatesResult;
    }

    /**
     * @return CreateUpdateMyTemplatesResult
     */
    public function getCreateUpdateMyTemplatesResult()
    {
      return $this->CreateUpdateMyTemplatesResult;
    }

    /**
     * @param CreateUpdateMyTemplatesResult $CreateUpdateMyTemplatesResult
     * @return CreateUpdateMyTemplatesResponse
     */
    public function setCreateUpdateMyTemplatesResult($CreateUpdateMyTemplatesResult)
    {
      $this->CreateUpdateMyTemplatesResult = $CreateUpdateMyTemplatesResult;
      return $this;
    }

}
