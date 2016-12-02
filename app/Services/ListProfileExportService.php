<?php

namespace App\Services;

use App\Facades\EspApiAccount;
use App\Models\ListProfileBaseTable;
use App\Repositories\ListProfileBaseTableRepo;
use App\Repositories\ListProfileCombineRepo;
use App\Repositories\ListProfileRepo;
use App\Repositories\OfferRepo;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;
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
    const BASE_TABLE_NAME = 'list_profile_export_';
    const WRITE_THRESHOLD = 50000;
    private $rows = [];
    private $rowCount = 0;
    private $suppressedRows = [];
    private $suppressedRowCount = 0;

    public function __construct(ListProfileRepo $listProfileRepo, OfferRepo $offerRepo, ListProfileCombineRepo $combineRepo)
    {
        $this->listProfileRepo = $listProfileRepo;
        $this->offerRepo = $offerRepo;
        $this->combineRepo = $combineRepo;
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
        $fileName = "{$date}_{$listProfile->name}.csv";

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
            $row = $this->mapRow($columns, $row);
            $this->batch($fileName, $row);
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
        $fileName = "{$date}_{$listProfileCombine->name}.csv";

        Storage::disk('espdata')->delete($fileName);
        Storage::disk('espdata')->append($fileName, implode(',', $listProfileCombineHeader));

        foreach ($files as $file) {
            $contents = Storage::disk('espdata')->get($file);
            Storage::disk('espdata')->append($fileName, $contents);
        }

    }

    public function exportListProfileToMany($listProfileId, $offerId, $deploys)
    {

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

            //these files are for us to build combines.
            $fileName = 'DeployTemp/' . $listProfile->name . '-' . $deploy->id . '-' . $offerId . '.csv';
            Storage::delete($fileName); // clear the file currently saved


             foreach ($resource as $row) {
                 if(!$row->suppression_status){
                     $this->batchSuppression($fileName, $row);
                 } else {
                     $row = $this->mapRow($header, $row);
                     $this->batch($fileName, $row, "local");
                 };
             }
            $this->writeBatch($fileName, "local");
            $this->writeBatchSuppression($fileName);

            //either get the deploy cache or build it
            $deployProgress = Cache::get("deploy-{$key}", function () use ($deploy) {
                $listProfileCombine = $this->combineRepo->getRowWithListProfiles($deploy->list_profile_combine_id);
                $num = count($listProfileCombine->listProfiles);
                return array(
                    "id" => $deploy->id,
                    "espAccount" => $deploy->esp_account_id,
                    "name" => $listProfileCombine->name,
                    "totalPieces" => $num,
                    "files" => array(),
                );
            });

            $deployProgress['totalPieces']--;

            if ($deployProgress['totalPieces'] == 0) {
                $deployProgress['files'] = array_merge($deployProgress['files'], array($fileName));
                Cache::forget("header-{$key}");
                Cache::forget("deploy-{$key}");
                $this->buildCombineFile($header, $deployProgress['name'], $deployProgress['files'], $offerId, $deployProgress['id'],  $deployProgress['espAccount']);
            } else {
                //Update the cache
                Cache::put("deploy-{$key}",
                    array(
                        "id" => $deployProgress['id'],
                        "espAccount" => $deployProgress['espAccount'],
                        "name" => $deployProgress['name'],
                        "totalPieces" => $deployProgress['totalPieces'],
                        "files" => array_merge($deployProgress['files'], array($fileName)),
                    ), 60 * 12);
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
        return implode(', ', $output);
    }

    private function buildCombineFile($header, $fileName, $files, $offerId,$deployId, $espAccount)
    {
        $espAccountName = EspApiAccount::getEspAccountName($espAccount);
        $offerName = $this->offerRepo->getOfferName($offerId);
        $date = Carbon::today()->toDateString();
        $combineFileName = "{$date}_{$deployId}_{$espAccountName}_{$fileName}_{$offerName}.csv";
        $combineFileNameDNM = "{$date}_DONOTMAIL_{$deployId}_{$espAccountName}_{$fileName}_{$offerName}.csv";
        Storage::disk('SystemFtp')->delete($combineFileName);
        Storage::disk('SystemFtp')->delete($combineFileNameDNM);
        Storage::disk('SystemFtp')->append($combineFileName, implode(',', $header));

        foreach ($files as $file) {
            $contents = Storage::get($file);
            Storage::disk('SystemFtp')->append($combineFileName, $contents);
            Storage::disk('SystemFtp')->delete($file);
        }

        foreach ($files as $file) {
            $contents = Storage::get($file.'-dnm');
            Storage::disk('SystemFtp')->append($combineFileNameDNM, $contents);
            Storage::disk('SystemFtp')->delete($file.'-dnm');
        }
    }

}