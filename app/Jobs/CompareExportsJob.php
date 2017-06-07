<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Factories\DataProcessingFactory;
use Mail;
use DB;
use Carbon\Carbon;
use App;
use Log;
use Storage;
use File;

class CompareExportsJob extends SafeJob {

    const JOB_NAME = 'CompareMt1Cmp';
    const MT1_TABLE = 'mt1_table';
    const CMP_TABLE = 'cmp_table';
    private $mt1FileName;
    private $cmpFileName;
    private $recordDataRepo;
    private $userActionRepo;
    private $assignmentRepo;

    public function __construct($mt1FileName, $cmpFileName, $tracking) {
	$this->mt1FileName = $mt1FileName;
	$this->cmpFileName = $cmpFileName;
        parent::__construct(self::JOB_NAME, $tracking);
    }

    protected function handleJob() {
        $this->buildTable($this->mt1FileName, self::MT1_TABLE);
        $this->buildTable($this->cmpFileName, self::CMP_TABLE);
        $sharedCount = $this->getSharedCount(self::MT1_TABLE, self::CMP_TABLE);

        $this->recordDataRepo = App::make(\App\Repositories\EmailAttributableFeedLatestDataRepo::class);
        $this->userActionRepo = App::make(\App\Repositories\ThirdPartyEmailStatusRepo::class);
        $this->assignmentRepo = App::make(\App\Repositories\EmailFeedAssignmentRepo::class);

        /**
            Part 1: Why are some records in MT1's file and not CMP's?
        */
        $inMt1Only = $this->getLeftJoinCursor(self::MT1_TABLE, self::CMP_TABLE); 

        $sameFeedButCmpAction = 0;
        $sameFeedButNoActionUnexplainable = 0;
        $diffFeedCmpActionPreventedAttr = 0;
        $legacyDataIssue = 0;
        $notEvenRaw = 0;
        $diffFeedUnexplainable = 0;
        $cmpInvalidatedMt1DidNot = 0;
        $cmpNotInvalidButNotAnEmail = 0;
        $unprocessedInCmp = 0;
        $doNotHaveFeedInstance = 0;
        $attributionMystery = 0;
        $raceCondition = 0;
        $noCmpAttribution = 0;
        $noStage2 = 0;

        $mt1TotalCount = $this->getTotalCount(self::MT1_TABLE);
        $mt1Count = 0;

        $checkList = [];
echo "PROCESSING MT1 RECORDS" . PHP_EOL;
        foreach ($inMt1Only as $record) {
            $comment = " 
            1. How many are attributed to the same feed(s)?
                 a. Of these, how many are no longer deliverable in CMP?                                                                        -> (lookup to third_party_email_statuses)
                 Remainder are problematic. What's the count? Were they not processed? Did they change attribution very recently?
            
            2. The rest are by definition attributed to other feeds. Why are they attributed differently?
                a. Do we have an action for them in CMP recently that made them responders and thus unattributable?                             -> (lookup to third_party_email_statuses)
                b. We didn't process this record because of limitations to our test                                                             -> compare the mapping table with raw_feed_emails and the actions
                c. Was there (this is harder to examine) an extreme corner case that looks like the following:
                    - In the MT1 legacy data, there was a record with an action that we did not get in hard start.
                    - There was a subsequent import seen by both MT1 and CMPTE.
                    - In MT1, the previous step did not lead to a re-attribution because of step 1, but did in CMP.
                    - Finally, the record comes in a third time and is ready to be freed from the first attribution in MT1, but not from the new attribution in CMP
            
            3. A subset of records might have had timing issues with suppression
            ";
            
            $mt1Count++;
            $mt1LastActionTime = $this->getMt1LastActionTime($record->email_address);

            $localEmailId = $this->getLocalEmailId($record->email_address);
            
            if ($localEmailId) {
                $cmpLastActionDate = $this->userActionRepo->getLastActionTime($localEmailId);
                $cmpSubscribeDate = $this->getAttributedData($localEmailId);
                $cmpFeedId = $this->getAssignedFeed($localEmailId) ?: null; 
            }
            
            // MT1 and CMP differ on attribution
            if (is_null($localEmailId)) {
                // Are there reasons? This should check the emails table
                // 1. Check the raw table
                if ($this->existsInRaw($record->email_address, $record->feed_id)) {
                    // Check if it was invalidated for some reason in invalid_feed_emails
                    if ($this->cmpInvalidated($record->email_address, $record->feed_id)) {
                        $cmpInvalidatedMt1DidNot++;
                    }
                    
                    // An odd case. Exists in the raw table. Was not invalidated. Yet does not exist in `emails`
                    // Perhaps an indication of a truly serious processing delay?
                    else {
                        $unprocessedInCmp++;
                    } 
                }
                else {
                    // Note - these might not be benign. They might be due to bugs in the ingestion system
                    $notEvenRaw++;
                }
                
            }
            else {
                if ($record->feed_id === $cmpFeedId) {
                    if ($cmpLastActionDate) {
                        // This user has an action by us. This is fine.
                        $sameFeedButCmpAction++;
                    }
                    else {
                        // No action and attributed to the same feed. This is not fine, unless we have (small) timing issue with suppression
                        // Might indicate processing problems with redshift, among other things 
                        $sameFeedButNoActionUnexplainable++;
                        if ($sameFeedButNoActionUnexplainable < 300) {
                            print "unexplained: " . $record->email_address . PHP_EOL;
                        }
                    }
                }
                else {
                    if (is_null($cmpFeedId)) {
                        // A possible processing problem
                        if (is_null($cmpSubscribeDate)) {
                            $noStage2++;
                        }

                        else {
                            $noCmpAttribution++;
                        }
                    }
                    elseif ($this->doNotHaveFeedInstance($localEmailId, $record->feed_id)) {
                        // Another possible processing problem
                        $doNotHaveFeedInstance++;
                    }
                    elseif (is_null($cmpLastActionDate)) {
                        // A tricky case. Both MT1 and CMP agree that no action took place, but their attribution differs nonetheless.
                        
                        // What different cases can we see here?
                        // We know that a feed instance came in because we passed the previous test
                        if ($this->attributionRaceConditionOccurred($localEmailId, $record->feed_id, $cmpFeedId)) {
                            $raceCondition++;
                        }
                        else {
                            $attributionMystery++; 
                        }
                    }
                    elseif (!is_null($cmpLastActionDate) && $cmpLastActionDate < $record->subscribe_date) { # maybe not this field? Depends if Jim updates or not.
                        // There's been a recent action that only CMP saw that prevented an attribution that MT1 used
                        $diffFeedCmpActionPreventedAttr++;
                    }
                    else {
                        // Not fine
                        // Might indicate a redshift upload problem, among other things
                        $diffFeedUnexplainable++;
                    }
                }
                
            }
        }
echo "PROCESSING CMP RECORDS" . PHP_EOL;
        /**
            Part 2: Why are certain records in CMP's file and not MT1's?
        */
        $cmpDeliverableButMt1HasAction = 0;
        $cmpActionYetDeliverable = 0;
        $mt1IncorrectlySkips = 0;
        $legacyDataIssue_CmpHasRecord = 0;
        $cmpDidNotReceiveNewRecord = 0;
        $cmpInvalidatedRecord = 0;
        $unexplainedInCmpOnly = 0;
        $inCmpOnly = $this->getLeftJoinCursor(self::CMP_TABLE, self::MT1_TABLE); 
        $cmpCount = 0;
        $cmpTotalCount = $this->getTotalCount(self::CMP_TABLE);
        $mt1DidNotProcess = 0;
        $unexplainedInCmpOnlySameFeed = 0;
        
        foreach($inCmpOnly as $record) {
            /*
            II. Figure out why certain records are only in the CMP file and not the MT1 file
            
            1. How many records are attributed to the same feed?
                a. Is there an action in MT1 (perhaps legacy, perhaps in a non-tracked ESP account) that's not in CMP?
                b. 
            2. What about the ones attributed to other feeds?
                a. Is there legacy action data in MT1 that prevented an attribution there?
                Remainder are problematic
            */
            $cmpCount++;
            $mappedRecord = $this->getMappedRecord($record->email_address);

            if ($mappedRecord) {
                $mt1CurrentFeed = $mappedRecord->mt1_feed_id; // These are safely nullable
                $mt1LastAction = $mappedRecord->last_mt1_action;
                $cmpLastAction = $this->userActionRepo->getLastActionTime($record->email_id);
                
                if ($record->feed_id === $mt1CurrentFeed) {
                    if (!is_null($mt1LastAction) && is_null($cmpLastAction)) {
                        $cmpDeliverableButMt1HasAction++;
                    }
                    elseif (!is_null($cmpLastAction)) {
                        // Cmp somehow has set this to deliverable despite it having an action - maybe check the time of these actions
                        $cmpActionYetDeliverable++;
                    }
                    elseif (is_null($mt1LastAction)) {
                        // cmp action is null implied
                        // both have no action, they are attributed to the same feed ... CMP has the record, but MT1 does not
                        
                        // THIS MIGHT BE AN INCORRECT DESCRIPTION - WE MIGHT BE MISSING MT1 ACTIONS SOMEHOW
                        // WE NEED TO CHECK SUBSCRIBE_DATES AS WELL - DO THEY MATCH UP?
                        $mt1IncorrectlySkips++;
                        if ($mt1IncorrectlySkips < 300) {
                            echo "mt1-skips: {$record->email_address}" . PHP_EOL;
                        }
                    }
                    else {
                        $unexplainedInCmpOnlySameFeed++;
                    }
                }
                else {
                    // The two have different feeds
                    if (!is_null($mt1LastAction) && $record->subscribe_date > $mt1LastAction) {
                        // What likely happened was that Mt1 had an action, Cmp did not, and we attributed the correct b/c we didn't have legacy data
                        $legacyDataIssue_CmpHasRecord++;
                    }
                    elseif (!$this->existsInRaw($record->email_address, $mt1CurrentFeed)) {
                        // Record came in to MT1 and not CMPTE during this time (say, via batch)
                        $cmpDidNotReceiveNewRecord++;
                    }
                    elseif ($this->cmpInvalidated($record->email_address, $mt1CurrentFeed)) {
                        // We invalidated the record and MT1 did not
                        $cmpInvalidatedRecord++;
                    }
                    else {
                        // These are unexplained
                        $unexplainedInCmpOnly++;
                    }
                }
                
            }
            else {
                $mt1DidNotProcess++;
            }
        }
        
        $message = <<<TXT
MT1 file total count: $mt1TotalCount
CMP file total count: $cmpTotalCount

Shared records: $sharedCount

For records only in MT1 (out of $mt1Count):
$sameFeedButCmpAction share a feed but have an action in CMP and are thus not deliverable. (same_feed_but_cmp_action.csv)
$diffFeedCmpActionPreventedAttr differ in terms of attribution because CMP had an action earlier that prevented attribution. (cmp_action_prevented_attribution.csv)
$cmpInvalidatedMt1DidNot are missing from CMP because they were invalidated by CMP's processing rules (cmp_invalidated_records.csv)
$unprocessedInCmp were ingested by CMP but not processed and are not invalid. (cmp_unprocessed.csv)
$notEvenRaw never appeared in CMP in any form (not_even_raw.csv)
$doNotHaveFeedInstance did not have feed instances for this feed id (no_feed_instances.csv)
$raceCondition differ because the order of ingestion differed between MT1 and CMP (race_condition.csv)
$noStage2 do not have a row in stage 2 email tables - tpes, eafld
$noCmpAttribution do not have attribution in CMP despite appearing in other parts of the system (no_cmp_attribution.csv)

$attributionMystery are a mystery case - different attribution, deliverable, with no current obvious issues (other.csv)
$sameFeedButNoActionUnexplainable records had the same attributed feed in MT1 and CMP but could not be explained by the above
$diffFeedUnexplainable records differed in terms of feeds but cannot be explained by the above


For records only in CMP (out of $cmpCount):
$cmpDeliverableButMt1HasAction are deliverable in CMP but have an action in MT1 and are thus not deliverable (cmp_deliverable_mt1_action.csv)
$mt1IncorrectlySkips are incorrectly classified as responders in MT1 (mt1_incorrect_responders.csv)
$legacyDataIssue_CmpHasRecord differ due to a lack of legacy data in CMP. A record was attributed in CMP (and became deliverable) because we did not have an action to block attribution. (cmp_legacy_data_issue.csv)
$cmpDidNotReceiveNewRecord caused by CMP not receiving a record (that MT1 did) that would have taken it away from this list
$mt1DidNotProcess were not processed by MT1

$unexplainedInCmpOnly unexplained with different feeds (in_cmp_only_unexplained.csv)
$unexplainedInCmpOnlySameFeed unexplained with the same feed (in_cmp_only_unexplained_same_feed.csv)
$cmpActionYetDeliverable records were mysteriously set to deliverable in CMP despite having an action (cmp_mistakenly_deliverable.csv)
TXT;
        echo $message;
        Mail::raw($message, function ($m) {
            $m->subject("List Profile Comparison Results");
            $m->priority(1);
            $m->to("rbertorelli@zetaglobal.com");
        });
    
    }


