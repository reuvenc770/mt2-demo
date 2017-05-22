<?php

namespace App\Services;

use App\DataModels\CacheReportCard;
use App\DataModels\ReportEntry;
use App\Models\ListProfileBaseTable;
use App\Models\OfferSuppressionList;
use App\Models\ListProfileCombine;
use App\Repositories\ListProfileBaseTableRepo;
use App\Repositories\ListProfileCombineRepo;
use App\Repositories\ListProfileRepo;
use App\Repositories\SuppressionListSuppressionRepo;
use App\Repositories\OfferRepo;
use Carbon\Carbon;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Filesystem;
use Storage;
use File;
use App\Models\Deploy;
use DB;

class ListProfileExportService {

    private $listProfileRepo;
    private $tableRepo;
    private $combineRepo;
    private $mt1SuppServ;
    private $miscSuppressionRepo;
    private $offerRepo;

    const BASE_TABLE_NAME = 'export_';
    const WRITE_THRESHOLD = 50000;
    private $rows = [];
    private $rowCount = 0;
    private $suppressedRows = [];
    private $suppressedRowCount = 0;

    public function __construct(ListProfileRepo $listProfileRepo, 
        ListProfileCombineRepo $combineRepo, 
        MT1SuppressionService $mt1SuppServ, 
        SuppressionListSuppressionRepo $miscSuppressionRepo,
        OfferRepo $offerRepo) {

        $this->listProfileRepo = $listProfileRepo;
        $this->combineRepo = $combineRepo;
        $this->mt1SuppServ = $mt1SuppServ;
        $this->miscSuppressionRepo = $miscSuppressionRepo;
        $this->offerRepo = $offerRepo;
    }

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

        $result = $this->tableRepo->getModel();
        $resource = $result->cursor();
        $count = 0;

        foreach ($resource as $row) {
            if (!$row->isGloballySuppressed() && !$row->isFeedSuppressed()) {
                
                $suppressed = false;
                foreach ($listProfile->offers as $offer) {
                    // handle advertiser suppression here
                    if ($this->mt1SuppServ->isSuppressed($row, $offer->id)) {
                        $suppressed = true;
                        $entry->incrementOfferSuppression();
                        break;
                    }
                }

                if (!$suppressed) {
                    $row = $this->mapRow($columns, $row);
                    $this->remoteBatch($fileName, $row);
                    $entry->increaseFinalRecordCount();
                }
            }
        }

