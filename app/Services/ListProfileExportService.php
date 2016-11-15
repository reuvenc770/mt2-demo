<?php

namespace App\Services;

use App\Models\ListProfileBaseTable;
use App\Repositories\ListProfileBaseTableRepo;
use App\Repositories\ListProfileCombineRepo;
use App\Repositories\ListProfileRepo;
use App\Repositories\OfferRepo;
use Storage;
use Cache;
use Log;
class ListProfileExportService
{

    private $listProfileRepo;
    private $offerRepo;
    private $combineRepo;
    const BASE_TABLE_NAME = 'list_profile_export_';
    const WRITE_THRESHOLD = 50000;
    private $rows = [];
    private $rowCount = 0;

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

    public function exportListProfile($listProfileId, $offerId)
    {

        $listProfile = $this->listProfileRepo->getProfile($listProfileId);

        if (!empty($offerId)) {
            $fileName = 'ListProfiles/' . $listProfile->name . '-' . $offerId . '.csv';
        } else {
            $fileName = 'ListProfiles/' . $listProfile->name . '.csv';
        }
        Log::info("WRITING TO {$fileName}");
        return true;
        $tableName = self::BASE_TABLE_NAME . $listProfileId;

        $this->tableRepo = new ListProfileBaseTableRepo(new ListProfileBaseTable($tableName));

        if ($offerId >= 1) {
            $fileName = 'ListProfiles/' . $listProfile->name . '-' . $offerId . '.csv';
        } else {
            $fileName = 'ListProfiles/' . $listProfile->name . '.csv';
        }

        Storage::delete($fileName); // clear the file currently saved

        $columns = json_decode($listProfile->columns, true);

        if ($this->listProfileRepo->shouldInsertHeader($listProfileId)) {
            Storage::append($fileName, implode(',', $columns));
        }

        $listIds = $this->offerRepo->getSuppressionListIds($offerId);
        $result = $this->tableRepo->suppressWithListIds($listIds);

        $resource = $result->cursor();

        foreach ($resource as $row) {
            $row = $this->mapRow($columns, $row);
            $this->batch($fileName, $row);
        }

        $this->writeBatch($fileName);

    }

    public function exportListProfileToMany($listProfileId, $offerId, $deploys)
    {

       $listProfile = $this->listProfileRepo->getProfile($listProfileId);

        //$tableName = self::BASE_TABLE_NAME . $listProfileId;
        //$this->tableRepo = new ListProfileBaseTableRepo(new ListProfileBaseTable($tableName));

        //$listIds = $this->offerRepo->getSuppressionListIds($offerId);
        //$result = $this->tableRepo->suppressWithListIds($listIds);

        //$resource = $result->cursor();

        foreach($deploys as $deploy){
            $key = "{$deploy->id}-{$deploy->list_profile_combine_id}";
            $header = Cache::get("header-{$key}", function () {
                return "lol";
            });
            $fileName = 'ListProfiles/' . $listProfile->name . '-' . $deploy->id.'-' . $offerId . '.csv';
            Storage::delete($fileName); // clear the file currently saved

            /**
            foreach ($resource as $row) {
                $row = $this->mapRow($header, $row);
                $this->batch($fileName, $row);
            }**/

            //$this->writeBatch($fileName);

            $deployProgress = Cache::get("deploy-{$key}", function () use($key, $fileName, $deploy ) {
                $listProfileCombine = $this->combineRepo->getRowWithListProfiles($deploy->list_profile_combine_id);
                $num = count($listProfileCombine->listProfiles);
                return array(
                    "totalPieces" => $num,
                    "files"=> array(),
                );
            });

            $deployProgress['totalPieces']--;
            if($deployProgress['totalPieces'] == 0){
                $deployProgress['files'] = array_merge($deployProgress['files'], array($fileName));
                Cache::forget("header-{$key}");
                Cache::forget("deploy-{$key}");
                Log::info("finished Combine {$key}");
                Log::info($deployProgress['files']);
            } else {
                Cache::put("deploy-{$key}",
                    array(
                        "totalPieces" =>  $deployProgress['totalPieces'] ,
                    "files"=> array_merge($deployProgress['files'], array($fileName)) ,
                    ),60*12);
            }

        }
    }

    private function batch($fileName, $row)
    {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatch($fileName);

            $this->rows = [$row];
            $this->rowCount = 1;
        } else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }

    private function writeBatch($fileName)
    {
        $string = implode(PHP_EOL, $this->rows);
        Storage::append($fileName, $string);
    }

    private function mapRow($columns, $row)
    {
        $output = [];

        foreach ($columns as $column) {
            if (isset($row->$column)) {
                $output[$column] = $row->$column;
            }
        }

        return implode(', ', $output);
    }

}