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
use App\Models\Deploy;

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

    public function __construct(ListProfileRepo $listProfileRepo, OfferRepo $offerRepo, ListProfileCombineRepo $combineRepo, MT1SuppressionService $mt1SuppServ ) {
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

    public function exportListProfile($listProfileId, $replacementHeader = array()) {

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

        $listIds = $this->listProfileRepo->getSuppressionListIds($offerId);
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

    private function batch($fileName, $row, $disk = 'espdata') {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatch($fileName,$disk);

            $this->rows = [$row];
            $this->rowCount = 1;
        } else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }

    private function batchSuppression($fileName, $row) {
        if ($this->suppressedRowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatchSuppression($fileName);

            $this->suppressedRows = [$row->email_address];
            $this->suppressedRowCount = 1;
        } else {
            $this->suppressedRows[] = $row->email_address;
            $this->suppressedRowCount++;
        }
    }

    private function writeBatch($fileName, $disk = 'espdata' ) {
        $string = implode(PHP_EOL, $this->rows);
        Storage::disk($disk)->append($fileName, $string);
    }

    private function writeBatchSuppression($fileName) {
        $string = implode(PHP_EOL, $this->suppressedRows);
        Storage::append($fileName.'-dnm', $string);
    }

    private function mapRow($columns, $row) {
        $output = [];

        foreach ($columns as $column) {
                $output[$column] = isset($row->$column) ? $row->$column : "";
        }
        return implode(',', $output);
    }

    public function createDeployExport(Deploy $deploy) {
        /*
            So, what are we doing here?
            1. Get the combine used by this particular deploy
            2. Generate the result for this set (deduped and made generic).
                - Two complications:
                    i. We don't know how many tables to UNION together
                    ii. The different tables might have different results
            3. Run each item against advertiser suppression.
            4. Based on result, write to mailing file or suppressed file
            5. Export file to FTP

            6. Do all that report card stuff
        */

        $combineId = $deploy->list_profile_combine_id;
        $fileName = '';

        //$fileName = 'DeployTemp/' . $listProfile->name . '-' . $deploy->id . '-' . $offerId . '.csv';
        //Storage::delete($fileName); // clear the file currently saved
        // need list of offers suppressed against
        // $offerSuppressionLists = OfferSuppressionList::find($offerId)->all();
        // $offersPlucked = $offerSuppressionLists->pluck('id');
        // $reportCard->addOffersSuppressedAgainst($offersPlucked);

        $result = $this->listProfileCombineRepo->generateCombine($combineId);

        foreach ($result as $row) {
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
                // all these files should be local
                $this->batch($fileName, $row, "local");
                $recordEntry->increaseFinalRecordCount();
            }
        }

        $this->writeBatch($fileName, "local");
        $this->writeBatchSuppression($fileName);

        /*
        if($reportCard){
      $reportCard->nextEntry();
        if($reportCard->isReportCardFinished()){
           $this->dispatch(new BuildAndSendReportCard($reportCard));
        }
        */
    }
    }

}
