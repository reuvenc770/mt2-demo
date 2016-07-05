<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/5/16
 * Time: 4:07 PM
 */

namespace App\Services;


use App\Models\AttributionRecordTruth;
use App\Repositories\AttributionRecordTruthRepo;
use Log;

class AttributionRecordTruthService
{
    private $recordTruthRepo;

    public function __construct(AttributionRecordTruthRepo $truthRepo)
    {
        $this->recordTruthRepo = $truthRepo;
    }

    public function expireRecord($emailId)
    {
        try {
            $this->recordTruthRepo->setField($emailId, AttributionRecordTruth::EXPIRE_COL, false);
        } catch (\Exception $e) {
            Log::error("Could not Expire Record because {$e->getMessage()}");
        }

    }

    public function deactivateRecord($emailId)
    {
        try {
            $this->recordTruthRepo->setField($emailId, AttributionRecordTruth::ACTIVE_COL, false);
        } catch (\Exception $e) {
            Log::error("Could not Expire Record because {$e->getMessage()}");
        }
    }

    public function toggleFieldRecord($emailId, $column, $value)
    {
        try {
            $this->recordTruthRepo->setField($emailId, $column, $value);
        } catch (\Exception $e) {
            Log::error("Could not Expire Record because {$e->getMessage()}");
        }
    }
}