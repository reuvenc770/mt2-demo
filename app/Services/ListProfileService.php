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
use Cache;
use App\Repositories\FeedRepo;
use App\Services\MT1Services\ClientStatsGroupingService;
use App\Services\ListProfileBaseTableCreationService;
use App\Services\ServiceTraits\PaginateList;

class ListProfileService
{
    use PaginateList;

    protected $profileRepo;
    protected $builder;
    private $rows = [];
    private $rowCount = 0;
    const INSERT_THRESHOLD = 1000; // Low threshold due to MySQL / PHP placeholder limits (2^16 - 1)
    private $uniqueColumn;
    const ROW_STORAGE_TIME = 60;
    protected $baseTableService;

    public function __construct(ListProfileRepo $profileRepo, ListProfileQueryBuilder $builder, ListProfileBaseTableCreationService $baseTableService) {
        $this->profileRepo = $profileRepo;
        $this->builder = $builder;
        $this->baseTableService = $baseTableService;
    }

    public function getModel () {
        return $this->profileRepo->getModel();
    }

    public function create ( $data ) {
        $cleanData = $this->cleanseData( $data ); 

        $id = $this->profileRepo->create( $cleanData );

        $this->saveEntities( $id , $data );
        return $id;
    }

    public function getFullProfileJson ( $id ) {
        $listProfile = $this->profileRepo->getProfile( $id );
        $schedule = $listProfile->schedule()->first();

        $zips = json_decode( $listProfile->zip );
        $cities = json_decode( $listProfile->city );

        return json_encode( [
            'profile_id' => $id ,
            'name' => $listProfile->name , 
            'actionRanges' => [
                'deliverable' => [
                    'min' => $listProfile->deliverable_start ,
                    'max' => $listProfile->deliverable_end
                ] ,
                'opener' => [
                    'min' => $listProfile->openers_start ,
                    'max' => $listProfile->openers_end ,
                    'multiaction' => $listProfile->open_count
                ] ,
                'clicker' => [
                    'min' => $listProfile->clickers_start ,
                    'max' => $listProfile->clickers_end ,
                    'multiaction' => $listProfile->click_count
                ] ,
                'converter' => [
                    'min' => $listProfile->converters_start ,
                    'max' => $listProfile->converters_end ,
                    'multiaction' => $listProfile->conversion_count
                ]
            ] ,
            'suppression' => $listProfile->use_global_suppression ? [ 'global' => [ 1 => 'Orange Global' ] ] : [] ,
            'attributeFilters' => [
                'age' => json_decode( $listProfile->age_range ) ,
                'genders' => array_intersect( [ 'Male' => 'M' , 'Female' => 'F' , 'Unknown' => 'U' ] , json_decode( $listProfile->gender ) ) ,
                'zips' => ( is_array( $zips ) && !empty( $zips ) ? implode( ',' , $zips ) : '' ) ,
                'cities' => ( is_array( $cities ) && !empty( $cities ) ? implode( ',' , $cities ) : '' ) ,
                'states' => json_decode( $listProfile->state ) ,
                'deviceTypes' => json_decode( $listProfile->device_type ) ,
                'mobileCarriers' => json_decode( $listProfile->mobile_carrier ) ,
                'os' => json_decode( $listProfile->device_os )
            ] ,
            'includeCsvHeader' => $listProfile->insert_header ,
            'selectedColumns' => json_decode( $listProfile->columns ) ,
            'exportOptions' => [
                'interval' =>  [ $listProfile->run_frequency ] ,
                'dayOfWeek' => $schedule->day_of_week ? $schedule->day_of_week : null ,
                'dayOfMonth' => $schedule->day_of_month ? $schedule->day_of_month : null
            ] ,
            'countries' => $listProfile->countries()->get()->pluck( 'id' ,'name' )->toArray() ,
            'feeds' => $listProfile->feeds()->get()->pluck( 'short_name' , 'id' )->toArray() ,
            'isps' => $listProfile->domainGroups()->get()->pluck( 'name' , 'id' )->toArray() ,
            'categories' => $listProfile->verticals()->get()->pluck( 'name' , 'id' )->toArray() ,
            'offers' => $listProfile->offers()->get()->toArray() ,
            'includeCsvHeader' => $listProfile->insert_header ? true : false ,
            'admiralsOnly' => $listProfile->admiral_only ? true : false
        ] );
    }

