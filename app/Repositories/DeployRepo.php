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
use Cache;
use Carbon\Carbon;
class DeployRepo
{
    protected $deploy;

    public function __construct(Deploy $deploy)
    {
        $this->deploy = $deploy;
    }

    public function getModel($searchData = null)
    {        
        $listProfileSchema = config('database.connections.list_profile.database');
        $query = $this->deploy
            ->leftJoin('esp_accounts', 'deploys.esp_account_id', '=', 'esp_accounts.id')
            ->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
            ->leftJoin('mailing_templates', 'mailing_templates.id', '=', 'deploys.template_id')
            ->leftJoin('domains', 'domains.id', '=', 'deploys.mailing_domain_id')
            ->leftJoin('domains as domains2', 'domains2.id', '=', 'deploys.content_domain_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'deploys.subject_id')
            ->leftJoin('froms', 'froms.id', '=', 'deploys.from_id')
            ->leftJoin('creatives', 'creatives.id', '=', 'deploys.creative_id')
            ->leftJoin("$listProfileSchema.list_profile_combines", 'list_profile_combines.id', '=', 'deploys.list_profile_combine_id')
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
                'list_profile_combines.name as list_profile',
                'cake_affiliate_id',
                'deployment_status',
                'creatives.is_approved as creative_approval',
                'creatives.status as creative_status','subjects.is_approved as subject_approval',
                'subjects.status as subject_status','froms.is_approved as from_approval',
                'froms.status as from_status',
                'notes');

        if('' !== $searchData) {
            $query = $this->mapQuery($searchData, $query);
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
            ->select(['deploys.*', 'offers.name as offer_name', 'offers.exclude_days'])
            ->where('deploys.id', $id)->first();
    }

    public function updateOrCreate($data)
    {
        $this->deploy->updateOrCreate(['id' => $data['id']], $data);
    }

    public function update($data, $id)
    {
        return $this->deploy->find($id)->update($data);
    }

    public function retrieveRowsForCsv($rows)
    {
        return $this->deploy->whereIn('id', $rows)
            ->select("id as deploy_id",
                "send_date",
                "esp_account_id",
                "offer_id",
                "creative_id",
                "from_id",
                "subject_id",
                "template_id",
                "mailing_domain_id",
                "content_domain_id",
                "list_profile_combine_id",
                "cake_affiliate_id",
                "encrypt_cake",
                "fully_encrypt",
                "url_format",
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

        $otherErrors = $this->validateDeploy($deploy);
        return array_merge($errors,$otherErrors);


    }

    public function massInsert($data){
        foreach($data as $row){
            $row['id'] = $row['deploy_id'];
            unset($row['deploy_id']);
            unset($row['valid']);
            $this->deploy->updateOrCreate(['id'=> $row['id']],$row);
        }
        return true;
    }

    public function deployPackages($data){
        $this->deploy->wherein('id',$data)->update(['deployment_status' => Deploy::CREATED_PACKAGE_STATUS]);
        Cache::tags($this->deploy->getClassName())->flush();
        return true;
    }

    public function returnCsvHeader()
    {
        return ['deploy_id', "send_date", "esp_account_id", "offer_id", "creative_id", "from_id", "subject_id", "template_id",
            "mailing_domain_id", "content_domain_id", "list_profile_combine_id", "cake_affiliate_id","encrypt_cake", "fully_encrypt", "url_format", "notes"];
    }


    private function mapQuery($searchData, $query){
        $searchData = json_decode($searchData, true);
        
        if (isset($searchData['esp'])) {
            $espAccounts = collect(EspApiAccount::getAllAccountsByESPName($searchData['esp']));
            $espAccountIds = $espAccounts->pluck('id');
            $query->whereIn('deploys.esp_account_id', $espAccountIds);
        }

        if (isset($searchData['espAccountId'])) {
            $query->where('deploys.esp_account_id', (int)$searchData['espAccountId']);
        }

        if (isset($searchData['deployId'])) {
            $query->where('deploys.id', (int)$searchData['deployId']);
        }

        if (isset($searchData['status'])) {
            $query->where('deploys.deployment_status', (int)$searchData['status']);
        }

        if (isset($searchData['offerNameWildcard'])) {
            $query->where('offers.name','LIKE', $searchData['offerNameWildcard'] . '%');
        }

        if (isset($searchData['dates'])) {
            $dates = explode(',', $searchData['dates']);
            $query->whereBetween('deploys.send_date',$dates);
        }

        return $query;
    }

    public function getPendingDeploys() {
        return $this->deploy->where('deployment_status',Deploy::PENDING_PACKAGE_STATUS)->get();
    }

    public function getDeployDetailsByIds($deployIds){
        return $this->deploy
            ->leftJoin('esp_accounts', 'deploys.esp_account_id', '=', 'esp_accounts.id')
            ->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
            ->leftJoin('mailing_templates', 'mailing_templates.id', '=', 'deploys.template_id')
            ->leftJoin('domains', 'domains.id', '=', 'deploys.mailing_domain_id')
            ->leftJoin('domains as domains2', 'domains2.id', '=', 'deploys.content_domain_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'deploys.subject_id')
            ->leftJoin('froms', 'froms.id', '=', 'deploys.from_id')
            ->leftJoin('creatives', 'creatives.id', '=', 'deploys.creative_id')
            ->leftJoin('list_profile_combines', 'list_profile_combines.id', '=', 'deploys.list_profile_combine_id')
            ->wherein("deploys.id",explode(",",$deployIds))
            ->where("deployment_status",1)
            ->selectRaw('send_date, deploys.id as deploy_id,
              IFNULL(esp_accounts.account_name, "DATA IS MISSING") AS account_name,
                IFNULL(mailing_templates.template_name, "DATA IS MISSING") as template_name,
                IFNULL(domains.domain_name, "DATA IS MISSING") as mailing_domain,
                IFNULL(domains2.domain_name, "DATA IS MISSING") as content_domain,
                IFNULL(subjects.subject_line, "DATA IS MISSING") as subject_line,
                IFNULL(froms.from_line, "DATA IS MISSING") as from_line,
                IFNULL(creatives.file_name, "DATA IS MISSING") as creative')->get();
    }

    public function duplicateDomainToDate($deployId, $date){
        $deploy = $this->deploy->find($deployId);
        $newDeploy = $deploy->replicate();
        $newDeploy->send_date = $date;
        return  $newDeploy;
    }

    public function validateDeploy($deploy){
        $errors = array();
        if (isset($deploy['esp_account_id'])) {
            $count = DB::select("Select count(*) as count from esp_accounts where id = :id and status = 1", ['id' => $deploy['esp_account_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Esp Account ID is not Valid or Deactivated";
            }
        } else {
            $errors[] = "Esp Account ID is missing";
        }

        if (isset($deploy['send_date'])) {
            // exclude_days is a 7 char string of Y/N
            $days = DB::select("Select exclude_days from offers where id = :id", ['id' => $deploy['offer_id']])[0];
            // value below is 0-indexed with Sun as 0 and Sat as 6
            $dayOfWeek = Carbon::parse($deploy['send_date'])->dayOfWeek;
            // 'N' means that the offer is not excluded and can be mailed
            if ($days->exclude_days[$dayOfWeek] !== 'N'){
                $errors[] = "Offer cannot be deployed on this day";
            }
        } else {
            $errors[] = "Deploy Date is missing";
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
            $count = DB::select("Select count(*) as count from creatives where id = :id and is_approved = '1' and status = 'A'", ['id' => $deploy['creative_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Creative is not active or wrong";
            }
        } else {
            $errors[] = "Creative ID is missing";
        }
        //from ok?
        if (isset($deploy['from_id'])) {
            $count = DB::select("Select count(*) as count from froms where id = :id and is_approved = 1 and status = 'A'", ['id' => $deploy['from_id']])[0];
            if ($count->count == 0) {
                $errors[] = "From is not active or wrong";
            }
        } else {
            $errors[] = "From  ID is missing";
        }
        //subject ok?
        if (isset($deploy['subject_id'])) {
            $count = DB::select("Select count(*) as count from subjects where id = :id and is_approved = 1 and status = 'A'", ['id' => $deploy['subject_id']])[0];
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
            $count = DB::select("Select count(*) as count from domains where id = :id and domain_type = 1 and status = 1 and live_a_record = 1", ['id' => $deploy['mailing_domain_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Mailing Domain ID is invalid or not Mailing Domain";
            }
        } else {
            $errors[] = "Mailing Domain is missing";
        }

        //content domain
        if (isset($deploy['content_domain_id'])) {
            $count = DB::select("Select count(*) as count from domains where id = :id and domain_type = 2  and status = 1 and live_a_record = 1", ['id' => $deploy['content_domain_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Content Domain is invalid or not content domain";
            } else {
                $errors[] = "Content Domain is missing";
            }
        }

        //list profile for now commented out

        if (isset($deploy['list_profile_combine_id'])) {
        $count = DB::select("Select count(*) as count from list_profile_combines where id = :id", ['id' => $deploy['list_profile_combine_id']])[0];
        if ($count->count == 0) {
        $errors[] = "List Profile is not active or wrong";
        }
        } else {
        $errors[] = "List Profile ID is missing";
        }

        //cake

        if (isset($deploy['cake_affiliate_id'])) {
            $count = DB::connection('mt1_data')->select("Select count(*) as count from EspAdvertiserJoin where affiliateID = :id", ['id' => $deploy['cake_affiliate_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Cake Affiliate is wrong";
            } else {
                $errors[] = "Cake Affiliate ID is missing";
            }
        }

        if (isset($deploy['encrypt_cake'])) {
            if($deploy['encrypt_cake'] != '1' && $deploy['encrypt_cake'] != '0'){
                $errors[] = "Encrypt Cake Value is wrong";
            }
        } else {
            $errors[] = "Encrypt Cake Links options is missing";
        }

        if (isset($deploy['fully_encrypt'])) {
            if($deploy['fully_encrypt'] != '1' && $deploy['fully_encrypt'] != '0'){
                $errors[] = "Full Encrypt Value is wrong";
            }
        } else {
            $errors[] = "Full Encrypt Links options is missing";
        }

        if (isset($deploy['url_format'])) {
            $options = ['new',"old","gmail"];
            if(!in_array($deploy['url_format'],$options)){
                $errors[] = "Url Format is wrong";
            }
        } else {
            $errors[] = "Url Format is missing";
        }

        return $errors;
    }

    public function getOffersForTodayWithListProfile($listProfileId) {
        $today = Carbon::today()->format('Y-m-d');

        return $this->deploy->where('send_date', $today)->where('list_profile_id', $listProfileId)->groupBy('offer_id')->get();
    }

    public function getDeploysForToday($date){
        return $this->deploy->where('send_date',$date)->get();
    }

    public function getDeploysFromProfileAndOffer($listProfileId, $offerId){
        $lpDB = config('database.connections.list_profile.database');
        return $this->deploy->
        join("{$lpDB}.list_profile_list_profile_combine as lplpc","deploys.list_profile_combine_id", "=", "lplpc.list_profile_combine_id")
            ->where("offer_id",$offerId)
            ->where("list_profile_id", $listProfileId)
            ->where("send_date", DB::raw("CURDATE()"))->get();
    }
}
