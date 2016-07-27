<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/27/16
 * Time: 11:36 AM
 */

namespace App\Services;


use App\Repositories\MailingTemplateRepo;

class MailingTemplateService
{

    protected $mailingTemplateRepo;

    public function __construct(MailingTemplateRepo $mailingTemplateRepo)
    {
        $this->mailingTemplateRepo = $mailingTemplateRepo;
    }
}