    protected function buildTable($fileName, $tableName) {
        /*
            Required file format: email_address, email_id, subscribe_date, feed_id ... whatever else
            and no headers
        */
        $tableName = 'mt2_temp_tables.' . $tableName;
        DB::table($tableName)->truncate();
        
        $host = config('filesystems.disks.SystemFtp.host');
        $user = config('filesystems.disks.SystemFtp.username');
        $pw = config('filesystems.disks.SystemFtp.password');
        $conn = ftp_connect($host);

        ftp_login($conn, $user, $pw);
        ftp_pasv($conn, true);
        $localFile = fopen("/var/www/html/cmp/storage/app/tmp/".$fileName, 'w');
        
        ftp_fget($conn, $localFile, 'mt1_cmp_lp_comparisons/'.$fileName, FTP_BINARY, 0);
        fclose($localFile);
        $f = fopen("/var/www/html/cmp/storage/app/tmp/".$fileName, 'r');
         
        while ($row = fgetcsv($f)) {
            $emailAddress = $row[0];
            $emailId = $row[1];
            $feedId = $row[3];
            $subscribeDate = $row[2];
            
            DB::table($tableName)->insert([
                'email_address' => $emailAddress,
                'email_id' => $emailId,
                'feed_id' => $feedId,
                'subscribe_date' => $subscribeDate
            ]);
        }

        fclose($f);
        unlink("/var/www/html/cmp/storage/app/tmp/".$fileName);
        Storage::disk('SystemFtp')->delete("mt1_cmp_lp_comparisons/$fileName");
    }