        $this->writeRemoteBatch($fileName);
        return $fileName;
    }

    private function remoteBatch($fileName, $row, $disk = 'espdata') {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeRemoteBatch($fileName, $disk);

            $this->rows = [$row];
            $this->rowCount = 1;
        } else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }

    private function writeRemoteBatch($fileName, $disk = 'espdata') {
        $string = implode(PHP_EOL, $this->rows);
        Storage::disk($disk)->append($fileName, $string);
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

    private function writeBatch($fileName) {
        $string = implode(PHP_EOL, $this->rows) . PHP_EOL;
        File::append($fileName, $string);
    }

    private function writeBatchSuppression($fileName) {
        $string = implode(PHP_EOL, $this->suppressedRows) . PHP_EOL;
        File::append($fileName, $string);
    }

    private function mapRow($columns, $row) {
        $output = [];

        foreach ($columns as $column) {
            if ($column !== 'globally_suppressed' && $column !== 'feed_suppressed') {
                $output[$column] = isset($row->$column) ? $row->$column : "";
            }
        }
        return implode(',', $output);
    }

    private function generateCombineQuery($listProfiles, $header) {
        $queryObj = null;
        $fromQuery = null;

        if (count($listProfiles) === 1) {
            // True for combines that consist of just one lp
            $listProfileId = $listProfiles[0]->id;
            $tableName = self::BASE_TABLE_NAME . $listProfileId;
            $queryObj = new ListProfileBaseTable($tableName);
        }
        else {
            /* A bit more complicated. We built up a query of the form:

                select
                    email_id, max(email_address) as email_address, ..., first_name, ...
                from
                    (select
                        email_id, email_address, .... , first_name (for example), ...
                    from
                        export_a

                    UNION ALL
                    
                    select
                        email_id, email_address, ..., '' as first_name, ...
                    from
                        export_b) x
                group by
                    email_id
            */

            foreach($listProfiles as $listProfile) {
                $lpId = $listProfile->id;
                $tableName = self::BASE_TABLE_NAME . $lpId;
                $selectStatement = $this->createSelectStatement($header, json_decode($listProfile->columns));

                if ($fromQuery === null) {
                    $fromQuery = new ListProfileBaseTable($tableName);
                    $fromQuery = $fromQuery->selectRaw($selectStatement);
                }
                else {
                    $unionObj = new ListProfileBaseTable($tableName);
                    $unionObj = $unionObj->selectRaw($selectStatement);
                    $fromQuery = $fromQuery->unionAll($unionObj);
                }
            }

            // Now, to dedupe. Unfortunately, the existence of different columns means that we can't dedupe automatically with UNION (DISTINCT).
            // To get around this, we use MAX() which is safe because it always returns a value and the metadata should not differ for the same record
            $subQuery = $fromQuery->toSql();
            $aggregateHeader = [];

            foreach($header as $column) {
                // Building up the raw header for the aggregate query (see removal of duplicates below)
                if('email_id' === $column) {
                    $aggregateHeader[] = $column;
                }
                else {
                    $aggregateHeader[] = "max($column) as $column";
                }
            }

            $rawSelectString = implode(',', $aggregateHeader);
            $queryObj = DB::connection('list_profile_export_tables')->table(DB::raw('(' . $subQuery . ') x'))
                            ->selectRaw($rawSelectString)
                            ->groupBy('email_id');
        }

        return $queryObj;
    }

    private function createSelectStatement(array $totalFields, array $availableFields) {
        // Not all tables have the same fields - the empty ones have to be blank

        // The appended fields should always be available
        $availableFields = array_unique(array_merge($availableFields, ['email_id', 'email_address', 'globally_suppressed', 'feed_suppressed']));
        $emptyColumns = array_diff($totalFields, $availableFields);
        $headerColumns = [];

        // Build up select for each query within the UNION-ed subquery
        foreach ($totalFields as $column) {
            if (in_array($column, $emptyColumns)) {
                // default values for empty columns that need to exist
                $headerColumns[] = "'' as $column";
            }
            else {
                $headerColumns[] = $column;
            }
        }

        return implode(',', $headerColumns);
    }

    private function uploadFiles($mailableFile, $dnmFile) {
        $localMailableFile = $this->getLocalFileName($mailableFile);
        $localDnmFile = $this->getLocalFileName($dnmFile);

        Storage::disk('espdata')->delete($mailableFile);
        Storage::disk('espdata')->delete($dnmFile);

        // And then move this to FTP
        // clear the files currently saved
        $mailStream = fopen($localMailableFile, 'r+');
        $dnmStream = fopen($localDnmFile, 'r+');

        Storage::disk('espdata')->writeStream($mailableFile, $mailStream);
        Storage::disk('espdata')->writeStream($dnmFile, $dnmStream);

        fclose($mailStream);
        fclose($dnmStream);

        File::delete($localMailableFile);
        File::delete($localDnmFile);
    }

    private function getLocalFileName($fileName) {
        return storage_path('app') . '/' . $fileName;
    }

    private function createDeployFileName(Deploy $deploy, $version = 'mailable') {
        $offerName = $deploy->offer->name;
        $espAccountName = $deploy->espAccount->account_name;
        $ftpFolder = $deploy->listProfileCombine->ftp_folder;
        $combineName = $deploy->listProfileCombine->name;

        if ('mailable' === $version) {
            return "{$ftpFolder}/{$deploy->send_date}_{$deploy->id}_{$espAccountName}_{$combineName}_{$offerName}.csv";
        }
        elseif ('donotmail' === $version) {
            return "{$ftpFolder}/{$deploy->send_date}_DONOTMAIL_{$deploy->id}_{$espAccountName}_{$combineName}_{$offerName}.csv";
        }
    }

    private function createSimpleCombineFileName(ListProfileCombine $combine, $version = 'mailable') {
        if ('mailable' === $version) {
            return "{$combine->ftp_folder}/{$combine->name}.csv";
        }
        elseif ('donotmail' === $version) {
            return "{$combine->ftp_folder}/DONOTMAIL_{$combine->name}.csv";
        }
    }

    public function createSimpleCombineExport(ListProfileCombine $combine, ReportEntry $reportEntry) {
        $listProfiles = $this->combineRepo
                            ->getRowWithListProfiles($combine->id)
                            ->listProfiles->all();

        $combineFileName = $this->createSimpleCombineFileName($combine, 'mailable');
        $combineFileNameDNM = $this->createSimpleCombineFileName($combine, 'donotmail'); 
        $reportEntry->setFileName($combineFileName);

        // Without a deploy, these lists are currently empty
        $miscLists = [];
        $offersSuppressed = [];

        return $this->createCombineExport($listProfiles, $reportEntry, $miscLists, $offersSuppressed, $combineFileName, $combineFileNameDNM);
    }

    public function createDeployExport(Deploy $deploy, ReportEntry $reportEntry) {
        $listProfiles = $this->combineRepo
                            ->getRowWithListProfiles($deploy->list_profile_combine_id)
                            ->listProfiles->all();

        $combineFileName = $this->createDeployFileName($deploy, 'mailable');
        $combineFileNameDNM = $this->createDeployFileName($deploy, 'donotmail'); 
        $reportEntry->setFileName($combineFileName);

        // Getting lists
        $miscLists = OfferSuppressionList::where('offer_id', $deploy->offer_id)->pluck('suppression_list_id')->all();
        $offersSuppressed = [(int)$deploy->offer_id];

        return $this->createCombineExport($listProfiles, $reportEntry, $miscLists, $offersSuppressed, $combineFileName, $combineFileNameDNM);
    }

    private function createCombineExport($listProfiles, $reportEntry, $miscLists, $offersSuppressed, $combineFileName, $combineFileNameDNM) {
        $header = ['email_id', 'email_address', 'globally_suppressed', 'feed_suppressed']; // these fields must always be available in the query
        $writeHeaderCount = 0;
        $localCombineFileName = $this->getLocalFileName($combineFileName);
        $localCombineFileNameDNM = $this->getLocalFileName($combineFileNameDNM);

        foreach ($listProfiles as $listProfile) {
            $lpOffers = $listProfile->offers->pluck('id')->all();
            $lpLists = OfferSuppressionList::whereIn('offer_id', $lpOffers)->pluck('suppression_list_id')->all();
            
            $offersSuppressed = array_unique(array_merge($offersSuppressed, $lpOffers));
            $miscLists = array_unique(array_merge($miscLists, $lpLists));
            $header = array_unique(array_merge($header, json_decode($listProfile->columns)));
            $writeHeaderCount += $listProfile->insert_header;
        }

        $miscListCount = count($miscLists);

        $offerNames = [];

        foreach ($offersSuppressed as $oId) {
            $offerNames[] = $this->offerRepo->getOfferName($oId);
        }

        $reportEntry->addOffersSuppressedAgainst($offerNames);

        // Generate the query for this set (deduped and made generic).
        $query = $this->generateCombineQuery($listProfiles, $header);

        if ($writeHeaderCount > 0) {
            // Remove fields from the actual file header
            $this->batch($localCombineFileName, implode(',', array_diff($header, ['globally_suppressed', 'feed_suppressed'])));
        }

        $count = 0;

        // Run each item against various suppression checks - global, feed, advertiser - and write to file.
        // I've seen cursor() fail on relatively small result sets b/c of memory issues
        // but then succeed on much larger result sets. Using it here because it seems to work.
        foreach ($query->cursor() as $row) {
            if(1 === (int)$row->globally_suppressed){
                $this->batchSuppression($localCombineFileNameDNM, $row);
                $reportEntry->increaseGlobalSuppressionCount();
            }
            elseif(1 === (int)$row->feed_suppressed){
                $this->batchSuppression($localCombineFileNameDNM, $row);
                $reportEntry->increaseListSuppressionCount();
            }
            elseif($miscListCount > 0 && $this->miscSuppressionRepo->isSuppressedInLists($row->email_address, $miscLists)) {
                $this->batchSuppression($localCombineFileNameDNM, $row);
                $reportEntry->increaseMiscSuppressionCount();
            }
            else {
                $suppressed = false;

                foreach ($offersSuppressed as $offerId) {
                    // handle advertiser suppression here
                    if ($this->mt1SuppServ->isSuppressed($row, $offerId)) {
                        $suppressed = true;
                        $this->batchSuppression($localCombineFileNameDNM, $row);
                        $reportEntry->incrementOfferSuppression();
                        break;
                    }
                }

                if (!$suppressed) {
                    $row = $this->mapRow($header, $row);
                    $this->batch($localCombineFileName, $row);
                    $reportEntry->increaseFinalRecordCount();
                }
            }

            $count++;
        }

        $reportEntry->addOriginalTotal($count);

        $this->writeBatch($localCombineFileName);
        $this->writeBatchSuppression($localCombineFileNameDNM);

        $this->uploadFiles($combineFileName, $combineFileNameDNM);
        return $reportEntry;
    }

}
