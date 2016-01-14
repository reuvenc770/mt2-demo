<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Factories\APIFactory;

class RetrieveReports extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $name;
    protected $accountNumber;
    protected $date;
    /**
     * Create a new job instance.
     * This is where we would inject account number
     *
     * @return void
     */
    public function __construct($name, $accountNumber, $date)
    {
       $this->name = $name;
       $this->accountNumber = $accountNumber;
       $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(APIFactory $APIFactory)
    {
        $BHService = $APIFactory->createAPIReportService($this->name,$this->accountNumber);
        $xmlBody = $BHService->retrieveReportStats($this->date);
        $BHService->insertRawStats($xmlBody);





    }
}
