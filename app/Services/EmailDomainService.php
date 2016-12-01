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
                Log::error($e->getMessage());
                return false;
            }
        }
    }
}