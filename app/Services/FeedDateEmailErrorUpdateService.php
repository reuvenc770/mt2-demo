<?php

namespace App\Services;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Repositories\EtlPickupRepo;
use App\Repositories\EmailDomainRepo;
use Carbon\Carbon;

class FeedDateEmailErrorUpdateService {

    private $rawEmailRepo;
    private $reportRepo;
    private $pickupRepo;
    private $emailDomainRepo;
    const ETL_NAME = 'UpdateFeedProcessingErrors';
    private $data;
    private $startPoint = 0;
    
    public function __construct(RawFeedEmailRepo $rawEmailRepo, 
        FeedDateEmailBreakdownRepo $reportRepo, 
        EtlPickupRepo $pickupRepo,
        EmailDomainRepo $emailDomainRepo) {
        
        $this->rawEmailRepo = $rawEmailRepo;
        $this->reportRepo = $reportRepo;
        $this->pickupRepo = $pickupRepo;
        $this->emailDomainRepo = $emailDomainRepo;
    }

    public function extract($startId) {
        $maxId = $this->rawEmailRepo->getMaxInvalidId();
        $this->data = $this->rawEmailRepo->getInvalidBetweenIds($startId, $maxId);
        $this->startPoint = $startId;
    }

    public function load() {
        $insert = [];
        $position = $this->startPoint;

        foreach($this->data->cursor() as $record) {
            $position = $record->id;
            $date = Carbon::parse($record->created_at)->toDateString();
            $domainGroupInfo = $this->emailDomainRepo->getDomainAndClassInfo($record->email_address);
            $domainGroupId = $domainGroupInfo ? $domainGroupInfo->domain_group_id : 0;
            $errorType = $this->getErrorType($record->errors);
            $filename = $record->realtime === 0 ? $this->stripFile($record->file) : 'Realtime';

            if (!isset($insert[$record->feed_id])) {
                $insert[$record->feed_id] = [];
            }

            if (!isset($insert[$record->feed_id][$domainGroupId])) {
                $insert[$record->feed_id][$domainGroupId] = [];
            }

            if (!isset($insert[$record->feed_id][$domainGroupId][$date][$filename])) {
                $insert[$record->feed_id][$domainGroupId][$date] = [];
            }

            if (!isset($insert[$record->feed_id][$domainGroupId][$date][$filename])) {
                $insert[$record->feed_id][$domainGroupId][$date] = [
                    'bad_source_urls' => 0,
                    'bad_ip_addresses' => 0,
                    'other_invalid' => 0
                ];
            }

            $insert[$record->feed_id][$domainGroupId][$date][$filename][$errorType]++;
        }

        $this->reportRepo->updateRawErrors($insert);
        $this->pickupRepo->updatePosition(self::ETL_NAME, $position);
    }

    private function getErrorType($errors) {
        // $errors is a JSON string
        // If more than one error exists, go with "other"
        $errorAssoc = json_decode($errors, true);

        if ($errorAssoc) {
            if (count($errorAssoc) > 1) {
                return 'other_invalid';
            }
            else {
                if (isset($errorAssoc['ip'])) {
                    return 'bad_ip_addresses';
                }
                else {
                    return 'other_invalid';
                }
            }
        }
        else {
            return 'other_invalid';
        }
    }

    private function stripFile($filePath) {
        // given a filepath like
        // /var/local/programdata/done/mt2_realtime/realtime_dev.aspiremail.mtroute.net_201777174.dat (for realtime)
        // /home/orangegenesis/Zeta Interactive 2017_07_07_085004.csv (for batch)
        $paths = explode('/', $filePath);
        $index = sizeof($paths) - 1;

        if ($index >= 0) {
            return $paths[$index];
        }
        else {
            return '';
        }
    }
}