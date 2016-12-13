<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:59 PM
 */

namespace App\Services;


use App\Repositories\DoingBusinessAsRepo;
use App\Services\ServiceTraits\PaginateList;
use Log;
class DoingBusinessAsService
{
    protected $doingBusinessAsRepo;
    use PaginateList;
    public function __construct(DoingBusinessAsRepo $businessAsRepo)
    {
        $this->doingBusinessAsRepo = $businessAsRepo;

    }

    public function insertRow($request){
        try {
           return $this->doingBusinessAsRepo->insertRow($request);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAll(){
        return $this->doingBusinessAsRepo->getAll();
    }

    public function getAllActive(){
        return $this->doingBusinessAsRepo->getAllActive();
    }

    public function getDBA($id){
        return $this->doingBusinessAsRepo->fetch($id);
    }

    public function updateAccount($id, $accountData){
        return $this->doingBusinessAsRepo->updateAccount( $id , $accountData );
    }

    public function toggleRow($id, $direction){
        return $this->doingBusinessAsRepo->toggleRow($id,$direction);
    }

    public function getModel($searchData = null){
        return $this->doingBusinessAsRepo->getModel($searchData);
    }

    //override return model so its a builder and not Collection
    public function getType(){
        return "DoingBusinessAs";
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

    public function tryToDelete($id){
        $canBeDeleted =  $this->doingBusinessAsRepo->canBeDeleted($id);
        if($canBeDeleted === true){
            $this->doingBusinessAsRepo->delete($id);
            return true;
        } else{
            return $canBeDeleted;
        }
    }
}