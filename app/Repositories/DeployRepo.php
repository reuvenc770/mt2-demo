<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:37 PM
 */

namespace App\Repositories;


use App\Models\Deploy;

class DeployRepo
{
    protected $deploy;

    public function __construct(Deploy $deploy){
        $this->deploy = $deploy;
    }

    public function getModel(){
        return $this->deploy
            ->leftJoin('esp_accounts', 'deploys.esp_account_id', '=', 'esp_accounts.id')
            ->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
            ->leftJoin('mailing_templates', 'mailing_templates.id', '=', 'deploys.template_id')
            ->leftJoin('domains', 'domains.id', '=', 'deploys.mailing_domain_id')
            ->leftJoin('domains as domains2', 'domains2.id', '=', 'deploys.content_domain_id')
            ->leftJoin('subjects','subjects.id','=','deploys.subject_id')
            ->leftJoin('froms','froms.id','=','deploys.from_id')
            ->leftJoin('creatives','creatives.id','=','deploys.creative_id')
            ->leftJoin('list_profiles', 'list_profiles.id','=', 'deploys.list_profile_id')
            ->select("send_date",
                'deploys.id as deploy_id',
                'esp_accounts.account_name',
                'offers.name as offer_name',
                'mailing_templates.template_name',
                'domains.domain_name as mailing_domain',
                'domains2.domain_name as content_domain',
                'subjects.subject_line as subject',
                'froms.from_line as from',
                'creatives.file_name as creative',
                'list_profiles.profile_name as list_profile',
                'cake_affiliate_id',
                'notes');
    }

    public function insert($data){
        return $this->deploy->create($data);
    }

    public function getDeploy($id){
        return $this->deploy->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
                            ->select(['deploys.*','offers.name as offer_name'])
                            ->where('deploys.id',$id)->first();
    }


    public function updateOrCreate($data) {
        $this->deploy->updateOrCreate(['id' => $data['id']], $data);
    }

    public function update($data,$id){
        return $this->deploy->where('id',$id)->update($data);
    }
}