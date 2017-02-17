<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/6/17
 * Time: 12:41 PM
 */

namespace App\Services;


use App\Repositories\AWeberEmailActionsRepo;
use App\Repositories\AWeberSubscriberRepo;

class AWeberEmailActionsService
{

    private $repo;
    protected $subRepo;
    const MAX_RECORD_COUNT = 500;
    private $records = [];
    
    public function __construct(AWeberEmailActionsRepo $repo, AWeberSubscriberRepo $subscriberRepo)
    {
        $this->repo = $repo;
        $this->subRepo = $subscriberRepo;
    }

    public function queueDeliverable ( $recordType , $email , $espId , $deployId, $espInternalId , $date ) {
        if($this->getEmailId($email) != "one") { //aweber sometimes will have an action that does not have a user
            $this->records [] = [
                'recordType' => $recordType,
                'email' => $this->getEmailId($email),
                'deployId' => $deployId,
                'espId' => $espId,
                'espInternalId' => $espInternalId,
                'date' => $date
            ];
        }
            if (self::MAX_RECORD_COUNT <= sizeof($this->records)) {
                $this->massRecordDeliverables();
            }

    }

    public function massRecordDeliverables () {
        $count = count($this->records);
        try {
            $this->repo->massRecordDeliverables($this->records);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $this->records = []; // clear out to free up space
        }

        return $count;
    }
    
    public function clearActionsByID($ids){
        return $this->repo->massDelete($ids);
    }

    public function getEmailId($fullUrl){
        return substr($fullUrl, strrpos($fullUrl, '/') + 1);
    }
    
    public function getEmailAddressFromUrl($url){
        $internalId = $this->getEmailId($url);
      return $this->subRepo->getByInternalId($internalId);
    }

    public function getEmailAddressById($id)
    {
        return $this->subRepo->getByInternalId($id);
    }

}