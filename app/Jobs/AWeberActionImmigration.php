<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\EmailRecordService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\AWeberEmailAction;
use App\Exceptions\JobException;
use App\Models\ActionType;
use App\Facades\JobTracking;
use App\Models\JobEntry;
class AWeberActionImmigration extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    CONST JOB_NAME = "AWeberActionImmigration";
    private $actionsTypes;
    private $actions;
    private $tracking;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($actions, $tracking)
    {
       $this->actionsTypes = ActionType::all();
        $this->actions = $actions;
        $this->actions = $tracking;
        JobTracking::startAggregationJob( self::JOB_NAME , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailRecordService $recordService)
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $recordsToRemove = [];
        try {
            foreach ($this->actions as $action) {
                $emailAddress = AWeberEmailAction::getEmailAddressById($action->email_id);
                if ($emailAddress) {
                    $recordService->queueDeliverable(
                        $this->getActionName($action->action_id),  //so to fit withen the method of queDeliverable i need to store this the other way
                        $emailAddress,
                        $action->esp_account_id,
                        $action->deploy_id,
                        $action->esp_internal_id,
                        $action->datetime);

                    $recordsToRemove[] = $action->id;
                }
            }
            $recordService->massRecordDeliverables();
            AWeberEmailAction::clearActionsByID($recordsToRemove);

        } catch (\Exception $e) {
            $jobException = new JobException('Failed to save records. ' . $e->getMessage(), JobException::NOTICE, $e);
            $jobException->setDelay(180);
            throw $jobException;
        }
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function getActionName($actionNumber)
    {
        $actionName = false;
        foreach ($this->actionsTypes as $action) {
            if ($action->id == $actionNumber) {
                $actionName = $action->name;
            }
            break;
        }
        return $actionName;
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
