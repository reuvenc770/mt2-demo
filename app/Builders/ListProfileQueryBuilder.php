<?php

namespace App\Builders;

use App\Exceptions\ValidationException;
use DB;
use Carbon\Carbon;

class ListProfileQueryBuilder {

    private $columnMapping; 

    private $mainTableAlias = 'flat';
    private $dataTable = 'record_data';

    private $attributionSchema;
    private $dataSchema;
    private $suppressionSchema;

    private $feedIds;
    private $emailDomainIds;
    private $offerIds;
    private $columns;
    private $recordDataColumns;
    private $attributionColumns;
    private $domainGroupColumns;
    private $feedColumns;
    private $clientColumns;
    private $emailColumns;
    private $ageAttributes;
    private $genderAttributes;
    private $zipAttributes;
    private $cityAttributes;
    private $stateAttributes;
    private $deviceAttributes;
    private $carrierAttributes;
    private $feedsWithSuppression;

    // Note already-prepared fields in ListProfileBaseTableService
    const REQUIRED_PROFILE_FIELDS = ['email_id', 'email_address', 'lower_case_md5', 'upper_case_md5'];


    public function __construct() {

        $this->columnMapping = [
            'email_id' => DB::connection('redshift')->raw('e.id as email_id'),
            'first_name' => 'rd.first_name',
            'last_name' => 'rd.last_name',
            'gender' => 'rd.gender',
            'address' => 'rd.address',
            'address2' => 'rd.address2',
            'city' => 'rd.city',
            'state' => 'rd.state',
            'zip' => 'rd.zip',
            'dob' => 'rd.dob',
            'age' => DB::connection('redshift')->raw("(current_date - rd.dob) / 365 as age"),
            'phone' => 'rd.phone',
            'ip' => 'rd.ip',
            'subscribe_date' => 'rd.subscribe_date',
            'feed_id' => 'efa.feed_id',
            'domain_group_name' => DB::connection('redshift')->raw('dg.name as domain_group_name'), 
            'country' => 'dg.country',
            'feed_name' => DB::connection('redshift')->raw('f.short_name as feed_name'), 
            'source_url' => 'rd.source_url',
            'client_name' => DB::connection('redshift')->raw('c.name as client_name'),
            'email_address' => 'e.email_address', 
            'lower_case_md5' => 'e.lower_case_md5', 
            'upper_case_md5' => 'e.upper_case_md5',
            'party' => 'f.party',
            'capture_date' => 'rd.capture_date',
            'device_type' => 'rd.device_type',
            'device_name' => 'rd.device_name',
            'carrier' => 'rd.carrier'
        ];
    }
    
    public function buildQuery($listProfile, $queryData) {
        $this->setValues($listProfile);

        if ('deliverable' === $queryData['type']) {
            $query = $this->buildDeliverableQuery($queryData);
        }
        else {
            $query = $this->buildNonDeliverableQuery($queryData);
        }

        $query = $this->buildSuppression($listProfile, $query);
        $query = $this->buildFeedSearch($query);
        $query = $this->addConditionsAndJoins($listProfile, $query);
        $query = $this->buildSelects($query);

        return $query;
    }


