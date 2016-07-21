<?php

namespace App\Jobs;

use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Factories\APIFactory;
use App\Services\SuppressionService;
use App\Repositories\EspApiAccountRepo;

class SharePublicatorsUnsubsJob extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;

    private $tracking;
    private $lookback;
    private $espName = 'Publicators';
    private $espId;
    private $jobName = 'ExportPublicatorsUnsubs';

    public function __construct($espId, $lookback, $tracking) {
        $this->tracking = $tracking;
        $this->lookback = $lookback;
        $this->espId = $espId;
        JobTracking::startEspJob($this->jobName, $this->espName, '', $tracking);
    }

    public function handle(EspApiAccountRepo $accountRepo, SuppressionService $suppressionService) {
        try {
            JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

            $accounts = $accountRepo->getAccountsByESPName($this->espName);
            $emails = $suppressionService->espSuppressionsForDateRange($this->espId, $this->lookback)->toArray();
            $emails = array_map([$this, 'returnEmailAddress'], $emails);            
            $segmentedEmails = array_chunk($emails, 1500);

            foreach ($accounts as $account) {
                echo "for {$account->id}" . PHP_EOL;
                $subscriberService = APIFactory::createApiSubscriptionService($this->espName, $account->id);
                $result = $accountRepo->getPublicatorsSuppressionListId($account->id);
                $listId = $result->suppression_list_id;
                
                foreach ($segmentedEmails as $segment) {
                    // shouldn't break these out into jobs to prevent multiple
                    // simultaneous authorization attempts
                    $subscriberService->uploadEmails($segment, $listId);
                    $subscriberService->exportUnsubs($segment);
                }
                
            }

            JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
        }
        catch (\Exception $e) {
            echo "{$this->jobName} failed with {$e->getMessage()}" . PHP_EOL;
            $this->failed();
        }
    }

    public function failed() {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

    protected function returnEmailAddress($item) {
        return $item['email_address'];
    }

}