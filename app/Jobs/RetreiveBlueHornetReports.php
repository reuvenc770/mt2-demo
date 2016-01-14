<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\BlueHornetService;
use Illuminate\Support\Facades\Log;

class RetreiveBlueHornetReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $bhAccount;
    protected $date;
    /**
     * Create a new job instance.
     * This is where we would inject account number
     *
     * @return void
     */
    public function __construct($BHAccountNumber = null, $date)
    {
       $this->bhAccount = $BHAccountNumber;
       $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BlueHornetService $bhService)
    {
        //Call method to retrieve Creds
        Log::info($bhService->retrieveReportStats($this->date));

    }
}