    private function setValues($listProfile) {
        // In the name of efficiency ... 

        if (empty($this->columns)) {
            // We can add to selected columns because the writer will only use the selected columns
            $declaredColumns = json_decode($listProfile->columns, true);
            $this->columns = array_unique(array_merge(self::REQUIRED_PROFILE_FIELDS, $declaredColumns));
            
        }

        if (sizeof($this->columns) === 0) { 
            throw new ValidationException("No columns selected"); 
        }


        if (empty($this->feedIds)) {
            $this->feedIds = $this->getFeedIds($listProfile);
        }
        if (empty($this->emailDomainIds)) {
            $this->emailDomainIds = $this->getEmailDomainIds($listProfile);
        }
        if (empty($this->offerIds)) {
            $this->offerIds = $this->getofferIds($listProfile);
        }
        if (empty($this->feedsWithSuppression)) {
            $tmp = $this->getFeedsWithIneligibleEmails($listProfile);
            $this->feedsWithSuppression = $tmp ?: [];
        }


        // Setting up columns for selects

        if (empty($this->recordDataColumns)) {
            $this->recordDataColumns = array_intersect(['first_name', 'last_name', 'gender', 'address', 'address2', 
                'city', 'state', 'zip', 'dob', 'age', 'phone', 'ip', 'subscribe_date', 'source_url', 'capture_date',
                'device_type', 'device_name', 'carrier'], $this->columns);
        }
        if (empty($this->attributionColumns)) {
            $this->attributionColumns = array_intersect(['feed_id'], $this->columns);
        }
        if (empty($this->domainGroupColumns)) {
            $this->domainGroupColumns = array_intersect(['domain_group_name', 'country'], $this->columns);
        }
        if (empty($this->feedColumns)) {
            $this->feedColumns = array_intersect(['feed_name', 'party'], $this->columns);
        }
        if (empty($this->clientColumns)) {
            $this->clientColumns = array_intersect(['client_name'], $this->columns);
        }
        if (empty($this->emailColumns)) {
            $this->emailColumns = array_intersect(['email_id', 'email_address', 'lower_case_md5', 'upper_case_md5'], $this->columns);
        }

        // Attributes

        if (empty($this->ageAttributes)) {
            $tmpAgeData = json_decode($listProfile->age_range, true);
            if (isset($tmpAgeData['max']) && (int)$tmpAgeData['max'] > 0 ) { # required for a changed age range
                $this->ageAttributes = $tmpAgeData;
            }
            else {
                $this->ageAttributes = null;
            }     
        }
        if (empty($this->genderAttributes)) {
            $this->genderAttributes = json_decode($listProfile->gender, true);
        }
        if (empty($this->zipAttributes)) {
            $this->zipAttributes = json_decode($listProfile->zip, true);
        }
        if (empty($this->cityAttributes)) {
            $this->cityAttributes = json_decode($listProfile->city, true);
        }
        if (empty($this->stateAttributes)) {
            $this->stateAttributes = json_decode($listProfile->state, true);
        }
        if (empty($this->deviceAttributes)) {
            $this->deviceAttributes = json_decode($listProfile->device_type, true);
        }
        if (empty($this->deviceOsAttributes)) {
            $this->deviceOsAttributes = json_decode($listProfile->device_os, true);
        }
        if (empty($this->carrierAttributes)) {
            $this->carrierAttributes = json_decode($listProfile->mobile_carrier, true);
        }
    }


    private function buildDeliverableQuery($queryData) {
        $start = $queryData['start'];
        $end = $queryData['end'];

        $this->mainTableAlias = 'rd';

        if (1 === $queryData['party']) {
            $this->dataTable = 'first_party_record_data';
        }
        else {
            // Currently 3
            $this->dataTable = 'record_data';
        }

        $query = DB::connection('redshift')->table("{$this->dataTable} as rd")
                    ->whereRaw('is_deliverable = 1')
                    ->whereRaw("subscribe_date BETWEEN current_date - INTERVAL '$end DAY' AND current_date - INTERVAL '$start DAY'");

        return $query;
    }


    /**
     *  Builds a subquery that reduces the size of the flat table.
     *  Method is a little ugly, but laravel offers no other way to do this.
     */

    private function buildNonDeliverableQuery($queryData) {
        $type = $queryData['type'];
        $count = $queryData['count'];
        $end = $queryData['end'];
        $start = $queryData['start'];

        $query = DB::connection('redshift')->table("list_profile_flat_table")->select('email_id')
                    ->groupBy('email_id')
                    ->whereRaw("date BETWEEN current_date - INTERVAL '$end DAY' AND current_date - INTERVAL '$start DAY'");

        $query = sizeof($this->emailDomainIds) > 0 ? $query->whereRaw('email_domain_id IN (' . implode(',', $this->emailDomainIds) . ')') : $query;
        $query = sizeof($this->offerIds) > 0 ? $query->whereRaw('offer_id IN (' . implode(',', $this->offerIds) . ')') : $query; 

        $query = $query->havingRaw("SUM($type) >= $count")->toSql();

        $query = DB::connection('redshift')->table(DB::connection('redshift')->raw('(' . $query . ') as flat'));

        $this->mainTableAlias = 'flat';

        return $query;
    }


