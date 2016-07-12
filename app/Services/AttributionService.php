<?php

namespace App\Services;


class AttributionService
{
    /**
     * @var EspRepo
     */
    protected $espRepo;

    /**
     * AttributionService constructor.
     * @param EspApiRepo $espRepo
     */
    public function __construct() {
        
    }

    public function getTransientRecords(AttributionRecordTruthRepo $sourceRepo) {
        return $sourceRepo->getTransientRecords();
    }

    public function getPotentialReplacements($emailId) {

        $union = $record->email()
                ->emailClientInstances()
                ->select()
                ->where('capture_date', '>', $beginDate)
                ->orderBy('capture_date');

        $potentialReplacements = $record->email()
                                        ->emailClientInstances()
                                        ->select('client_id', 'level')
                                        ->join('attribution_levels') // need attribution levels
                                        ->where('capture_date', $beginDate)
                                        ->where('client_id', '<>', $clientId)
                                        ->orderBy('capture_date')
                                        ->union($union)
                                        ->get();

        /***
            from transient records, we need
            email_id
            client_id
            client attribution level
            capture date
            recent_import
            has_action
            action_expired



            from the potential replacements, we need
            
            client_id
            client_attribution_level
            capture_date

            // WAIT - some records are EXEMPT from attribution. We need to know how that works.

        */

        return $potentialReplacements;

    }

    /**
     *  Method changesAttribution()
     *  @return Boolean
     *  Should attribution be changed or not?
     */

    public function changesAttribution($captureDate, $hasAction, $actionExpired, $currentAttrLevel, $testAttrLevel) {
        $importExpirationDateRange = 10;
        $tMinusTenDay = Carbon::today()->subDay($importExpirationDateRange);

        if ($tMinusTenDay->gte($captureDate)) {
            // needs to be explicitly checked - we don't just have the query to watch this

            if ($hasAction && $actionExpired) {
                return true
            }
            elseif (!$hasAction && $testAttrLevel < $currentAttrLevel) {
                // "less than" here means "has a higher attribution level"
                return true
            }
        } 

        return false;
    }

    public function changeAttribution($emailId, $clientId) {
        // update email_client_assignment
        // update schedule tables
        // update truth table
    }
}