<?php
namespace App\Library\Campaigner;
class ArrayOfEmailTemplateDescription
{

    /**
     * @var EmailTemplateDescription[] $EmailTemplateDescription
     */
    protected $EmailTemplateDescription = null;

    
    public function __construct()
    {
    
    }

    /**
     * @return EmailTemplateDescription[]
     */
    public function getEmailTemplateDescription()
    {
      return $this->EmailTemplateDescription;
    }

    /**
     * @param EmailTemplateDescription[] $EmailTemplateDescription
     * @return ArrayOfEmailTemplateDescription
     */
    public function setEmailTemplateDescription(array $EmailTemplateDescription)
    {
      $this->EmailTemplateDescription = $EmailTemplateDescription;
      return $this;
    }

}
