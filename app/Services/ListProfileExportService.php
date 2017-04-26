<?php

namespace App\Services;

use App\DataModels\CacheReportCard;
use App\DataModels\ReportEntry;
use App\Facades\EspApiAccount;
use App\Jobs\BuildAndSendReportCard;
use App\Models\ListProfileBaseTable;
use App\Models\OfferSuppressionList;
use App\Models\SuppressionListSuppression;
use App\Repositories\ListProfileBaseTableRepo;
use App\Repositories\ListProfileCombineRepo;
use App\Repositories\ListProfileRepo;
use App\Repositories\OfferRepo;
use App\Repositories\OfferSuppressionListRepo;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Filesystem;
use Storage;
use Cache;
use Log;

class ListProfileExportService
{
    use DispatchesJobs;

    private $listProfileRepo;
    private $offerRepo;
    private $tableRepo;
    private $combineRepo;
    private $mt1SuppServ;
    const BASE_TABLE_NAME = 'list_profile_export_';
    const WRITE_THRESHOLD = 50000;
    private $rows = [];
    private $rowCount = 0;
    private $suppressedRows = [];
    private $suppressedRowCount = 0;

    public function __construct(ListProfileRepo $listProfileRepo, OfferRepo $offerRepo, ListProfileCombineRepo $combineRepo, MT1SuppressionService $mt1SuppServ )
    {
        $this->listProfileRepo = $listProfileRepo;
        $this->offerRepo = $offerRepo;
        $this->combineRepo = $combineRepo;
        $this->mt1SuppServ = $mt1SuppServ;
    }

    /**
     *  Create a file export for this particular ListProfile.
     *  1. Take the results of the list profile base table just prepared
     *  2. Run this table against the indicated offer suppression
     *  3. Output the surviving email addresses to a file determined by the name.
     */

    public function exportListProfile($listProfileId, $offerId, $replacementHeader = array())
    {

        $listProfile = $this->listProfileRepo->getProfile($listProfileId);

        $tableName = self::BASE_TABLE_NAME . $listProfileId;
        $date = Carbon::today()->toDateString();
        $this->tableRepo = new ListProfileBaseTableRepo(new ListProfileBaseTable($tableName));
        $fileName = "{$listProfile->ftp_folder}/{$date}_{$listProfile->name}.csv";

        Storage::disk('espdata')->delete($fileName); // clear the file currently saved

        $columns = json_decode($listProfile->columns, true);

        if ($this->listProfileRepo->shouldInsertHeader($listProfileId) || !empty($replacementHeader) ) {
            $columns = $replacementHeader ? $replacementHeader : $columns;
            Storage::disk('espdata')->append($fileName, implode(',', $columns));
        }

        $listIds = $this->offerRepo->getSuppressionListIds($offerId);
        $result = $this->tableRepo->suppressWithListIds($listIds);

        $resource = $result->cursor();


        foreach ($resource as $row) {
            if ( !$row->globally_suppressed ) {
                $row = $this->mapRow($columns, $row);
                $this->batch($fileName, $row);
            }
        }

        $this->writeBatch($fileName);
        return $fileName;

    }

    public function exportListProfileCombine($listProfileCombineId)
    {
        $listProfileCombine = $this->combineRepo->getRowWithListProfiles($listProfileCombineId);
        $files = array();
        $listProfileCombineHeader = array();
        $columns = $this->combineRepo->getCombineHeader($listProfileCombineId);
        foreach($columns as $item){
            $listProfileCombineHeader = array_merge($listProfileCombineHeader, json_decode($item->columns));
        }

        foreach($listProfileCombine as $listProfile){
            $files[] = $this->exportListProfile($listProfile->id, null, array_unique($listProfileCombineHeader));
        }
        $date = Carbon::today()->toDateString();
        $fileName = "{$listProfileCombine->ftp_folder}/{$date}_{$listProfileCombine->name}.csv";

        Storage::disk('espdata')->delete($fileName);
        Storage::disk('espdata')->append($fileName, implode(',', $listProfileCombineHeader));

        foreach ($files as $file) {
            $contents = Storage::disk('espdata')->get($file);
            Storage::disk('espdata')->append($fileName, $contents);
        }

    }

