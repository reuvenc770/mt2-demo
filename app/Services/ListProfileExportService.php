<?php

namespace App\Services;

use App\Models\ListProfileBaseTable;
use App\Repositories\ListProfileBaseTableRepo;
use App\Repositories\ListProfileRepo;
use App\Repositories\OfferRepo;
use Storage;

class ListProfileExportService {

    private $listProfileRepo;
    private $offerRepo;
    private $tableRepo;
    const BASE_TABLE_NAME = 'list_profile_export_';
    const WRITE_THRESHOLD = 50000;
    private $rows = [];
    private $rowCount = 0;
    
    public function __construct(ListProfileRepo $listProfileRepo, OfferRepo $offerRepo) {
        $this->listProfileRepo = $listProfileRepo;
        $this->offerRepo = $offerRepo;
    }

    /**
     *  Create a file export for this particular deploy.
     *  1. Take the results of the list profile base table just prepared
     *  2. Run this table against the indicated offer suppression
     *  3. Output the surviving email addresses to a file determined by the name.
     */

    public function export($listProfileId, $offerId) {

        $listProfile = $this->listProfileRepo->getProfile($listProfileId);

        $tableName = self::BASE_TABLE_NAME . $listProfileId;

        $this->tableRepo = new ListProfileBaseTableRepo(new ListProfileBaseTable($tableName));

        $fileName = 'ListProfiles/' . $listProfile->name . '-' . $offerId . '.csv';
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

        return $fileName;

    }


    private function batch($fileName, $row) {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatch($fileName);

            $this->rows = [$row];
            $this->rowCount = 1;
        }
        else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }

    private function writeBatch($fileName) {
        $string = implode(PHP_EOL, $this->rows);
        Storage::append($fileName, $string);
    }

    private function mapRow($columns, $row) {
        $output = [];

        foreach ($columns as $column) {
            $output[$column] = $row->$column;
        }

        return implode(', ', $output);
    }

}