    protected function getTotalCount($table) {
        return DB::table('mt2_temp_tables.'.$table)->count();
    }

    protected function getLeftJoinCursor($leftTable, $rightTable) {
        $leftTable = 'mt2_temp_tables.' . $leftTable;
        $rightTable = 'mt2_temp_tables.' . $rightTable;

        return DB::table("$leftTable as l")->leftJoin("$rightTable as r", 'l.email_address', '=', 'r.email_address')->whereRaw("r.email_address is null")->select('l.*')->cursor();
    }

    protected function getSharedCount($mt1Table, $cmpTable) {
        return DB::table('mt2_temp_tables.'.$mt1Table.' as mt1')->join('mt2_temp_tables.'.$cmpTable.' as cmp', 'mt1.email_address', '=', 'cmp.email_address')->count();
    }

    protected function getMt1LastActionTime($emailAddress) {
        $record = DB::table('mt2_shuttle.mt1_cmp_attribution_map')->where('email_address', $emailAddress)->first();
        if ($record) {
            return $record->last_mt1_action;
        }
        else {
            return null;
        }
    }

    protected function getLocalEmailId($emailAddress) {
        $record = DB::table('emails')->where('email_address', $emailAddress)->first();
        if ($record) {
            return $record->id;
        }
        else {
            return null;
        }
    }