    public function exportListProfileToMany($listProfileId, $offerId, $deploys, $reportCardName = null)
    {
        $reportCard = null;
        
        if($reportCardName){
           $reportCard = CacheReportCard::getReportCard($reportCardName);
        }

        $listProfile = $this->listProfileRepo->getProfile($listProfileId);

        $tableName = self::BASE_TABLE_NAME . $listProfileId;
        $this->tableRepo = new ListProfileBaseTableRepo(new ListProfileBaseTable($tableName));

        $listIds = $this->offerRepo->getSuppressionListIds($offerId);
        $result = $this->tableRepo->suppressWithListIds($listIds);

        $resource = $result->cursor();

        foreach ($deploys as $deploy) {

            $headers = array();

            $key = "{$deploy->id}-{$deploy->list_profile_combine_id}";

            $header = Cache::get("header-{$key}", function () use ($deploy, $headers) {
                $columns = $this->combineRepo->getCombineHeader($deploy->list_profile_combine_id);
                foreach($columns as $item){
                    $headers = array_merge($headers, json_decode($item->columns));
                }
                return array_unique($headers);
            });

            $deployProgress = Cache::get("deploy-{$key}", function () use ($deploy) {
                $listProfileCombine = $this->combineRepo->getRowWithListProfiles($deploy->list_profile_combine_id);
                $num = count($listProfileCombine->listProfiles);
                return array(
                    "id" => $deploy->id,
                    "ftp_folder" => $listProfileCombine->ftp_folder,
                    "reportEntry" => new ReportEntry($deploy->deploy_name),
                    "espAccount" => $deploy->esp_account_id,
                    "name" => $listProfileCombine->name,
                    "totalPieces" => $num,
                    "files" => array(),
                );
            });

            //these files are for us to build combines.
            $fileName = 'DeployTemp/' . $listProfile->name . '-' . $deploy->id . '-' . $offerId . '.csv';
            Storage::delete($fileName); // clear the file currently saved

            $recordEntry = $deployProgress['reportEntry'];
            $recordEntry->addToOriginalTotal(count($resource));
            //GrabOffersSuppressedAgainst and store for to place in record
            if($reportCardName) {
                $offerSuppressionLists = OfferSuppressionList::find($offerId)->all();
                $offersPlucked = $offerSuppressionLists->pluck('id');
                $reportCard->addOffersSuppressedAgainst($offersPlucked);
            }

            foreach ($resource as $row) {
                if($row->globally_suppressed){
                     $this->batchSuppression($fileName, $row);
                    $recordEntry->increaseGlobalSuppressionCount();
                }
                elseif ($this->mt1SuppServ->isSuppressed($row->email_id)){
                    $this->batchSuppression($fileName, $row);
                    $recordEntry->increaseListSuppressionCount();
                }
                 elseif(!$row->suppression_status){
                     $this->batchSuppression($fileName, $row);
                     $recordEntry->increaseListSuppressionCount();
                }
                else {
                     $row = $this->mapRow($header, $row);
                     $this->batch($fileName, $row, "local");
                     $recordEntry->increaseFinalRecordCount();
                }
            }

            $this->writeBatch($fileName, "local");
            $this->writeBatchSuppression($fileName);

            //either get the deploy cache or build it

            $deployProgress['totalPieces']--;

            if ($deployProgress['totalPieces'] == 0) {
                $deployProgress['files'] = array_merge($deployProgress['files'], array($fileName));
                Cache::forget("header-{$key}");
                Cache::forget("deploy-{$key}");
                $fileName = $this->buildCombineFile($header,$deployProgress['ftp_folder'], $deployProgress['name'], $deployProgress['files'], $offerId, $deployProgress['id'],  $deployProgress['espAccount']);
                if($reportCardName) {
                    $recordEntry->setFileName($fileName);
                    $reportCard->addEntry($recordEntry);
                }
            } else {
                //Update the cache
                Cache::put("deploy-{$key}",
                    array(
                        "id" => $deployProgress['id'],
                        "ftp_folder" => $deployProgress['ftp_folder'],
                        "espAccount" => $deployProgress['espAccount'],
                        "reportEntry" => $recordEntry,
                        "name" => $deployProgress['name'],
                        "totalPieces" => $deployProgress['totalPieces'],
                        "files" => array_merge($deployProgress['files'], array($fileName)),
                    ), 60 * 12);
            }

        }

        if($reportCard){
          $reportCard->nextEntry();
            if($reportCard->isReportCardFinished()){
               $this->dispatch(new BuildAndSendReportCard($reportCard));
            }
        }
    }

