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
use App\Repositories\RepoInterfaces\Mt2Export;
use App\Models\Offer;

# Dependencies for testing
use App;
use App\Repositories\EtlPickupRepo;

class DeployRepo implements Mt2Export
{
    protected $deploy;
    protected $offer;

    public function __construct(Deploy $deploy , Offer $offer)
    {
        $this->deploy = $deploy;
        $this->offer = $offer;
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
                'notes')
            ->where('deploys.mailing_domain_id','<>',0)
            ->where('deploys.content_domain_id','<>',0);

        if('' !== $searchData) {
            $query = $this->mapQuery($searchData, $query);
        }
        return $query;
    }

    public function insert($data)
    {
        # TODO: setting some default values for now due to strict mode
        $data['send_date'] = Carbon::parse($data['send_date'])->toDateString();
        $data['deploy_name'] = '';
        $data['external_deploy_id'] = '';
        $data['deployment_status'] = 0;
        $obj = $this->deploy->create($data);
        $deployName = $obj->createDeployName();
        $obj->deploy_name = $deployName;
        $obj->save();
        return $obj;
    }

    public function getDeploy($id)
    {
        return $this->deploy->leftJoin('offers', 'offers.id', '=', 'deploys.offer_id')
            ->select(['deploys.*', 'offers.name as offer_name', 'offers.exclude_days'])
            ->where('deploys.id', $id)->first();
    }

    public function updateOrCreate($data)
    {
        $data['send_date'] = Carbon::parse($data['send_date'])->toDateString();
        $this->deploy->updateOrCreate(['id' => $data['id']], $data);
    }

    public function prepareTableForSync() {}

    public function update($data, $id)
    {
        $data['send_date'] = Carbon::parse($data['send_date'])->toDateString();
        return $this->deploy->find($id)->update($data);
    }

    public function retrieveRowsForCsv($rows)
    {
        $listProfileSchema = config('database.connections.list_profile.database');
        return $this->deploy->whereIn('deploys.id', $rows)
            ->select("deploys.id as deploy_id",
                "send_date",
                "esp_account_id",
                "offer_id",
                "creative_id",
                "from_id",
                "subject_id",
                "template_id",
                "mailing_domain_id",
                "content_domain_id",
                "lpc.name as list_profile_name",
                "cake_affiliate_id",
                "encrypt_cake",
                "fully_encrypt",
                "url_format",
                "notes"
            )->join("{$listProfileSchema}.list_profile_combines as lpc", 'deploys.list_profile_combine_id', '=', 'lpc.id')->get();
    }

    //Rob will probably say oh we can just do this, but I am not rob and I suck at sql
    public function validateOldDeploy($deploy)
    {
        $errors = array();
        //deploy_id
        if(isset($deploy['deploy_id']) && $deploy['deploy_id'] != 0) {
            if (isset($deploy['deploy_id'])) {
                $count = DB::select("Select count(*) as count from deploys where id = :id", ['id' => $deploy['deploy_id']])[0];
                if ($count->count == 0) {
                    $errors[] = "Deploy ID is not Valid.";
                }
            } else {
                $errors[] = "Deploy ID is missing";
            }
        }

        $otherErrors = $this->validateDeploy($deploy,false);
        return array_merge($errors,$otherErrors);


    }

    public function massInsert($data){
        foreach($data as $row){
            # TODO: temp fix for getting around strict mode
            $row['send_date'] = Carbon::parse($row['send_date'])->toDateString();
            $row['deploy_name'] = '';
            $row['external_deploy_id'] = '';
            $row['deployment_status'] = 0;
            $obj = $this->deploy->updateOrCreate(['id'=> $row['id']],$row);

            $deployName = $obj->createDeployName();
            $obj->deploy_name = $deployName;
            $obj->save();
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
            "mailing_domain_id", "content_domain_id", "list_profile_name", "cake_affiliate_id","encrypt_cake", "fully_encrypt", "url_format", "notes"];
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

        if (isset($searchData['listProfileParty'])) {
            $query->where('deploys.party' , $searchData['listProfileParty'] );
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
            ->leftJoin('list_profile.list_profile_combines as lpc', 'lpc.id', '=', 'deploys.list_profile_combine_id')
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

    public function validateDeploy($deploy,$copyToFutureBool = true){
        $errors = array();
        if (isset($deploy['esp_account_id']) && $deploy['esp_account_id'] !=='' ) {
            $count = DB::select("Select count(*) as count from esp_accounts where id = :id and enable_stats = 1", ['id' => $deploy['esp_account_id']])[0];
            if ($count->count == 0) {
                $errors[] = "ESP Account ID/Name is invalid or deactivated";
            }
        } else {
            $errors[] = "ESP Account ID is missing";
        }

        if (isset($deploy['send_date']) && $deploy['send_date'] !== '' ) {
            // exclude_days is a 7 char string of Y/N
            $result = $this->offer->where('id', $deploy['offer_id'] );
            $days = $result->count() > 0 ? $result->first() : null;

            try {
                // value below is 0-indexed with Monday as 0 and Sunday as 6
                $dayOfWeek = date('N', strtotime($deploy['send_date']) ) - 1;
            } catch ( \Exception $e) {
                $errors[] = "Send date is invalid.";
            }

            if ( is_null($days) ){
                $errors[] = "No offer or exclude days specified";
            }
            // 'N' means that the offer is not excluded and can be mailed
            elseif ($days->exclude_days[$dayOfWeek] !== 'N'){
                $errors[] = "Offer cannot be deployed on this day";
            }
        } else {
            $errors[] = "Deploy Date is missing";
        }
        //offer real?
        if (isset($deploy['offer_id']) && $deploy['offer_id'] !== '' ) {
            $count = DB::select("Select count(*) as count from offers where id = :id", ['id' => $deploy['offer_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Offer ID is invalid";
            }
        } else {
            $errors[] = "Offer ID is missing";
        }
        //creative ok?
        if (isset($deploy['creative_id']) && $deploy['creative_id'] !== '') {
            $count = DB::select("Select count(*) as count from creatives where id = :id and is_approved = '1' and status = 'A'", ['id' => $deploy['creative_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Creative is not active or ID is incorrect";
            }
        } else {
            $errors[] = "Creative ID is missing";
        }
        //from ok?
        if (isset($deploy['from_id']) && $deploy['from_id'] !=='' ) {
            $count = DB::select("Select count(*) as count from froms where id = :id and is_approved = 1 and status = 'A'", ['id' => $deploy['from_id']])[0];
            if ($count->count == 0) {
                $errors[] = "From is not active or ID is incorrect";
            }
        } else {
            $errors[] = "From ID is missing";
        }
        //subject ok?
        if (isset($deploy['subject_id']) && $deploy['subject_id']!== '' ) {
            $reportSchema = config( 'database.connections.reporting_data.database' );
            $count = DB::select(
                "SELECT count(*) AS count FROM subjects s INNER JOIN {$reportSchema}.offer_subject_maps osm ON( s.id = osm.subject_id ) WHERE s.id = :id AND s.is_approved = 1 AND s.status = 'A' AND osm.offer_id = :offerid",
                [
                    'id' => $deploy['subject_id'] ,
                    'offerid' => $deploy[ 'offer_id' ]
                ]
            )[0];
            
            if ($count->count == 0) {
                $errors[] = "Subject is not active or ID is incorrect";
            }
        } else {
            $errors[] = "Subject ID is missing";
        }
        //template_ok
        if (isset($deploy['template_id']) && $deploy['template_id'] !== '' ) {
            $count = DB::select("Select count(*) as count from mailing_templates where id = :id", ['id' => $deploy['template_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Template ID/Name is not active or incorrect";
            }
        } else {
            $errors[] = "Template ID is missing";
        }
        //mailing domain
        if (isset($deploy['mailing_domain_id']) && $deploy['mailing_domain_id'] !=='' ) {
            $count = DB::select("Select count(*) as count from domains where id = :id and domain_type = 1 and status = 1 and live_a_record = 1", ['id' => $deploy['mailing_domain_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Mailing Domain is invalid or not a mailing domain";
            }
        } else {
            $errors[] = "Mailing Domain is missing";
        }

        //content domain
        if (isset($deploy['content_domain_id']) && $deploy['content_domain_id'] !== '' ) {
            $count = DB::select("Select count(*) as count from domains where id = :id and domain_type = 2  and status = 1 and live_a_record = 1", ['id' => $deploy['content_domain_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Content Domain is invalid or not a content domain";
            }
        } else {
            $errors[] = "Content Domain is missing";
        }

        //list profile for now commented out
        if($copyToFutureBool){
            $lpSchema = config('database.connections.list_profile.database');

            if (isset($deploy['list_profile_combine_id'])) {
                $count = DB::select("Select count(*) as count from $lpSchema.list_profile_combines where id = :id", ['id' => $deploy['list_profile_combine_id']])[0];
                if ($count->count == 0) {
                    $errors[] = "List Profile is not active or ID is incorrect";
                }
            } else {
                $errors[] = "List Profile is missing";
            }
        } else{
            $lpSchema = config('database.connections.list_profile.database');

            if (isset($deploy['list_profile_name'])) {
                $count = DB::select("Select count(*) as count from $lpSchema.list_profile_combines where name = :name", ['name' => $deploy['list_profile_name']])[0];
                if ($count->count == 0) {
                    $errors[] = "List Profile is not active or wrong";
                }
            } else {
                $errors[] = "List Profile Name is missing";
            }
        }

        //cake

        if (isset($deploy['cake_affiliate_id']) && $deploy['cake_affiliate_id'] !=='' ) {
            $count = DB::connection('mt1_data')->select("Select count(*) as count from EspAdvertiserJoin where affiliateID = :id", ['id' => $deploy['cake_affiliate_id']])[0];
            if ($count->count == 0) {
                $errors[] = "Cake Affiliate is incorrect";
            }
        } else {
            $errors[] = "Cake Affiliate ID is missing";
        }

        if (isset($deploy['encrypt_cake']) && $deploy['encrypt_cake'] !== '' ) {
            if($deploy['encrypt_cake'] != '1' && $deploy['encrypt_cake'] != '0'){
                $errors[] = "Encrypt Cake value is incorrect";
            }
        } else {
            $errors[] = "Encrypt Cake Links options is missing";
        }

        if (isset($deploy['fully_encrypt']) && $deploy['fully_encrypt'] !== '') {
            if($deploy['fully_encrypt'] != '1' && $deploy['fully_encrypt'] != '0'){
                $errors[] = "Full Encrypt value is incorrect";
            }
        } else {
            $errors[] = "Full Encrypt Links options is missing";
        }

        if (isset($deploy['url_format']) && $deploy['url_format'] !== '' ) {
            $options = ['long',"short","encrypt"];
            if(!in_array($deploy['url_format'],$options)){
                $errors[] = "Url Format is incorrect";
            }
        } else {
            $errors[] = "Url Format is missing";
        }

        return $errors;
    }


    public function getDeploysForToday($date){
        return $this->deploy->where('send_date',$date)->whereRaw("id > 2000000")->get();
    }
    //TODO: maybe move..  Seems
    public function getDeploysFromProfileAndOffer($listProfileId, $offerId){
        $lpDB = config('database.connections.list_profile.database');
        return $this->deploy->
        join("{$lpDB}.list_profile_list_profile_combine as lplpc","deploys.list_profile_combine_id", "=", "lplpc.list_profile_combine_id")
            ->where("offer_id",$offerId)
            ->where("list_profile_id", $listProfileId)->get();
    }


    public function getUpdatedFrom($date) {
        return $this->deploy
            ->select('id', 'creative_id', 'subject_id', 'from_id', 'list_profile_combine_id')
            ->where('updated_at', '>=', $date)
            ->get();
    }

    public function transformForMt1($startingId) {
        return $this->deploy
            ->selectRaw("id as tracking_id, id as subAffiliateID, offer_id as advertiserID, 0 as espID, creative_id as creativeID, subject_id as subjectID, send_date as sendDate, cake_affiliate_id as affiliateID, updated_at as lastUpdated")
            ->where('id', '>=', $startingId);
    }

    public function findReportOrphansForEspAccounts($ids){
        $reportSchema = config('database.connections.reporting_data.database');
        return $this->deploy
            ->select("deploys.*", 'subjects.subject_line')
            ->with('espAccount')
            ->leftJoin("{$reportSchema}.standard_reports", 'deploys.id', '=', 'standard_reports.external_deploy_id')
            ->leftJoin("subjects", 'deploys.subject_id', '=', 'subjects.id')
            ->whereIn('deploys.esp_account_id',$ids)
            ->where('standard_reports.external_deploy_id',null)->get();
    }

    public function getDeployParty($id) {
        $deploy = $this->deploy->where('id', $id)->first();

        if ($deploy) {
            return $deploy->party;
        }
        else {
            return null;
        }
    }

    public function getFeedIdsInDeploy($deployId) {
        // Returns an array of stdClass objects with the property feed_id
        $lpSchema = config('database.connections.list_profile.database');

        return DB::select("SELECT
            DISTINCT feed_id
        FROM
            deploys d
            INNER JOIN $lpSchema.list_profile_list_profile_combine lplpc ON d.list_profile_combine_id = lplpc.list_profile_combine_id
            INNER JOIN $lpSchema.list_profile_feeds lpf ON lplpc.list_profile_combine_id = lpf.list_profile_id
        WHERE
            d.id = ?", [$deployId]);
    }

    public function getCakeVerticalId($deployId) {
        $cakeOffers = $this->deploy->find($deployId)->offer->cakeOffers->first();
        if ($cakeOffers) {
            $cakeOffer = $cakeOffers->first();
            if ($cakeOffer) {
                return $cakeOffer->vertical_id;
            }
        }

        return 0;
    }

}