    private function buildSuppression($listProfile, $query) {

        $listIds = $this->getFeedSuppressionListIds($listProfile);

        if ($listProfile->use_global_suppression || count($listIds) > 0) {
            $query = $query->join("emails as e", "{$this->mainTableAlias}.email_id", '=', 'e.id');

            if ($listProfile->use_global_suppression) {
                $query = $query->leftJoin("suppression_global_orange as s", 'e.email_address', '=', 's.email_address')->where('s.email_address', null);
            }

            if (count($listIds) > 0) {
                $listIds = '(' . implode(',', $listIds) . ')';
                $query = $query->leftJoin("suppression_list_suppressions as sls", function($join) use ($listIds) {
                    $join->on("e.email_address", '=', 'sls.email_address');
                    $join->on('sls.suppression_list_id', 'in', DB::connection('redshift')->raw($listIds));
                })  
                ->whereNull('sls.email_address');
            }
        }

        return $query;
    }

    private function buildFeedSearch($query) {
        if (sizeof($this->feedIds) > 0 
            || $this->attributionColumns 
            || $this->feedColumns 
            || $this->clientColumns) {

            $query = $query->join("email_feed_assignments as efa", "{$this->mainTableAlias}.email_id", '=', 'efa.email_id');

            if ($this->feedColumns || $this->clientColumns) {
                $query = $query->join("feeds as f", 'efa.feed_id', '=', 'f.id');

                if ($this->clientColumns) {
                    $query = $query->join("clients as c", 'f.client_id', '=', 'c.id');
                }
            }

            $feedsWithoutIgnores = array_diff($this->feedIds, $this->feedsWithSuppression);
            $feedsWithIgnores = $this->feedsWithSuppression;

            if (sizeof($feedsWithoutIgnores) > 0 && sizeof($this->feedsWithSuppression) > 0) {
                // Get everything from the selected feeds, less those deliberately ignored

                $query = $query->join("email_feed_status as efs", function($join) {
                    $join->on('efa.feed_id', '=', 'efs.feed_id');
                    $join->on("{$this->mainTableAlias}.email_id", '=', 'efs.email_id');
                })->where(function ($q) use ($feedsWithIgnores) {
                    $q->whereIn('efs.feed_id', $feedsWithIgnores)->where('efs.status', 'Active');
                })->orWhere(function ($q) use ($feedsWithoutIgnores) {
                    $q->whereIn('efs.feed_id', $feedsWithoutIgnores);
                });
            }
            elseif (sizeof($feedsWithoutIgnores) > 0) {
                // Get everything from the selected feeds - no ignores required
                $query = $query->whereRaw('efa.feed_id IN (' . implode(',', $feedsWithoutIgnores) . ')');
            }
            elseif (sizeof($this->feedsWithSuppression) > 0) {
                // Get data from all feeds, except those emails ignored for these
                $query = $query->join("email_feed_status as efs", function($join) {
                    $join->on('efa.feed_id', '=', 'efs.feed_id');
                    $join->on("{$this->mainTableAlias}.email_id", '=', 'efs.email_id');
                })->whereNotIn('efs.feed_id', $feedsWithIgnores)
                  ->orWhere(function ($q) use ($feedsWithIgnores) {
                    $q->whereIn('efs.feed_id', $feedsWithIgnores)->where('efs.status', 'Active');
                });
            }
            else {
                // Get everything - no conditions
            }
        }

        return $query;
    }


    private function addConditionsAndJoins($listProfile, $query) {
        // Would have loved to make this neater, but it's all unique logic

        if ($this->recordDataColumns 
            || $this->ageAttributes 
            || $this->genderAttributes 
            || $this->zipAttributes 
            || $this->cityAttributes 
            || $this->stateAttributes 
            || $this->deviceAttributes 
            || $this->carrierAttributes) {
            
            if ('rd' !== $this->mainTableAlias) {
                // make sure we don't join on itself
                $query = $query->join("{$this->dataTable} as rd", "{$this->mainTableAlias}.email_id", '=', 'rd.email_id');
            }
            
            $query = $this->buildAgeAttributes($query, $this->ageAttributes);
            $query = $this->buildAttributes($query, 'gender', $this->genderAttributes);
            $query = $this->buildAttributes($query, 'zip', $this->zipAttributes);
            $query = $this->buildAttributes($query, 'city', $this->cityAttributes);
            $query = $this->buildAttributes($query, 'state', $this->stateAttributes);
            $query = $this->buildAttributes($query, 'device_type', $this->deviceAttributes);
            $query = $this->buildAttributes($query, 'device_os', $this->deviceOsAttributes);
            $query = $this->buildAttributes($query, 'carrier', $this->carrierAttributes);
        }

        if ($this->emailColumns || $this->domainGroupColumns || ('rd' === $this->mainTableAlias && count($this->emailDomainIds) > 0) ) {

            if (0 === $listProfile->use_global_suppression) {
                // Don't want to join on this table twice.
                $query = $query->join("emails as e", "{$this->mainTableAlias}.email_id", '=', 'e.id');
            }

            if ('rd' === $this->mainTableAlias && count($this->emailDomainIds) > 0) {
                // Adding condition for deliverable queries. Non-deliverable queries already have this.
                // The join has already been handled as well either above or with suppression
                $query = $query->whereRaw('e.email_domain_id in (' . implode(',', $this->emailDomainIds) . ')');
            }
            
            if ($this->domainGroupColumns) {
                $query = $query->join("email_domains as ed", 'e.email_domain_id', '=', 'ed.id')
                               ->join("domain_groups as dg", 'ed.domain_group_id', '=', 'dg.id');
            }
        }

        return $query;
    }


