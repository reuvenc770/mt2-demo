<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:59 PM
 */

namespace App\Services;


use App\Repositories\DoingBusinessAsRepo;
use Log;
class DoingBusinessAsService
{
    protected $doingBusinessAsRepo;

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
        return $this->doingBusinessAsRepo->getAll();
    }

    public function getDBA($id){
        return $this->doingBusinessAsRepo->fetch($id);
    }

    public function updateAccount($id, $accountData){
        return $this->doingBusinessAsRepo->updateAccount( $id , $accountData );
    }
}