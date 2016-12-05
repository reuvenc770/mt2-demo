<?php

namespace App\Jobs;

use App\Exceptions\CampaignNameException;
use App\Facades\SlackLevel;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Factories\APIFactory;
use App\Facades\EspApiAccount;
use League\Csv\Reader;

use App\Exceptions\EspAccountDoesNotExistException;

class ImportCsvStats extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    protected $espName;
    protected $filePath;
    CONST SLACK_TARGET ="#mt2team";
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
        $paddedArray = array();
        try {
            $mapping = EspApiAccount::grabCsvMapping($espName);
        } catch (\Exception $e){
            SlackLevel::to(self::SLACK_TARGET)->send("{$espName} does not have ESP Field Maps");
            return $returnArray;
        }
    $reader = Reader::createFromPath($filePath);
        //padding array with fake keys so we can still use Readers helper methods
        foreach ($mapping as $key => $map) {
            for ($i = 0; $i <= 16; $i++) {
                if (isset($paddedArray[$i]) && $paddedArray[$i] != '') {
                    continue;
                }
                else if($i == $map - 1 ){ //UI base is 1
                    $paddedArray[$i] = $key;
                }
                else {
                    $paddedArray[$i] = 'fake'.$i;
                }
            }
        }
    $data = $reader->fetchAssoc($paddedArray);
    foreach ($data as $key => $row) {
        try {
            $row['m_deploy_id'] = $this->getDeployIDFromName($row['campaign_name']);
            $row['esp_internal_id'] = 0;
            $row['external_deploy_id'] = $this->getDeployIDFromName($row['campaign_name']);
            $row['esp_account_id'] = EspApiAccount::getEspAccountIdFromName($row['campaign_name']);
            //undo the fake keys
            for ($i = 0; $i <= 16; $i++) {
                unset($row['fake'.$i]);
            }
            $returnArray[] = $row;

        } catch (CampaignNameException $e){
            SlackLevel::to(self::SLACK_TARGET)->send("Campaign Name cannot be parsed for row {$key} in file {$filePath}");
            continue;
        } catch(EspAccountDoesNotExistException $e){
            SlackLevel::to(self::SLACK_TARGET)->send("Esp Account Listed for row {$key} does not exist in file {$filePath}");
        }
        catch (\Exception $e){
            SlackLevel::to(self::SLACK_TARGET)->send("Something else went wrong with row {$key} - {$e->getMessage()}");
            continue;
        }
    }
    return $returnArray;
    }

    protected function getDeployIDFromName($name){
        try {
            return explode('_', $name)[0];
        } catch (\Exception $e){
            throw new CampaignNameException();
        }
    }
}
