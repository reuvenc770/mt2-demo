<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/8/16
 * Time: 11:31 AM
 */

namespace App\Services;


use App\Repositories\ListProfileRepo;
use App\Builders\ListProfileQueryBuilder;
use Storage;
use Cache;
use App\Repositories\FeedRepo;
use App\Services\MT1Services\ClientStatsGroupingService;
use App\Services\MT1Services\ClientService;
use App\Services\ListProfileBaseTableService;

class ListProfileService
{
    protected $profileRepo;
    protected $builder;
    private $rows = [];
    private $rowCount = 0;
    const INSERT_THRESHOLD = 50000;
    private $uniqueColumn;
    const ROW_STORAGE_TIME = 60;
    protected $baseTableService;

    public function __construct(ListProfileRepo $profileRepo, ListProfileQueryBuilder $builder, ListProfileBaseTableService $baseTableService) {
        $this->profileRepo = $profileRepo;
        $this->builder = $builder;
        $this->baseTableService = $baseTableService;
    }


    public function getActiveListProfiles() {
        return $this->profileRepo->returnActiveProfiles();
    }


    public function buildProfileTable($id) {
        /**
            - Run against hygiene
         */

        $listProfile = $this->profileRepo->getProfile($id);
        $queries = $this->returnQueriesData($listProfile);
        $queryNumber = 1;
        $totalCount = 0;

        $fileName = 'ListProfiles/' . $listProfile->name . '.csv';
        $listProfileTag = 'list_profile-' . $listProfile->id . '-' . $listProfile->name;

        Storage::delete($fileName); // clear the file currently saved

        foreach ($queries as $queryData) {
            $query = $this->builder->buildQuery($listProfile, $queryData);

            // .. if we have hygiene, we write out both files. Write full one to a secret location. Send the other one (just email address/md5) out.
            // When the second returns. Find a way to subtract it from the first
            
            $insertHeader = $listProfile->insert_header === 1;
            $columns = $this->builder->getColumns();

            /**
                Need to create the table here.
            */

            // set up unique column. Can be one of email_id, email_address, or ''
            $this->uniqueColumn = $this->getUniqueColumn($columns);

            /**
            This has to be moved elsewhere
            if ($insertHeader && 1 === $queryNumber) {
                Storage::append($fileName, implode(',', $columns));
            }
            */

            $resource = $query->cursor();

            foreach ($resource as $row) {
                if ($this->isUnique($listProfileTag, $row)) {
                    $this->saveToCache($listProfileTag, $row->{$this->uniqueColumn});
                    $row = $this->mapDataToColumns($columns, $row);

                    /**
                        Rewrite this to do an insert into a table
                    */
                    $this->append($fileName, $row);
                    $totalCount++;
                }
            }

            $this->write($fileName);
            $this->clear();
            
            $queryNumber++;
        }

        Cache::tags($listProfileTag)->flush();

        $listProfile->total_count = $totalCount;

    }


    private function returnQueriesData($listProfile) {
        $queries = [];

        if ($listProfile->deliverable_end !== $listProfile->deliverable_start && $listProfile->deliverable_end !== 0) {
            $queries[] = ['type' => 'deliverable', 'start' => $listProfile->deliverable_start, 'end' => $listProfile->deliverable_end, 'count' => 1];
        }
        if ($listProfile->openers_start !== $listProfile->openers_end && $listProfile->openers_end !== 0) {
            $queries[] = ['type' => 'opens', 'start' => $listProfile->openers_start, 'end' => $listProfile->openers_end, 'count' => $listProfile->open_count];
        }
        if ($listProfile->clickers_start !== $listProfile->clickers_end && $listProfile->clickers_end !== 0) {
            $queries[] = ['type' => 'clicks', 'start' => $listProfile->clickers_start, 'end' => $listProfile->clickers_end, 'count' => $listProfile->click_count];
        }
        if ($listProfile->converters_start !== $listProfile->converters_end && $listProfile->converters_end !== 0) {
            $queries[] = ['type' => 'conversions', 'start' => $listProfile->converters_start, 'end' => $listProfile->converters_end, 'count' => $listProfile->conversion_count];
        }

        return $queries;
    }


    private function mapDataToColumns($columns, $row) {
        $output = [];

        foreach ($columns as $column) {
            $output[] = $row->$column;
        }

        return implode(',', $output);
    }


    private function append($fileName, $row) {
        if ($this->rowCount >= self::INSERT_THRESHOLD) {
            $this->write($fileName);

            $this->rows = [$row];
            $this->rowCount = 0;
        }
        else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }


    private function write($fileName) {
        $string = implode(PHP_EOL, $this->rows);
        Storage::append($fileName, $string);
    }


    private function clear() {
        $this->rows = [];
        $this->rowCount = 0;
    }


    private function getUniqueColumn(array $columns) {
        // In order to de-dupe rows, we need a field that is guaranteed to be unique, so either email id or email address.
        // If neither of those columns returns data, we will not dedupe data.
        if (in_array('email_id', $columns)) {
            return 'email_id';
        }
        elseif (in_array('email_address', $columns)) {
            return 'email_address';
        }
        return '';
    }


    private function isUnique($tag, $row) {
        if ('' === $this->uniqueColumn) {
            return true;
        }
        else {
            return is_null(Cache::tags($tag)->get($row->{$this->uniqueColumn}));
        }
    }


    private function saveToCache($tag, $value) {
        Cache::tags($tag)->put($value, 1, self::ROW_STORAGE_TIME);
    }
}
