<?php

namespace App\Jobs;

use App\Services\AbstractReportService;
use App\Services\AttributionRecordTruthService;
use App\Services\SeedEmailService;
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
use App\Services\EmailFeedActionService;
use Maknz\Slack\Facades\Slack;

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
    public function __construct($orphans, $tracking)
    {
        $this->orphans = $orphans;

        $this->tracking = $tracking;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttributionRecordTruthService $truthService, EmailFeedActionService $actionService, SeedEmailService $seedService) {

        JobTracking::startEspJob( 'Orphan Adoption: ' . "Chunk" , null , null , $this->tracking );
        $inserts = [];
        $deleteIds = [];
        $actionsRecords = [];
        $reports = array_fill_keys(["deploy_missing","email_missing"],[]);
        foreach ($this->orphans as $orphan) {

            //If Email is a Seed delete it and move on.
            if($seedService->checkForSeed($orphan->email_address)){
                $deleteIds[] = $orphan->id;
                continue;
            }

            $currentEmailId = 0;

            $emailRecordCount = Email::where( 'email_address' , $orphan->email_address )->count();

                $deployId = (int)$orphan->deploy_id;

                $found = (int)StandardReport::where('esp_internal_id', $orphan->esp_internal_id)->pluck('external_deploy_id')->first();

                if (0 === $deployId && 0 !== $found) {
                    $deployId = $found;
                }

                if ( $emailRecordCount > 0 && $deployId > 0) {
                    echo "I GOT HERE";
                    $currentEmailId = Email::select('id')->where('email_address', $orphan->email_address)->pluck('id')->first();

                    $value = "('$currentEmailId', 
                        '{$orphan->esp_account_id}', 
                        '{$deployId}', 
                        '{$orphan->esp_internal_id}', 
                        '{$orphan->action_id}', 
                        '{$orphan->datetime}', NOW(), NOW())";
                    $inserts[] = $value;

                    $deleteIds[] = $orphan->id;
                }
                else {
                    $date = Carbon::today()->subDay(5)->toDateString();
                    if($deployId == 0 && $orphan->create_date <= $date){
                        $reports['deploy_missing'][] = ["esp_account" => $orphan->esp_account_id, "esp_internal_id" => $orphan->esp_intenral_id];
                    } elseif ($emailRecordCount  == 0  && $orphan->create_date <= $date){
                        $reports['email_missing'][] = $orphan->email_address;
                    }
                }


                if($currentEmailId > 0 && $emailRecordCount > 0){
                    if ($orphan->action_id == AbstractReportService::RECORD_TYPE_CLICKER ||
                        $orphan->action_id == AbstractReportService::RECORD_TYPE_OPENER) {
                        $actionsRecords[] = ["email_id" =>$currentEmailId, "type" => $orphan->action_id];
                    }
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
                    $emails = collect($actionsRecords)->pluck("email_id")->all();
                    $truthService->bulkToggleFieldRecord($emails, "has_action", true);
                    $actionService->bulkUpdate($actionsRecords);
                }

            }
            if(sizeof($deleteIds) > 0) {
                DB::table('orphan_emails')
                    ->whereIn('id', $deleteIds)
                    ->delete();
            }

            $deployCount = count($reports['deploy_missing']);
            if($deployCount > 0 ){
                Slack::to(self::SLACK_TARGET_SUBJECT)->send("{$deployCount} actions cannot be attached to a deploy for over 5 days");
            }
            $emailMissing = count($reports['email_missing']);
            if($emailMissing > 0){
                Slack::to(self::SLACK_TARGET_SUBJECT)->send("{$emailMissing} actions do not have an EID for over 5 days");
            }

        }
        catch ( \Exception $e ) {
            Log::error( 'Query errors:' );
            Log::error( $e->getMessage() );
            Log::error( $e->getTraceAsString() );

        }
        $this->changeJobEntry( JobEntry::SUCCESS );
    }


    protected function changeJobEntry ( $status ) {
        JobTracking::changeJobState( $status , $this->tracking);
    }

    public function failed() {
        $this->changeJobEntry(JobEntry::FAILED);
    }
}