    protected function getAttributedData($emailId) {
        $record = $this->recordDataRepo->getRecordDataFromEid($emailId);
        
        if ($record) {
            return $record->subscribe_date;
        }
        else {
            return null;
        }
    }

    protected function existsInRaw($emailAddress, $feedId) {
        return DB::table('raw_feed_emails')->where('email_address', $emailAddress)->whereRaw('feed_id =' . (int)$feedId)->count() > 0;
    }

    protected function cmpInvalidated($emailAddress, $feedId) {
        return DB::table('invalid_email_instances')->where('email_address', $emailAddress)->whereRaw('feed_id =' . (int)$feedId)->count() > 0;
    }

    protected function getMt1Attribution($emailAddress) {
        $record = DB::table('mt2_shuttle.mt1_cmp_attribution_map')->where('email_address', $emailAddress)->first();
        
        if ($record) {
            return $record->mt1_feed_id;
        }
        else {
            return null;
        }
    }

    protected function getMappedRecord($emailAddress) {
        return DB::table('mt2_shuttle.mt1_cmp_attribution_map')->where('email_address',  $emailAddress)->first();
    }

    protected function doNotHaveFeedInstance($emailId, $feedId) {
        return DB::table('mt2_data.email_feed_instances')->where('email_id', $emailId)->where('feed_id', $feedId)->count() === 0;
    }

    protected function attributionRaceConditionOccurred($emailId, $potentialLosingFeedId, $potentialWinningFeedId) {
        $minPotentialLosingId = DB::table('mt2_data.email_feed_instances')->where('email_id', $emailId)->where('feed_id', $potentialLosingFeedId)->min('id');
        $minPotentialWinningFeedId =DB::table('mt2_data.email_feed_instances')->where('email_id', $emailId)->where('feed_id', $potentialWinningFeedId)->min('id');

        return $minPotentialWinningFeedId < $minPotentialLosingId;
    }

    protected function getAssignedFeed($emailId) {
        $record = DB::table('attribution.email_feed_assignments')->where('email_id', $emailId)->first();
        
        if ($record) {
            return $record->feed_id;
        }
        else {
            return null;
        }
    }
}
