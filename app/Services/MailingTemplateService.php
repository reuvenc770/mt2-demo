<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/27/16
 * Time: 11:36 AM
 */

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use Log;
use App\Repositories\MailingTemplateRepo;

class MailingTemplateService
{
    use PaginateList;
    protected $mailingTemplateRepo;

    public function __construct(MailingTemplateRepo $mailingTemplateRepo)
    {
        $this->mailingTemplateRepo = $mailingTemplateRepo;
    }

    public function getAllTemplates(){
        return $this->mailingTemplateRepo->getAll();
    }

    public function insertTemplate($insertData, $espIds){
        $item = $this->mailingTemplateRepo->insertRow($insertData);
        foreach($espIds as $espId){
            $this->mailingTemplateRepo->attachPivot($item,$espId);
        }
    }

    public function retrieveTemplate($id){
        $row = $this->mailingTemplateRepo->getRow($id);
        return $row;
    }

    public function updateTemplate($insertData, $id, $espIds){
        $item = $this->mailingTemplateRepo->updateRow($insertData, $id);
        $this->mailingTemplateRepo->syncPivot($item,$espIds);
        return true;
    }

    public function getModel(){
        return $this->mailingTemplateRepo->getModel();
    }

}