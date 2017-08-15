<?php
/*/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/29/16
 * Time: 2:38 PM
 */

namespace App\Services;

use App\Facades\EspApiAccount;
use App\Repositories\CakeAffiliateRepo;
use App\Repositories\DeployRepo;
use App\Repositories\ListProfileCombineRepo;
use App\Repositories\MT1Repositories\EspAdvertiserJoinRepo;
use App\Services\ServiceTraits\PaginateList;
use League\Csv\Writer;
use Log;
use Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DeployService
{
    protected $deployRepo;
    protected $combineRepo;
    protected $cakeAffiliate;
    protected $requiredHeaders = ["send_date", "esp_account_id", "offer_id", "creative_id", "from_id", "subject_id", "template_id", "mailing_domain_id", "content_domain_id", "list_profile_name", "cake_affiliate_id","encrypt_cake", "fully_encrypt", "url_format"];
    use PaginateList , DispatchesJobs;

    public function __construct(DeployRepo $deployRepo, CakeAffiliateRepo $repo, ListProfileCombineRepo $combineRepo)
    {
        $this->deployRepo = $deployRepo;
        $this->cakeAffiliate = $repo;
        $this->combineRepo = $combineRepo;
    }

    public function getCakeAffiliates()
    {
        return $this->cakeAffiliate->getAll();
    }

    public function getModel($searchData = null)
    {

        return $this->deployRepo->getModel($searchData);

    }

    public function insertDeploy($data)
    {
        $deploy = $this->deployRepo->insert($data);

        if ( $deploy ) {
            $this->takeDeploySnapshot( $deploy->id );
        }

        return $deploy;
    }

    protected function takeDeploySnapshot ( $deployId ) {
        $this->dispatch( \App::make( \App\Jobs\DeploySnapshotJob::class , [
            $deployId , 
            str_random( 16 )
        ] ) );
    }

    public function getDeploy($deployId)
    {
        $deploy = $this->deployRepo->getDeploy($deployId);
        $deploy->offer_id = ['id' => $deploy->offer_id, "name" => $deploy->offer_name, "exclude_days" => $deploy->exclude_days];
        unset($deploy->exclude_days);
        unset($deploy->offer_name);
        return $deploy;
    }

    public function updateDeploy($data, $id)
    {
        $this->deployRepo->update($data, $id);
    }

    public function exportCsv($rows)
    {
        $rows = $this->deployRepo->retrieveRowsForCsv($rows);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $schema = $this->deployRepo->returnCsvHeader();

        $writer->insertOne($schema);

        foreach ($rows as $row) {
            $writer->insertOne($row->toArray());
        }
        return $writer->__toString();
    }

    public function validateDeploy($deploy)
    {
        return $this->deployRepo->validateOldDeploy($deploy);
    }

    public function massUpload($deploys)
    {
        $deploysWithProfile = array();
        foreach($deploys as $row){
            $row['id'] = isset( $row['deploy_id'] ) ? $row[ 'deploy_id' ] : null;
            $row['list_profile_combine_id'] = $this->combineRepo->getIdFromName($row['list_profile_name']);
            unset($row['deploy_id']);
            unset($row['valid']);
            unset($row['list_profile_name']);
            $deploysWithProfile[] = $row;
        }
        return $this->deployRepo->massInsert($deploysWithProfile);
    }

    public function deployPackages($data)
    {
        $this->deployRepo->deployPackages($data);
    }

    public function getPaginatedJson($page, $count, $params = null)
    {
        $searchData = null;
        if ($this->hasCache($page, $count, $params)) {
            return $this->getCachedJson($page, $count, $params);
        } else {
            try {

                $searchData = isset($params['data']) ? $params['data'] : null;
                $eloquentObj = $this->getModel($searchData);

                if ( isset( $params['sort'] ) ){
                    $sort = json_decode( $params['sort'] , true );

                    $order = 'asc';

                    if ( isset( $sort[ 'desc' ] ) && $sort[ 'desc' ] === true ) {
                        $order = 'desc';
                    }

                    $eloquentObj = $eloquentObj->orderBy($sort['field'], $order );
                }

                $paginationJSON = $eloquentObj->paginate($count)->toJSON();

                $this->cachePagination(
                    $paginationJSON,
                    $page,
                    $count, $params
                );

                return $paginationJSON;
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function getPendingDeploys()
    {
        return $this->deployRepo->getPendingDeploys();
    }

    public function getdeployTextDetailsForDeploys($deployIds)
    {

        $records = $this->deployRepo->getDeployDetailsByIds($deployIds)->toArray();
        return $records;
    }
    public function getType(){
        return "Deploy";
    }

    public function getHeaderRow()
    {
        return ['Send Date', 'Deploy ID', 'List Profile', 'ESP Account', "Mailing Template", "Mailing Domain",
            "Content Domain", "Subject", "From", "Creative"];
    }

    public function copyToFutureDate($data){
        $errorCollection = array();
        foreach($data['deploy_ids'] as $deployId){
          $newDeploy = $this->deployRepo->duplicateDomainToDate($deployId, $data['future_date']);
            $copyToFutureBool = true; //this lets the validator know I am passing a list profile id vs name
            $errors = $this->deployRepo->validateDeploy($newDeploy->toArray(),$copyToFutureBool);
            if(count($errors) == 0){
                $newDeploy->save();
            }
            else {
                $errors['deploy_id'] = $deployId;
                $errorCollection[] = $errors;
            }
        }
        return $errorCollection;
    }

    public function getOrphanDeploysForEsp($espName){

        $ids = collect(EspApiAccount::getAllAccountsByESPName($espName))->pluck('id');
        return $this->deployRepo->findReportOrphansForEspAccounts($ids);
    }

    public function getDeployParty($id) {
        return $this->deployRepo->getDeployParty($id);
    }

    public function getFeedIdsInDeploy($deployId) {
        return $this->deployRepo->getFeedIdsInDeploy($deployId);
    }

    public function getMissingHeaders($headers){
        return array_diff($this->requiredHeaders, $headers);
    }

    public function returnCsvHeader(){
        return $this->deployRepo->returnCsvHeader();
    }
}
