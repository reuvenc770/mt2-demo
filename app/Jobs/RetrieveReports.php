<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Factories\APIFactory;

class RetrieveReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $apiName;
    protected $accountNumber;
    protected $date;
    /**
     * Create a new job instance.
     * This is where we would inject account number
     *
     * @return void
     */
    public function __construct($apiName, $accountNumber, $date)
    {
       $this->apiName = $apiName;
       $this->accountNumber = $accountNumber;
       $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportService = APIFactory::createAPIReportService($this->apiName,$this->accountNumber);
        $xmlBody = $reportService->retrieveReportStats($this->date);
        $reportService->insertRawStats($xmlBody);
    }
}
