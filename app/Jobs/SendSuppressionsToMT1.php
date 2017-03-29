<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\SuppressionService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use App\Models\JobEntry;
use File;
use PDO;
use DB;

class SendSuppressionsToMT1 extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $tracking;
    protected $date;
    CONST JOB_NAME = "FTPSuppressionsToMT1";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date, $tracking)
    {
        $this->date = $date;
        $this->tracking = $tracking;
        JobTracking::startEspJob(self::JOB_NAME,"", "", $this->tracking);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SuppressionService $service) {
        JobTracking::changeJobState(JobEntry::RUNNING,$this->tracking);
        $filePath = "/MT2/{$this->date}-{$this->tracking}.csv";

        $pdo = DB::connection()->getPdo();
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $query = $service->getAllSuppressionsSinceDate($this->date);

        $statement = $pdo->prepare($query->toSql());
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_OBJ)) {
            File::disk('MT1SuppressionDropOff')->append($filePath, $row->email_address . PHP_EOL);
        }

        JobTracking::changeJobState(JobEntry::SUCCESS,$this->tracking);
    }

    public function failed()
    {
        JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
    }
}
