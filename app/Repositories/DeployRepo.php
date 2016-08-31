<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:37 PM
 */

namespace App\Repositories;


use App\Models\Deploy;
use DB;
use App\Facades\EspApiAccount;
use Log;
class DeployRepo
{
    protected $deploy;

    public function __construct(Deploy $deploy)
    {
        $this->deploy = $deploy;
    }

    public function getModel($searchType = null, $searchData = null)
    {
        $query = $this->deploy
            ->leftJoin('esp_accounts', 'deploys.esp_account_id', '=', 'esp_accounts.id')
            ->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
            ->leftJoin('mailing_templates', 'mailing_templates.id', '=', 'deploys.template_id')
            ->leftJoin('domains', 'domains.id', '=', 'deploys.mailing_domain_id')
            ->leftJoin('domains as domains2', 'domains2.id', '=', 'deploys.content_domain_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'deploys.subject_id')
            ->leftJoin('froms', 'froms.id', '=', 'deploys.from_id')
            ->leftJoin('creatives', 'creatives.id', '=', 'deploys.creative_id')
            ->leftJoin('list_profiles', 'list_profiles.id', '=', 'deploys.list_profile_id')
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
                'deployed',
                'notes');
        if($searchData && $searchType) {
            $query = $this->mapQuery($searchType, $searchData, $query);
        }

        $query->orderBy('deploy_id', 'desc');
        return $query;
    }

    public function insert($data)
    {
        return $this->deploy->create($data);
    }

    public function getDeploy($id)
    {
        return $this->deploy->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
            ->select(['deploys.*', 'offers.name as offer_name'])
            ->where('deploys.id', $id)->first();
    }

    public function updateOrCreate($data)
    {
        $this->deploy->updateOrCreate(['id' => $data['id']], $data);
    }

    public function update($data, $id)
    {
        return $this->deploy->where('id', $id)->update($data);
    }

    public function retrieveRowsForCsv($rows)
    {
        return $this->deploy->whereIn('id', $rows)
            ->select("id as deploy_id",
                "send_date as deploy_date",
                "esp_account_id",
                "offer_id",
                "creative_id",
                "from_id",
                "subject_id",
                "template_id",
                "mailing_domain_id",
                "content_domain_id",
                "list_profile_id",
                "cake_affiliate_id",
                "notes"
            )->get();
    }

    //Rob will probably say oh we can just do this, but I am not rob and I suck at sql
    public function validateOldDeploy($deploy)
    {
        $errors = array();
        //deploy_id
        if($deploy['deploy_id'] != 0) {
            if (isset($deploy['deploy_id'])) {
                $count = DB::select("Select count(*) as count from deploys where id = :id", ['id' => $deploy['deploy_id']])[0];
                if ($count->count == 0) {
                    $errors[] = "Deploy ID is not Valid.";
                }
            } else {
                $errors[] = "Deploy ID is missing";
            }
        }
        if (isset($deploy['esp_account_id'])) {
            $count = DB::select("Select count(*) as count from esp_accounts where id = :id", ['id' => $deploy['esp_account_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Esp Account ID is not Valid.";
            }
        } else {
            $errors[] = "Esp Account ID is missing";
        }
        //offer real?
        if (isset($deploy['offer_id'])) {
            $count = DB::select("Select count(*) as count from offers where id = :id", ['id' => $deploy['offer_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Offer ID is not Valid.";
            }
        } else {
            $errors[] = "Offer ID is missing";
        }
        //creative ok?
        if (isset($deploy['creative_id'])) {
            $count = DB::select("Select count(*) as count from creatives where id = :id and approved = 1 and status = 1", ['id' => $deploy['creative_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Creative is not active or wrong";
            }
        } else {
            $errors[] = "Creative ID is missing";
        }
        //from ok?
        if (isset($deploy['from_id'])) {
            $count = DB::select("Select count(*) as count from froms where id = :id and is_approved = 1 and status = 1", ['id' => $deploy['from_id']])[0];
            if ($count->count == 0) {
                $errors[] = "From is not active or wrong";
            }
        } else {
            $errors[] = "From  ID is missing";
        }
        //subject ok?
        if (isset($deploy['subject_id'])) {
            $count = DB::select("Select count(*) as count from subjects where id = :id and is_approved = 1 and status = 1", ['id' => $deploy['subject_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Subject is not active or wrong";
            }
        } else {
            $errors[] = "Subject Id is missing";
        }
        //template_ok
        if (isset($deploy['template_id'])) {
            $count = DB::select("Select count(*) as count from mailing_templates where id = :id", ['id' => $deploy['template_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Template ID is not active or wrong";
            }
        } else {
            $errors[] = "Template ID is missing";
        }
        //mailing domain
        if (isset($deploy['mailing_domain_id'])) {
            $count = DB::select("Select count(*) as count from domains where id = :id and domain_type = 1", ['id' => $deploy['mailing_domain_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Mailing Domain ID is invalid or not Mailing Domain";
            }
        } else {
            $errors[] = "Mailing Domain is missing";
        }
        //content domain
        if (isset($deploy['content_domain_id'])) {
            $count = DB::select("Select count(*) as count from domains where id = :id and domain_type = 2", ['id' => $deploy['content_domain_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Content Domain is invalid or not content domain";
            }
        } else {
            $errors[] = "Content Domain is missing";
        }
        //list profile
        if (isset($deploy['list_profile_id'])) {
            $count = DB::select("Select count(*) as count from list_profiles where id = :id", ['id' => $deploy['list_profile_id']])[0];
            if ($count->count == 0) {
                $errors[] = "List Profile is not active or wrong";
            }
        } else {
            $errors[] = "List Profile ID is missing";
        }
        //cake
        if (isset($deploy['list_profile_id'])) {
        $count = DB::connection('mt1_data')->select("Select count(*) as count from EspAdvertiserJoin where affiliateID = :id",['id'=> $deploy['deploy_id']])[0];
        if($count->count == 0){
            $errors[] = "Cake Affiliate is wrong";
         } } else {
            $errors[] = "Cake Affiliate ID is missing";
        }

        return $errors;
    }

    public function massInsert($data){
        foreach($data as $row){
            $row['id'] = $row['deploy_id'];
            $row['send_date'] = $row['deploy_date'];
            unset($row['deploy_id']);
            unset($row['deploy_date']);
            unset($row['valid']);
            $this->deploy->updateOrCreate(['id'=> $row['id']],$row);
        }
        return true;
    }

    public function returnCsvHeader()
    {
        return ['deploy_id', "deploy_date", "esp_account_id", "offer_id", "creative_id", "from_id", "subject_id", "template_id",
            "mailing_domain_id", "content_domain_id", "list_profile_id", "cake_affiliate_id", "notes"];
    }


    private function mapQuery($searchType, $searchData, $query){


        switch($searchType){
            case "esp":
                $espAccounts = collect(EspApiAccount::getAllAccountsByESPName($searchData));
                $espAccountIds = $espAccounts->pluck('id');
                $query = $query->wherein('domains.esp_account_id',$espAccountIds);
                break;
            case "espAccount":
                $query = $query->where('domains.esp_account_id',$searchData);
                break;
            case "status":
                $query = $query->where('deploys.deployed',$searchData);
                break;
            case "date":
                $dates = explode(',',$searchData);
                $query = $query->whereBetween('deploys.send_date',$dates);
                break;
            case "offer":
                $query = $query->where('offers.name','LIKE', "$searchData%");
                break;
            case "deploy":
                $query = $query->where('deploys.id',$searchData);
                break;
            default:

        };
        return $query;
    }
}