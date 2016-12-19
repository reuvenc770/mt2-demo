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
    const ROW_STORAGE_TIME = 720;
    protected $baseTableService;
    private $columnLabelMap = [
        'email_id'  =>  'Email ID',
        'first_name'  =>  'First Name',
        'last_name'  =>  'Last Name',
        'address'  =>  'Address',
        'address2'  =>  'Address 2',
        'city'  =>  'City',
        'state'  =>  'State',
        'zip'  =>  'Zip',
        'country'  =>  'Country',
        'gender'  =>  'Gender',
        'ip'  =>  'IP Address',
        'phone'  =>  'Phone Number',
        'source_url'  =>  'Source URL',
        'age'  =>  'Age',
        'device_type'  =>  'Device Type',
        'device_name'  =>  'Device Name',
        'carrier'  =>  'Carrier',
        'capture_date'  =>  'Capture Date',
        'esp_account'  =>  'ESP Account',
        'email_address'  =>  'Email Address',
        'lower_case_md5'  =>  'Lowercase MD5',
        'upper_case_md5'  =>  'Uppercase MD5',
        'domain_group_name'  =>  "ISP",
        'dob'  =>  "Date of Birth",
        'feed_id'  =>  "Feed ID",
        'feed_name'  =>  "Feed Name",
        'client_name'  =>  "Client",
        'subscribe_date'  =>  'Subscribe Date',
        'tower_date'  =>  'Tower Date'
    ];

    // set up unique column. 'email_id' will always be in place so we can hardcode this
    private $uniqueColumn = 'email_id';

    public function __construct(ListProfileRepo $profileRepo, ListProfileQueryBuilder $builder, ListProfileBaseTableCreationService $baseTableService) {
        $this->profileRepo = $profileRepo;
        $this->builder = $builder;
        $this->baseTableService = $baseTableService;
    }

    public function getModel () {
        return $this->profileRepo->getModel();
    }

    public function getType(){
        return "ListProfile";
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
            'party' => $listProfile->party,
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
            'selectedColumns' => $this->buildDisplayColumns(json_decode( $listProfile->columns )) ,
            'exportOptions' => [
                'interval' =>  [ $listProfile->run_frequency ] ,
                'dayOfWeek' => isset($schedule) && $schedule->day_of_week ? $schedule->day_of_week : null ,
                'dayOfMonth' => isset($schedule) && $schedule->day_of_month ? $schedule->day_of_month : null
            ] ,
            'country_id' => $listProfile->country_id,
            'feeds' => $listProfile->feeds()->get()->pluck( 'short_name' , 'id' )->toArray() ,
            'isps' => $listProfile->domainGroups()->get()->pluck( 'name' , 'id' )->toArray() ,
            'feedGroups' => $listProfile->feedGroups()->get()->pluck( 'name' , 'id' )->toArray() ,
            'feedClients' => $listProfile->clients()->get()->pluck( 'name' , 'id' )->toArray() ,
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

    public function getAllListProfiles() {
        return $this->profileRepo->getAllListProfiles();
    }


    public function buildProfileTable($id) {
        /**
            - Run against hygiene
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
            'country_id' => $data[ 'country_id' ] ,
        ];
    }

    private function saveEntities ( $id , $data , $isUpdate = false ) {
        if ( $data[ 'categories' ] || $isUpdate ) {
            $this->profileRepo->assignVerticals( $id , array_keys( $data[ 'categories' ] ) );
        }

        if ( $data[ 'exportOptions' ][ 'interval' ] || $isUpdate ) {
            $this->profileRepo->assignSchedule( $id , $data[ 'exportOptions' ] );
        }

        if ( $data[ 'feedClients' ] || $isUpdate ) {
            $this->profileRepo->assignClients( $id , array_keys($data[ 'feedClients' ]) );
        }

        if ( $data[ 'offers' ] || $isUpdate ) {
            $this->profileRepo->assignOffers( $id , $data[ 'offers' ] );
        }

        if ( $data[ 'feeds' ] || $isUpdate ) {
            $this->profileRepo->assignFeeds( $id , array_keys( $data[ 'feeds' ] ) );
        }

        if ( $data[ 'feedGroups' ] || $isUpdate ) {
            $this->profileRepo->assignFeedGroups( $id , array_keys( $data[ 'feedGroups' ] ) );
        }

        if ( $data[ 'isps' ] || $isUpdate ) {
            $this->profileRepo->assignIsps( $id , array_keys( $data[ 'isps' ] ) );
        }

    }


    private function returnQueriesData($listProfile) {
        $queries = [];
        $party = (int)$listProfile->party;

        if ($listProfile->deliverable_end !== $listProfile->deliverable_start && $listProfile->deliverable_end !== 0) {
            $queries[] = ['type' => 'deliverable', 'start' => $listProfile->deliverable_start, 'end' => $listProfile->deliverable_end, 'count' => 1, 'party' => $party];
        }
        if ($listProfile->openers_start !== $listProfile->openers_end && $listProfile->openers_end !== 0) {
            $queries[] = ['type' => 'opens', 'start' => $listProfile->openers_start, 'end' => $listProfile->openers_end, 'count' => $listProfile->open_count, 'party' => $party];
        }
        if ($listProfile->clickers_start !== $listProfile->clickers_end && $listProfile->clickers_end !== 0) {
            $queries[] = ['type' => 'clicks', 'start' => $listProfile->clickers_start, 'end' => $listProfile->clickers_end, 'count' => $listProfile->click_count, 'party' => $party];
        }
        if ($listProfile->converters_start !== $listProfile->converters_end && $listProfile->converters_end !== 0) {
            $queries[] = ['type' => 'conversions', 'start' => $listProfile->converters_start, 'end' => $listProfile->converters_end, 'count' => $listProfile->conversion_count, 'party' => $party];
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


    private function isUnique($tag, $row) {
        return is_null(Cache::tags($tag)->get($row->{$this->uniqueColumn}));
    }

    private function buildDisplayColumns(array $columns) {
        $output = [];

        foreach ($columns as $column) {
            $output[] = [
                'header' => $column,
                'label' => $this->columnLabelMap[$column]
            ];
        }

        return $output;
    }


    private function saveToCache($tag, $value) {
        Cache::tags($tag)->put($value, 1, self::ROW_STORAGE_TIME);
    }

    public function cloneProfile($id){
        $currentProfile = $this->profileRepo->getProfile($id);

        $copyProfile = $currentProfile->replicate();
        $copyProfile->name = "COPY_{$currentProfile->name}";
        $copyProfile->save();

        $feeds = $currentProfile->feeds()->pluck( 'id' );
        if ( $feeds->count() > 0 ) {
            $this->profileRepo->assignFeeds( $copyProfile->id , $feeds->toArray() );
        }

        $isps = $currentProfile->domainGroups()->pluck( 'id' );
        if ( $isps->count() > 0 ) {
            $this->profileRepo->assignIsps( $copyProfile->id , $isps->toArray() );
        }

        return $copyProfile->id;
    }
}
