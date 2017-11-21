<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\DeployService;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;
use Maknz\Slack\Facades\Slack;

class SendOpsDeploys extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $tracking;
    protected $deploys;
    protected $username;
    protected $packageService;
    protected $userService;

    CONST JOB_NAME = "SendOpsDeploys";
    const SLACK_CHANNEL = '#cmp_hard_start_errors';

    public function __construct($deploys, $tracking, $username)
    {
        $this->deploys = $deploys;
        $this->tracking = $tracking;
        $this->username = $username;
        JobTracking::startEspJob(self::JOB_NAME . '-' . $deploys,"", "", $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DeployService $service)
    {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);

        $this->packageService = \App::make( \App\Services\PackageZipCreationService::class );
        $this->userService = \App::make( \App\Services\UserService::class );
        foreach(explode( ',' , $this->deploys ) as $deployId) {
            try {
                $this->packageService->uploadPackage($deployId);
            } catch ( \Exception $e ) {
                \Log::error( $e );
                $error = $e->getMessage();

                Slack::to( self::SLACK_CHANNEL )->send( "SendOpsDeploys Error ({$this->username}):\n{$error}" ); 

                $user = $this->userService->findByUsername( $this->username );

                if ( is_null( $user ) ) {
                    Slack::to( self::SLACK_CHANNEL )->send( "SendOpsDeploys Error:\nFailed to find email for user '{$this->username}'. No notification was sent for failed package creation." ); 

                    continue;
                }

                $email = $user->first()->email;

                \Mail::raw( $error , function ( $message ) use ( $email , $deployId ) {
                    $message->to($email);
                    $message->subject("Error Creating Deployment Package - {$deployId}");
                    $message->priority(1);
                });
            }
        }

        $records = $service->getdeployTextDetailsForDeploys($this->deploys);
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->insertOne($service->getHeaderRow());
        $writer->insertAll($records);
        $date = Carbon::today()->toDateString();
        //FTP LOCATION TO BE DETERMINED
        Storage::disk("dataExportFTP")->put("/deploys/{$date}_{$this->username}_{$this->tracking}.csv", $writer->__toString());
        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }

}
