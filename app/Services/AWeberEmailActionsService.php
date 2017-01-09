<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/6/17
 * Time: 12:41 PM
 */

namespace App\Services;


use App\Repositories\AWeberEmailActionsRepo;

class AWeberEmailActionsService
{

    private $repo;
    const MAX_RECORD_COUNT = 500;
    private $records = [];
    
    public function __construct(AWeberEmailActionsRepo $repo)
    {
        $this->repo = $repo;
    }

    public function queueDeliverable ( $recordType , $email , $espId , $deployId, $espInternalId , $date ) {
            $this->records []= [
                'recordType' => $recordType ,
                'email' => $email ,
                'deployId' => $deployId,
                'espId' => $espId ,
                'espInternalId' => $espInternalId ,
                'date' => $date
            ];

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
}