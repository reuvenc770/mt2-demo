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

    public function insertRow($data){
       return $mailingTemplate = $this->mailingTemplate->create($data);
    }

    public function updateRow($data, $id){
        $this->mailingTemplate->find( $id )->update($data);
        return $this->getRow($id);
    }

    public function attachPivot($template, $id){
        return $template->espAccounts()->attach($id);
    }

    //adds news rows kills old rows.
    public function syncPivot($template, $id){
        return $template->espAccounts()->sync($id);

    }

    public function getRow($id){
        return $this->mailingTemplate->with("espAccounts")->find($id);
    }

    public function getAll(){
        return $this->mailingTemplate->get();
    }
    public function getModel(){
        return $this->mailingTemplate;
    }

    public function updateOrCreate($data) {
        $this->mailingTemplate->updateOrCreate(['id' => $data['id']], $data);
    }

}