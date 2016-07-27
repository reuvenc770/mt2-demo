<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/27/16
 * Time: 11:36 AM
 */

namespace App\Repositories;


use App\Models\MailingTemplate;

class MailingTemplateRepo
{
    protected $mailingTemplate;

    public function __construct(MailingTemplate $mailingTemplate)
    {
        $this->mailingTemplate = $mailingTemplate;
    }

}