    public function formUpdate ( $id , $data ) {
        $cleanData = $this->cleanseData( $data );
        $cleanData[ 'profile_id' ] = $id;

        $this->profileRepo->updateOrCreate( $cleanData );

        $this->saveEntities( $id , $data , true );
    }

    public function updateOrCreate ( $data ) {
        $this->profileRepo->updateOrCreate( $data );
    }

    public function getActiveListProfiles() {
        return $this->profileRepo->returnActiveProfiles();
    }


    public function buildProfileTable($id) {
        /**
            - Run against hygiene
            Feed & offer suppression
         */
        $listProfile = $this->profileRepo->getProfile($id);
        $queries = $this->returnQueriesData($listProfile);
        $queryNumber = 1;
        $totalCount = 0;

        $listProfileTag = 'list_profile-' . $listProfile->id . '-' . $listProfile->name;

        foreach ($queries as $queryData) {
            $query = $this->builder->buildQuery($listProfile, $queryData);

            // .. if we have hygiene, we write out both files. Write full one to a secret location. Send the other one (just email address/md5) out.
            // When the second returns. Find a way to subtract it from the first
            
            $columns = $this->builder->getColumns();

            if (1 === $queryNumber) {
                $this->baseTableService->createTable($id, $columns);
            }

            // set up unique column. Can be one of email_id, email_address, or ''
            // Not entirely needed anymore given that we use email_id in all list profiles
            // We could hardcode this instead
            $this->uniqueColumn = $this->getUniqueColumn($columns);

            $resource = $query->cursor();

            foreach ($resource as $row) {
                if ($this->isUnique($listProfileTag, $row)) {
                    $this->saveToCache($listProfileTag, $row->{$this->uniqueColumn});
                    $row = $this->mapDataToColumns($columns, $row);
                    $this->batch($row);
                    $totalCount++;
                }
            }

            $this->batchInsert();
            $this->clear();
            
            $queryNumber++;
        }

        Cache::tags($listProfileTag)->flush();

        $this->profileRepo->updateTotalCount($listProfile->id, $totalCount);

    }