    private function getFeedIds($listProfile) {
        $feedIds = [];

        foreach ($listProfile->clients as $client) {
            foreach ($client->feeds as $feed) {
                $feedIds[] = $feed->id;
            }
        }

        foreach ($listProfile->feedGroups as $feedGroup) {
            foreach ($feedGroup->feeds as $feed) {
                $feedIds[] = $feed->id;
            }
        }

        foreach ($listProfile->feeds as $feed) {
            $feedIds[] = $feed->id;
        }

        return $feedIds;
    }


    private function getEmailDomainIds($listProfile) {
        $emailDomainIds = [];

        foreach ($listProfile->domainGroups as $domainGroup) {
            foreach ($domainGroup->domains as $domain) {
                $emailDomainIds[] = $domain->id;
            }
        }

        return $emailDomainIds;
    }


    private function getOfferIds($listProfile) {
        $offerIds = [];

        foreach($listProfile->offers as $offer) {
            $offerIds[] = $offer->id;
        }

        foreach ($listProfile->verticals as $vertical) {
            foreach ($vertical->offers as $offer) {
                $offerIds[] = $offer->id;
            }
        }

        return $offerIds;
    }


    private function getFeedsWithIneligibleEmails($listProfile) {
        return json_decode($listProfile->feeds_suppressed, true);
    }


    private function buildSelects($query) {
        $selects = [];
        foreach($this->columns as $column) {
            if (isset($this->columnMapping[$column])) {
                $selects[] = $this->columnMapping[$column];
            }
        }

        return $query->select($selects);
    }


    /**
     *  The following two methods assume an original json structure {"include": [], "exclude": []}
     */

    private function extractIncludes($assoc) {
        return isset($assoc["include"]) ? $assoc['include'] : [];
    }


    private function extractExcludes($assoc) {
        return isset($assoc["exclude"]) ? $assoc['exclude'] : [];
    }


    private function buildAttributes($query, $field, $attributes) {
        if ($attributes) {
            $include = $this->extractIncludes($attributes);
            $exclude = $this->extractExcludes($attributes);

            if (sizeof($include) > 0) {
                $query = $query->whereIn($field, $include);
            }
            if (sizeof($exclude) > 0) {
                $query = $query->whereNotIn($field, $exclude);
            }
        }

        return $query;
    }


    // Assuming an original json structure of {"min": #, "max": #, "unknown": BOOL}
    private function buildAgeAttributes($query, $attributes) {
        $field = 'dob';

        if ($attributes) {
            $youngest = Carbon::now()->subYears($attributes['min'])->format('Y-m-d');
            $oldest = Carbon::now()->subYears($attributes['max'])->format('Y-m-d');
            
            if ($attributes['unknown']) {
                $query = $query->whereRaw("(($field BETWEEN '$oldest' and '$youngest') OR $field IS NULL)");
            }
            else {
                $query = $query->whereRaw("$field BETWEEN '$oldest' and '$youngest'");
            }
        }

        return $query;

    }

    private function getFeedSuppressionListIds($listProfile) {
        $listIds = [];

        foreach ($listProfile->clients as $client) {
            foreach ($client->feeds as $feed) {
                if ($feed->suppression_list_id) {
                    $listIds[] = $feed->suppression_list_id;
                }
               
            }
        }

        foreach ($listProfile->feeds as $feed) {
            if ($feed->suppression_list_id) {
                $listIds[] = $feed->suppression_list_id;
            }
        }

        return $listIds;
    }


    // We occasionally need to access columns
    public function getColumns() {
        return $this->columns;
    }

    public function getFeeds() {
        return $this->feedIds;
    }
}
