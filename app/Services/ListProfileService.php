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
use App\Services\MT1Services\ClientStatsGroupingService;
use App\Services\ListProfileBaseTableCreationService;
use App\Services\ServiceTraits\PaginateList;
use Log;
use Cache;

class ListProfileService
{
    use PaginateList;

    protected $profileRepo;
    protected $builder;
    private $rows = [];
    private $cache1 = [];
    private $cache2 = [];
    private $rowCount = 0;
    const MAX_ROWS = 65535;
    private $insertThreshold = 1000; // default
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
        'short_name' => "Feed Short Name",
        'client_name'  =>  "Client",
        'subscribe_date'  =>  'Registration Date',
        'tower_date'  =>  'Tower Date',
        'action_date' => 'Action Date',
        'action_status' => 'Action Status'
    ];

    // set up unique column. 'email_id' will always be in place so we can hardcode this
    private $uniqueColumn = 'email_id';

    public function __construct(ListProfileRepo $profileRepo, ListProfileQueryBuilder $builder, ListProfileBaseTableCreationService $baseTableService) {
        $this->profileRepo = $profileRepo;
        $this->builder = $builder;
        $this->baseTableService = $baseTableService;
    }

    public function getModel ( $options = [] ) {
        return $this->profileRepo->getModel( $options );
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

    public function tryToDelete ( $id ) {
        $canBeDeleted = $this->profileRepo->canBeDeleted( $id );

        if ( $canBeDeleted === true ) {
            $this->profileRepo->delete( $id );

            return true;
        } else {
            return $canBeDeleted;
        }
    }

    public function getFullProfileJson ( $id ) {
        $listProfile = $this->profileRepo->getProfile( $id );
        $schedule = $listProfile->schedule()->first();

        $zips = json_decode( $listProfile->zip );
        $cities = json_decode( $listProfile->city );

        $offersSuppressed = $listProfile->offerSuppression->all();

        return json_encode( [
            'profile_id' => $id ,
            'name' => $listProfile->name ,
            'ftp_folder' =>  $listProfile->ftp_folder,
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
            'suppression' =>  [ 'global' => ($listProfile->use_global_suppression ? [ 1 => 'Orange Global' ] : []), 'offer' => $offersSuppressed ],
            'attributeFilters' => [
                'age' => json_decode( $listProfile->age_range ) ,
                'genders' => $this->includeAndExclude($listProfile->gender),
                'zips' => $this->includeAndExclude($listProfile->zip),
                'cities' => $this->includeAndExclude($listProfile->city),
                'states' => $this->includeAndExclude($listProfile->state),
                'deviceTypes' => $this->includeAndExclude($listProfile->device_type),
                'mobileCarriers' => $this->includeAndExclude($listProfile->mobile_carrier),
                'os' => $this->includeAndExclude($listProfile->device_os)
            ] ,
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
            'offerActions' => $listProfile->offerAction()->get()->toArray() ,
            'includeCsvHeader' => $listProfile->insert_header ? true : false ,
            'admiralsOnly' => $listProfile->admiral_only ? true : false
        ] );
    }

    private function includeAndExclude($json) {
        // JSON is untrustworthy
        if ('' === $json || 'null' === $json) {
            return ['include' => [], 'exclude' => []];
        }

        $array = json_decode($json, true);
        $output = [];

        if (!isset($array['include'])) {
            $output['include'] = [];
            $output['exclude'] = [];
            return $output;
        }

        if (gettype($array['include']) === 'array') {
            $output['include'] = $array['include'];
        }
        else {
            $output['include'] = explode(',', $array['include']);
        }

        if (gettype($array['exclude']) === 'array') {
            $output['exclude'] = $array['exclude'];
        }
        else {
            $output['exclude'] = explode(',', $array['exclude']);
        }

        return $output;
    }

    private function formatCommaLists($array) {
        $output = [];

        if (!isset($array['include'])) {
            $output['include'] = [];
            $output['exclude'] = [];
            return $output;
        }

        if (gettype($array['include']) === 'array') {
            $output['include'] = $array['include'];
        }
        else {
            $inc = preg_replace('/(\s+|\,\s*$)/', '', $array['include']);
            $output['include'] = explode(',', $inc);
        }

        if (gettype($array['exclude']) === 'array') {
            $output['exclude'] = $array['exclude'];
        }
        else {
            $excl = preg_replace('/(\s+|\,\s*$)/', '', $array['exclude']);
            $output['exclude'] = explode(',', $excl);
        }

        return $output;
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

        foreach ($queries as $queryData) {
            $query = $this->builder->buildQuery($listProfile, $queryData);

            // .. if we have hygiene, we write out both files. Write full one to a secret location. Send the other one (just email address/md5) out.
            // When the second returns. Find a way to subtract it from the first

            $columns = $this->builder->getColumns();

            $this->setInsertSize(count($columns));

            if (1 === $queryNumber) {
                $this->baseTableService->createTable($id, $columns);
            }

            $resource = $query->cursor();

            foreach ($resource as $row) {
                if ($this->isUnique($this->uniqueColumn, $row->{$this->uniqueColumn})) {
                    $this->saveToCache($row->{$this->uniqueColumn});
                    $mappedRow = $this->mapDataToColumns($columns, $row);
                    $this->batch($mappedRow);

                    if (!$row->globally_suppressed && !$row->feed_suppressed) {
                        $totalCount++;
                    }
                    
                }
            }

            $this->batchInsert();
            $this->clear();
            $queryNumber++;
        }

        Cache::tags('ListProfile')->flush();
        $this->profileRepo->updateTotalCount($listProfile->id, $totalCount);
        return $totalCount;
    }

    private function setInsertSize($columnCount) {
        // MySQL / PHP has a placeholder limit of 2^16 - 1 (65535) 
        // The lower the limit the more likely that your query can be handled, 
        // but the slower it will go. We can set this dynamically to improve perf
        // leaving in a safety factor (floor and multiply by .9) to make sure we're safe

        $this->insertThreshold = (int)floor((self::MAX_ROWS / $columnCount) * 0.9);
    }

    private function cleanseData ( $data ) {
        return [
            'name' => $data[ 'name' ] ,
            'ftp_folder' => $data['ftp_folder'],
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
            'gender' => json_encode($this->formatCommaLists($data['attributeFilters']['genders'])),
            'zip' => json_encode($this->formatCommaLists($data['attributeFilters']['zips'])),
            'city' => json_encode($this->formatCommaLists($data['attributeFilters']['cities'])),
            'state' => json_encode($this->formatCommaLists($data['attributeFilters']['states'])),
            'device_type' => json_encode($this->formatCommaLists($data['attributeFilters']['deviceTypes'])),
            'mobile_carrier' => json_encode($this->formatCommaLists($data['attributeFilters']['mobileCarriers'])),
            'device_os' => json_encode($this->formatCommaLists($data['attributeFilters']['os'])),
            'insert_header' => $data[ 'includeCsvHeader' ],
            'columns' => json_encode( $data[ 'selectedColumns' ] ) ,
            'run_frequency' => ( ( isset( $data[ 'exportOptions' ][ 'interval' ] ) && $choice = array_intersect( $data[ 'exportOptions' ][ 'interval' ] , [ 'Daily' , 'Weekly' , 'Monthly' , 'Never' ] ) ) ? array_pop( $choice ) : 'Never' ) ,
            'admiral_only' => $data[ 'admiralsOnly' ] ,
            'country_id' => $data[ 'country_id' ] ,
            'party' => $data['party']
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

        if ( $data[ 'offerActions' ] || $isUpdate ) {
            $this->profileRepo->assignOfferActions( $id , $data[ 'offerActions' ] );
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

        if ($data['suppression']['offer'] || $isUpdate) {
            $this->profileRepo->assignOfferSuppression($id, $data['suppression']['offer']);
        }

    }


    private function returnQueriesData($listProfile) {
        $queries = [];
        $party = (int)$listProfile->party;

        if ($listProfile->deliverable_end !== $listProfile->deliverable_start && $listProfile->deliverable_end !== 0) {
            $queries[] = ['type' => 'deliverable', 'start' => $listProfile->deliverable_start, 'end' => $listProfile->deliverable_end, 'count' => 1, 'party' => $party];
        }

        // For these, we can cut down on queries significantly by merging together those queries with the same date range

        $responderData = [];

        if ($listProfile->openers_start !== $listProfile->openers_end && $listProfile->openers_end !== 0) {
            $responderData[] = ['action' => 'has_open', 'start' => $listProfile->openers_start, 'end' => $listProfile->openers_end, 'count' => $listProfile->open_count, 'party' => $party];
        }
        if ($listProfile->clickers_start !== $listProfile->clickers_end && $listProfile->clickers_end !== 0) {
            $responderData[] = ['action' => 'has_click', 'start' => $listProfile->clickers_start, 'end' => $listProfile->clickers_end, 'count' => $listProfile->click_count, 'party' => $party];
        }
        if ($listProfile->converters_start !== $listProfile->converters_end && $listProfile->converters_end !== 0) {
            $responderData[] = ['action' => 'has_conversion', 'start' => $listProfile->converters_start, 'end' => $listProfile->converters_end, 'count' => $listProfile->conversion_count, 'party' => $party];
        }

        $responders = array_reduce($responderData, function($carry, $item) {
                $output = $carry;
                $i = 0;
                $len = sizeof($carry);

                while ($i < $len) {
                    if ($item['start'] === $carry[$i]['start'] && $item['end'] === $carry[$i]['end'] && $item['count'] === $carry[$i]['count']) {
                        $output[$i]['action'][] = $item['action'];
                        $i = $len;
                    }
                    elseif ($i === $len - 1) {
                        $output[] = ['type' => 'responder', 'action' => [$item['action']], 'start' => $item['start'], 'end' => $item['end'], 'count' => $item['count'], 'party' => $item['party']];
                    }

                    $i++;
                }

                if (0 === $len) {
                    $output[] = ['type' => 'responder', 'action' => [$item['action']], 'start' => $item['start'], 'end' => $item['end'], 'count' => $item['count'], 'party' => $item['party']];
                }

                return $output;
            }, []);

        return empty($responders) ? $queries : array_merge($queries, $responders);
    }


    private function mapDataToColumns($columns, $row) {
        $output = [];

        foreach ($columns as $id=>$column) {
            $output[$column] = $row->$column ?: '';
        }

        return $output;
    }


    private function batch($row) {
        if ($this->rowCount >= $this->insertThreshold) {
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
        $this->cache2 = $this->cache1;
        $this->cache1 = [];
    }


    private function clear() {
        $this->rows = [];
        $this->rowCount = 0;
    }


    private function isUnique($field, $value) {
        return !isset($this->cache1[$value]) && !isset($this->cache2[$value]) && $this->baseTableService->isUnique($field, $value); 
    }

    private function saveToCache($value) {
        $this->cache1[$value] = 1;
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

        $clients = $currentProfile->clients()->pluck('id');
        if ($clients->count() > 0) {
            $this->profileRepo->assignClients($copyProfile->id, $clients->toArray());
        }

        $feedGroups = $currentProfile->feedGroups()->pluck('id');
        if ($feedGroups->count() > 0) {
            $this->profileRepo->assignFeedGroups($copyProfile->id, $feedGroups->toArray());
        }

        $offerActions = $currentProfile->offerAction()->pluck('id');
        if ($offerActions->count() > 0) {
            $this->profileRepo->assignOfferActions($copyProfile->id, $offerActions->toArray());
        }

        $offersSuppressed = $currentProfile->offerSuppression()->pluck('id');
        if ($offersSuppressed->count() > 0) {
            $this->profileRepo->assignOfferSuppression($copyProfile->id, $offersSuppressed->toArray());
        }

        $verticals = $currentProfile->verticals()->pluck('id');
        if ($verticals->count() > 0) {
            $this->profileRepo->assignVerticals($copyProfile->id, $verticals->toArray());
        }

        $copyProfile->columns = $currentProfile->columns;
        $copyProfile->save();

        return $copyProfile->id;
    }
}
