<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Repositories\EtlPickupRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Factories\APIFactory;
use App\Repositories\EspApiAccount;


class ExportRecordsJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    private $tracking;
    private $feedId;
    const BASE_NAME = 'FeedExport';

    public function __construct($feedId, $tracking) {
        $this->feedId = $feedId;
        $this->tracking = $tracking;
        $this->jobName = self::BASE_NAME . "-{$this->feedId}";
        JobTracking::startAggregationJob($this->jobName, $this->tracking);
    }

    public function handle(EmailFeedInstanceRepo $instancesRepo, 
        EtlPickupRepo $pickupRepo,
        EspDataExportRepo $dataExportRepo,
        EspApiAccountRepo $espAccountRepo) {

        if ($this->jobCanRun($this->jobName)) {
            try {
                $this->createLock($this->jobName);
                JobTracking::changeJobState(JobEntry::RUNNING, $this->tracking);
                echo "{$this->jobName} running" . PHP_EOL;

                $exportInfoResult = $dataExportRepo->getForFeedId($this->feedId);

                // The outer loop should only consist of a single item, but just to be sure ...
                foreach ($exportInfoResult as $info) {
                    $espName = $espAccountRepo->getAccount($info->esp_account_id)->account_name;

                    $apiService = APIFactory::createApiReportService($espName, $info->esp_account_id);
                    $startPoint = $pickupRepo->getLastInsertedForName($this->jobName);
                    $listId = $info->target_list;
                    $records = $instancesRepo->getRecordsFromFeedStartingAt($this->feedId, $startPoint);

                    foreach ($records->cursor() as $record) {
                        // export here
                        $id = $record->id;
                        $apiService->addContact($record, $listId);
                    }
                }

                $pickupRepo->updatePosition($this->jobName, $id);
                JobTracking::changeJobState(JobEntry::SUCCESS, $this->tracking);
            }
            catch (\Exception $e) {
                echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
                $this->failed();
            }
            finally {
                $this->unlock($this->jobName);
            }
        }
        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
            JobTracking::changeJobState(JobEntry::SKIPPED, $this->tracking);
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

}