    private function cleanseData ( $data ) {
        return [
            'name' => $data[ 'name' ] ,
            'deliverable_start' => isset( $data[ 'actionRanges' ][ 'deliverable' ][ 'min' ] ) ? $data[ 'actionRanges' ][ 'deliverable' ][ 'min' ] : 0 ,
            'deliverable_end' => isset( $data[ 'actionRanges' ][ 'deliverable' ][ 'max' ] ) ? $data[ 'actionRanges' ][ 'deliverable' ][ 'max' ] : 0 ,
            'openers_start' => isset( $data[ 'actionRanges' ][ 'opener' ][ 'min' ] ) ? $data[ 'actionRanges' ][ 'opener' ][ 'min' ] : 0 ,
            'openers_end' => isset( $data[ 'actionRanges' ][ 'opener' ][ 'max' ] ) ? $data[ 'actionRanges' ][ 'opener' ][ 'max' ] : 0 ,
            'open_count' => isset( $data[ 'actionRanges' ][ 'opener' ][ 'multiaction' ] ) ? $data[ 'actionRanges' ][ 'opener' ][ 'multiaction' ] : 1 ,
            'clickers_start' => isset( $data[ 'actionRanges' ][ 'clicker' ][ 'min' ] ) ? $data[ 'actionRanges' ][ 'clicker' ][ 'min' ] : 0 ,
            'clickers_end' => isset( $data[ 'actionRanges' ][ 'clicker' ][ 'max' ] ) ? $data[ 'actionRanges' ][ 'clicker' ][ 'max' ] : 0 ,
            'click_count' => isset( $data[ 'actionRanges' ][ 'clicker' ][ 'multiaction' ] ) ? $data[ 'actionRanges' ][ 'clicker' ][ 'multiaction' ] : 1 ,
            'converters_start' => isset( $data[ 'actionRanges' ][ 'converter' ][ 'min' ] ) ? $data[ 'actionRanges' ][ 'converter' ][ 'min' ] : 0 ,
            'converters_end' => isset( $data[ 'actionRanges' ][ 'converter' ][ 'max' ] ) ? $data[ 'actionRanges' ][ 'converter' ][ 'max' ] : 0 ,
            'conversion_count' => isset( $data[ 'actionRanges' ][ 'converter' ][ 'multiaction' ] ) ? $data[ 'actionRanges' ][ 'converter' ][ 'multiaction' ] : 1,
            'use_global_suppression' => $data[ 'suppression' ][ 'global' ] ? 1 : 0 ,
            'age_range' => json_encode( $data[ 'attributeFilters' ][ 'age' ] ) ,
            'gender' => json_encode( array_values( $data[ 'attributeFilters' ][ 'genders' ] ) ) ,
            'zip' => $data[ 'attributeFilters' ][ 'zips' ] ? json_encode( explode( ',' , $data[ 'attributeFilters' ][ 'zips' ] ) ) : '{}' ,
            'city' => $data[ 'attributeFilters' ][ 'cities' ] ? json_encode( explode( ',' , $data[ 'attributeFilters' ][ 'cities' ] ) ) : '{}' ,
            'state' => json_encode( $data[ 'attributeFilters' ][ 'states' ] ) ,
            'device_type' => json_encode( $data[ 'attributeFilters' ][ 'deviceTypes' ] ) ,
            'mobile_carrier' => json_encode( $data[ 'attributeFilters' ][ 'mobileCarriers' ] ) ,
            'insert_header' => $data[ 'includeCsvHeader' ],
            'device_os' => json_encode( $data[ 'attributeFilters' ][ 'os' ] ) ,
            'columns' => json_encode( $data[ 'selectedColumns' ] ) ,
            'run_frequency' => ( ( isset( $data[ 'exportOptions' ][ 'interval' ] ) && $choice = array_intersect( $data[ 'exportOptions' ][ 'interval' ] , [ 'Daily' , 'Weekly' , 'Monthly' , 'Never' ] ) ) ? array_pop( $choice ) : 'Never' ) ,
            'admiral_only' => $data[ 'admiralsOnly' ] ,
        ]; 
    }

    private function saveEntities ( $id , $data , $isUpdate = false ) {
        if ( $data[ 'categories' ] || $isUpdate ) {
            $this->profileRepo->assignVerticals( $id , array_keys( $data[ 'categories' ] ) );
        } 

        if ( $data[ 'exportOptions' ][ 'interval' ] || $isUpdate ) {
            $this->profileRepo->assignSchedule( $id , $data[ 'exportOptions' ] );
        }

        if ( $data[ 'offers' ] || $isUpdate ) {
            $this->profileRepo->assignOffers( $id , $data[ 'offers' ] );
        }

        if ( $data[ 'feeds' ] || $isUpdate ) {
            $this->profileRepo->assignFeeds( $id , array_keys( $data[ 'feeds' ] ) );
        }

        if ( $data[ 'isps' ] || $isUpdate ) {
            $this->profileRepo->assignIsps( $id , array_keys( $data[ 'isps' ] ) );
        }

        if ( $data[ 'countries' ] || $isUpdate ) {
            $this->profileRepo->assignCountries( $id , $data[ 'countries' ] );
        }
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

        foreach ($columns as $id=>$column) {
            $output[$column] = $row->$column ?: '';
        }

        return $output;
    }


    private function batch($row) {
        if ($this->rowCount >= self::INSERT_THRESHOLD) {
            $this->batchInsert();

            $this->rows = [$row];
            $this->rowCount = 0;
        }
        else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }


    private function batchInsert() {
        $this->baseTableService->massInsert($this->rows);
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
