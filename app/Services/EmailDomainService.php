<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 9/29/16
 * Time: 3:30 PM
 */

namespace App\Services;


use App\Repositories\EmailDomainRepo;
use App\Services\ServiceTraits\PaginateList;
use App\DataModels\ProcessingRecord; 

class EmailDomainService
{
    use PaginateList;
    protected $emailDomainRepo;

    public function __construct(EmailDomainRepo $emailDomainRepo)
    {
        $this->emailDomainRepo = $emailDomainRepo;
    }

    public function getModel($searchData = null){
        return $this->emailDomainRepo->getModel($searchData);
    }


    public function getType(){
        return "EmailDomain";
    }

    public function getEmailDomainById($id){
        return $this->emailDomainRepo->getRow($id);
    }

    public function insertDomain($request){

        return $this->emailDomainRepo->insertRow($request);

    }

    public function updateDomain($id, $groupData){
        return $this->emailDomainRepo->updateRow($id, $groupData);
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
                    $count,
                    $params
                );

                return $paginationJSON;
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
                return false;
            }
        }
    }

    public function setRecordDomainInfo(ProcessingRecord $record) {
        if ($record->newEmail && preg_match('/@/', $record->emailAddress)) {
            // Need to set these values for validation
            $record->domainId = $this->emailDomainRepo->getIdForName($record->emailAddress);
            $result = $this->emailDomainRepo->getDomainAndClassInfo($record->emailAddress);
            $record->domainGroupId = $result->domain_group_id;
        }
        elseif ($record->newEmail) {
            $record->domainGroupId = 0; // These are totally invalid records.
        }

        return $record;
    }
}