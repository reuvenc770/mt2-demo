<?php

namespace App\Jobs;

use App\Services\AbstractReportService;
use App\Services\AttributionRecordTruthService;
use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use DB;
use App\Factories\ServiceFactory;
use App\Models\JobEntry;
use App\Models\Email;
use App\Models\EmailFeedInstance;
use App\Models\OrphanEmail;
use App\Facades\JobTracking;
use App\Models\StandardReport;

class AdoptOrphanEmails extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $orphans;
    protected $tracking;
    protected $firstId;
    protected $lastId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $orphans = [] , $firstId = 0 , $lastId = 0 , $tracking)
    {
        $this->orphans = is_array( $orphans ) ? collect( $orphans ) : $orphans;

        $this->tracking = $tracking;

        $this->firstId = $firstId;
        $this->lastId = $lastId;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttributionRecordTruthService $truthService) {

        JobTracking::startEspJob( 'Orphan Adoption: ' . $this->firstId . '-' . $this->lastId , null , null , $this->tracking );
        $attempts = 0;
        $processed = 0;

        $inserts = [];
        $deleteIds = [];
        $actionsRecords = [];
        foreach ($this->orphans as $orphan) {
            $orphan = OrphanEmail::find( $orphan->id );
            $currentEmailId = 0;
            $failedToProcess = false;

            if ( is_object( $orphan ) ) {

                $emailRecordCount = Email::where( 'email_address' , $orphan->email_address )->count();
                $deployId = (int)$orphan->deploy_id;
                $found = (int)StandardReport::where('esp_internal_id', $orphan->esp_internal_id)->pluck('external_deploy_id')->first();

                if (0 === $deployId && 0 !== $found) {
                    $deployId = $found;
                }

                if ( $emailRecordCount > 0 && $deployId > 0) {
                    $currentEmailId = Email::select('id')->where('email_address', $orphan->email_address)->pluck('id')->first();

                    $value = "('$currentEmailId', 
                        '{$orphan->esp_account_id}', 
                        '{$deployId}', 
                        '{$orphan->esp_internal_id}', 
                        '{$orphan->action_id}', 
                        '{$orphan->datetime}', NOW(), NOW())";
                    $inserts[] = $value;

                    $deleteIds[] = $orphan->id;
                    $processed++;

                    if ($orphan->action_id == AbstractReportService::RECORD_TYPE_CLICKER ||
                        $orphan->action_id == AbstractReportService::RECORD_TYPE_OPENER) {
                            $actionsRecords[] = ["email_id" =>$currentEmailId, "datetime" => $orphan->datetime];
                    }
                } 
                else {
                    Log::emergency("Orphan failed to be adopted ", array(
                                                                    "email" => $orphan->email_address,
                                                                    "esp_account_id" => $orphan->esp_account_id,
                                                                    "deploy_id" => $orphan->deploy_id,
                                                                    "esp_internal_id" => $orphan->esp_internal_id,
                                                            )
                    );
                    $failedToProcess = true;
                    $attempts++;
                }


                if($currentEmailId > 0 && $emailRecordCount > 0){
                    if ($orphan->action_id == AbstractReportService::RECORD_TYPE_CLICKER ||
                        $orphan->action_id == AbstractReportService::RECORD_TYPE_OPENER) {
                        $actionsRecords[] = ["email_id" =>$currentEmailId, "datetime" => $orphan->datetime];
                    }
                }
            }

            if ( $failedToProcess ) {
                $orphan->increment( 'adopt_attempts' );
            }
        }


        try {
            if (sizeof($inserts) > 0) {
                $insertString = implode(',', $inserts);
                DB::connection( 'reporting_data' )->statement("
                    INSERT INTO email_actions
                        ( email_id , esp_account_id , deploy_id, esp_internal_id , action_id , datetime , created_at , updated_at )    
                        VALUES
                        $insertString
                        ON DUPLICATE KEY UPDATE
                        email_id = email_id ,
                        esp_account_id = esp_account_id ,
                        deploy_id = deploy_id,
                        esp_internal_id = esp_internal_id ,
                        action_id = action_id ,
                        datetime = datetime ,
                        created_at = created_at ,
                        updated_at = NOW()" 
                );

                if(count($actionsRecords) > 0) {
                    $scheduledFilterService = ServiceFactory::createFilterService("activity");

                        $emails = collect($actionsRecords)->pluck("email_id")->all();
                        $truthService->bulkToggleFieldRecord($emails, "has_action", true);

                    $scheduledFilterService->insertScheduleFilterBulk($actionsRecords, 90);
                }

                DB::table('orphan_emails')
                    ->whereIn('id', $deleteIds)
                    ->delete();
            }

        }
        catch ( Exception $e ) {
            Log::error( 'Query errors:' );
            Log::error( $e->getMessage() );
            Log::error( $e->getTraceAsString() );

            $failedToProcess = true;
            $attempts++;
        }

        Log::info( 'Successfully Processed ' . $processed . ' Orphan Emails.' );
        Log::info( 'Failed Processing ' . $attempts . ' Orphan Emails.' );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function initJobEntry () {
        JobTracking::startEspJob( 'Orphan Adoption: ' . $this->firstId . '-' . $this->lastId , null , null , $this->tracking );
    }

    protected function changeJobEntry ( $status ) {
        JobTracking::changeJobState( $status , $this->tracking);
    }

    public function failed() {
        $this->changeJobEntry(JobEntry::FAILED);
    }
}