    private function batch($fileName, $row, $disk = 'espdata')
    {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatch($fileName,$disk);

            $this->rows = [$row];
            $this->rowCount = 1;
        } else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }

    private function batchSuppression($fileName, $row)
    {
        if ($this->suppressedRowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatchSuppression($fileName);

            $this->suppressedRows = [$row->email_address];
            $this->suppressedRowCount = 1;
        } else {
            $this->suppressedRows[] = $row->email_address;
            $this->suppressedRowCount++;
        }
    }

    private function writeBatch($fileName, $disk = 'espdata' )
    {
        $string = implode(PHP_EOL, $this->rows);
        Storage::disk($disk)->append($fileName, $string);
    }

    private function writeBatchSuppression($fileName)
    {
        $string = implode(PHP_EOL, $this->suppressedRows);
        Storage::append($fileName.'-dnm', $string);
    }

    private function mapRow($columns, $row)
    {
        $output = [];

        foreach ($columns as $column) {
                $output[$column] = isset($row->$column) ? $row->$column : "";
        }
        return implode(',', $output);
    }

    private function buildCombineFile($header, $ftpFolder, $fileName, $files, $offerId,$deployId, $espAccount)
    {
        $adapter = new Ftp([
                'host' => config("filesystems.disks.SystemFtp.host"),
                'username' => config("filesystems.disks.SystemFtp.username"),
                'password' => config("filesystems.disks.SystemFtp.password"),
            ]
        );
        $fileSys = new Filesystem($adapter);
        $espAccountName = EspApiAccount::getEspAccountName($espAccount);
        $offerName = $this->offerRepo->getOfferName($offerId);
        $date = Carbon::today()->toDateString();
        $combineFileName = "{$ftpFolder}/{$date}_{$deployId}_{$espAccountName}_{$fileName}_{$offerName}.csv";
        $combineFileNameDNM = "{$ftpFolder}/{$date}_DONOTMAIL_{$deployId}_{$espAccountName}_{$fileName}_{$offerName}.csv";
        Storage::disk('SystemFtp')->delete($combineFileName);
        Storage::disk('SystemFtp')->delete($combineFileNameDNM);
        Storage::disk('SystemFtp')->append($combineFileName, implode(',', $header));
        $tempStream = tmpfile();
        $tempDNMStream = tmpfile();
        foreach ($files as $file) {
            $contents = fopen(storage_path("app") . $file, 'r+');
            fwrite($tempStream, $contents);
            Storage::disk('SystemFtp')->delete($file);
        }
        foreach ($files as $file) {
            $contents = fopen(storage_path("app") . $file.'-dnm', 'r+');
            fwrite($tempDNMStream, $contents);
            Storage::disk('SystemFtp')->delete($file.'-dnm');
        }
        $fileSys->putStream($combineFileName,$this->dedupeStream($tempStream));
        $fileSys->putStream($combineFileNameDNM,$this->dedupeStream($tempDNMStream));
        return $combineFileName;
    }
    
    public function dedupeStream($stream)
    {
        $inputHandle = fopen((storage_path("app") . $stream), "r");
        $csv = trim(fgetcsv($inputHandle, 0, ","));
        return array_flip(array_flip($csv));//faster then array_unique;
        
    }

}
