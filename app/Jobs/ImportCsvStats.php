<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\StandardReportService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Factories\APIFactory;
use App\Facades\EspApiAccount;
use League\Csv\Reader;
use Storage;
class ImportCsvStats extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $espName;
    protected $filePath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($espName, $filePath)
    {
        $this->espName = $espName;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportService = APIFactory::createSimpleStandardReportService();
        $reportArray = $this->mapCsvToRawStatsArray($this->espName, $this->filePath);
        foreach($reportArray as $report){
            $reportService->insertStandardStats($report);
        }
    }

    private function mapCsvToRawStatsArray($espName,$filePath) {
    $returnArray = array();
    $mapping = EspApiAccount::grabCsvMapping($espName);
    $reader = Reader::createFromPath($filePath);
    $data = $reader->fetchAssoc(explode(',',$mapping));
    foreach ($data as $row) {
        $row['m_deploy_id'] = $this->getDeployIDFromName($row['campaign_name']);
        $row['esp_internal_id'] = 0;
        $row['external_deploy_id'] = $this->getDeployIDFromName($row['campaign_name']);
        $row['esp_account_id'] = EspApiAccount::getEspAccountIdFromName($row['campaign_name']);
        $returnArray[] = $row;
    }
    return $returnArray;
    }

    protected function getDeployIDFromName($name){
        return explode('_',$name)[0];
    }
}
