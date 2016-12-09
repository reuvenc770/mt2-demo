<?php

namespace App\Repositories;

use App\Models\Email;
#use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use DB;
use App\Repositories\RepoInterfaces\Mt2Export;

/**
 *
 */
class EmailRepo implements Mt2Export {

    private $emailModel;
    private $batchEmails = [];
    private $batchEmailCount = 0;
    const INSERT_THRESHOLD = 10000;

    public function __construct(Email $emailModel) {
        $this->emailModel = $emailModel;
    }

    public function getEmailId($emailAddress) {
        return $this->emailModel->select( 'id' )->where( 'email_address' , $emailAddress )->get();
    }

    public function getEmaiAddress($eid) {
        return $this->emailModel->select( 'email_address' )->find($eid);
    }


    public function insertDelayedBatch($row) {
        if ($this->batchEmailCount >= self::INSERT_THRESHOLD) {
            $this->emailModel->insert($this->batchEmails);
            $this->batchEmails = [$this->createRow($row)];
            $this->batchEmailCount = 1;
        }
        else {
            $this->batchEmails[] = $this->createRow($row);
            $this->batchEmailCount++;
        }
    }

    public function insertStored() {
        $this->emailModel->insert($this->batchEmails);
        $this->batchEmails = [];
        $this->batchEmailCount = 0;
    }

    public function insertCopy($emailData) {
        DB::statement(
            "INSERT INTO emails (id, email_address, email_domain_id, lower_case_md5, upper_case_md5)
            VALUES(:id, :addr, :domain_id, :lower_md5, :upper_md5)
            ON DUPLICATE KEY UPDATE
            id = id,
            email_address=email_address,
            email_domain_id=email_domain_id,
            lower_case_md5=lower_case_md5,
            upper_case_md5 = upper_case_md5",
            array(
                ':id' => $emailData['id'],
                ':addr' => $emailData['email_address'],
                ':domain_id' => $emailData['email_domain_id'],
                ':lower_md5' => md5(strtolower($emailData['email_address'])),
                ':upper_md5' => md5(strtoupper($emailData['email_address']))
            )
        );
    }

    private function getAttributedFeedForAddress($emailAddr) {
        # TODO: flesh out attribution. This will return a feed_id
        return 1;
    }

    private function createRow($row) {
        return [
            'id' => $row['id'],
            'email_address' => $row['email_address'],
            'email_domain_id' => $row['email_domain_id'],
            'lower_case_md5' => md5(strtolower($row['email_address'])),
            'upper_case_md5' => md5(strtoupper($row['email_address']))
        ];
    }

    /**
     *  Returns attributed feed id for provided email id
     *  If nothing found, returns 0
     */

    public function getCurrentAttributedFeedId($emailId) {
        $assignment = $this->emailModel->find($emailId)->feedAssignment;
        if ($assignment) {
            return $assignment->feed_id;
        }
        else {
            return 0;
        } 
    }

    /**
     *  Returns the attribution level for the feed that the $emailId is currently associated to
     *  However, if no attribution level exists for that particular feed, return 1000 (a number far below any attr level)
     *  This last case should only be a temporary problem - we don't want this situation in MT2 (and neither do they)
     */

    public function getCurrentAttributionLevel($emailId) {
        $attributionSearchBase = $this->emailModel->find($emailId)->feedAssignment;

        if ($attributionSearchBase) {
            $feedSearch = $attributionSearchBase->feed;

            if ($feedSearch) {
                $attributionLevel = $feedSearch->attributionLevel;

                if ($attributionLevel) {
                    return $attributionLevel->level;
                }
                else {
                    return null;
                }
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }

                
    }


    public function getAttributionTruths($emailId) {
        $email = $this->emailModel->find($emailId);

        if ($email && $email->attributionTruths) {
            return $email->attributionTruths;
        }
        else {
            return 0;
        }
    }

    /**
     *  Returns boolean for "is a recent import?" for provided email id
     *  unless nothing found, in which case it returns 0 (so be sure to use ===)
     */

    public function isRecentImport($emailId) {
        $truth = $this->emailModel->find($emailId)->attributionTruths;
        if ($truth) {
            return ($truth->recent_import == 1);
        }
        else {
            return 0;
        }
    }


    public function hasActions($emailId) {
        return ($this->emailModel->find($emailId)->attributionTruths->has_action == 1);
    }

    public function getCaptureDate($emailId) {
        return $this->emailModel->find($emailId)->feedAssignment->capture_date;
    }

    // A temporary, necessary evil
    public function updateEmailId($oldEmailId, $newEmailId) {
        $this->emailModel
             ->where('id', $oldEmailId)
             ->update(['id' => $newEmailId]);
    }

    // More of the above
    public function updateInBatchIdSwitches($switches) {
        foreach ($switches as $row) {
            $old = $row['old'];
            $new = $row['new'];

            $this->emailModel->where('id', $old)->update(['id' => $new]);
        }
    }

    public function insertNew(array $row) {
        // Due to the possibility of incomplete parallelization, 
        // we cannot be sure that this email is not already in the db.
        $email = $this->emailModel->where('email_address', $row['email_address'])->first();

        if (!$email) {
            // One final precaution
            $email = $this->emailModel->updateOrCreate(['email_address' => $row['email_address']], $row);
        }

        return $email;
    }

    public function getRecordInfoAddress($address) {
        $attr = config('database.connections.attribution.database');
        $supp = config('database.connections.suppression.database');

        return DB::select("SELECT
            e.email_id as eid, 
            e.email_address, 
            first_name, 
            last_name, 
            CONCAT(rd.address, ' ', rd.city, ', ', rd.state, ' ', rd.zip) as address, 
            e.source_url, 
            e.capture_date as date, 
            e.ip, 
            IFNULL(sgo.suppress_datetime, '') as removal_date, 
            dob as birthdate,
            gender,
            e.subscribe_date,
            f.short_name,
            '' as action,
            IFNULL(last_action_date, '') as action_date,
            IF(sgo.email_address IS NULL, 0, 1) as suppressed,
            IFNULL(sr.display_status, '') as suppression_reason,
            IF(sgo.email_address IS NULL, 'A', 'U') as status,
            IF(efa.feed_id = e.feed_id, 'Y', '') as attributed_feed

        FROM
            (SELECT
                email_id, 
                email_address, 
                feed_id, 
                MIN(capture_date) as capture_date, 
                MIN(subscribe_datetime) as subscribe_date, 
                source_url, 
                MIN(ip) as ip # false, but we need to pick a value
            FROM
                email_feed_instances e1
                INNER JOIN emails e2 ON e1.email_id = e2.id
            WHERE
                email_address = :address
            GROUP BY
                email_id, email_address, feed_id, source_url) e
            INNER JOIN feeds f ON e.feed_id = f.id
            LEFT JOIN $attr.email_feed_assignments efa ON e.email_id = efa.email_id
            LEFT JOIN record_data rd ON e.email_id = rd.email_id
            LEFT JOIN $supp.suppression_global_orange sgo ON e.email_address = sgo.email_address
            LEFT JOIN suppression_reasons sr ON sgo.reason_id = sr.id

        UNION

        #First Party
        SELECT
            e.id as eid, 
            e.email_address, 
            rd.first_name, 
            rd.last_name, 
            CONCAT(rd.address, ' ', rd.city, ', ', rd.state, ' ', rd.zip) as address, 
            rd.source_url, 
            rd.capture_date as date, 
            rd.ip, 
            '' as removal_date, 
            dob as birthdate,
            gender,
            rd.subscribe_date,
            f.short_name,
            '' as action,
            IFNULL(last_action_date, '') as action_date,
            0 as suppressed,
            '' as suppression_reason,
            'A' as status,
            '1st party' as attributed_feed

        FROM
            emails e
            LEFT JOIN first_party_record_data rd ON e.id = rd.email_id
            INNER JOIN feeds f ON rd.feed_id = f.id
        WHERE
            e.email_address = :address2", [

            'address' => $address,
            'address2' => $address
        ]);

    }

    public function getRecordInfoId($id) {
        $attr = config('database.connections.attribution.database');
        $supp = config('database.connections.suppression.database');

        return DB::select("SELECT
            e.email_id as eid, 
            e.email_address, 
            first_name, 
            last_name, 
            CONCAT(rd.address, ' ', rd.city, ', ', rd.state, ' ', rd.zip) as address, 
            e.source_url, 
            e.capture_date as date, 
            e.ip, 
            IFNULL(sgo.suppress_datetime, '') as removal_date, 
            dob as birthdate,
            gender,
            e.subscribe_date,
            f.short_name,
            '' as action,
            IFNULL(last_action_date, '') as action_date,
            IF(sgo.email_address IS NULL, 0, 1) as suppressed,
            IFNULL(sr.display_status, '') as suppression_reason,
            IF(sgo.email_address IS NULL, 'A', 'U') as status,
            IF(efa.feed_id = e.feed_id, 'Y', '') as attributed_feed

        FROM
            (SELECT
                email_id, 
                email_address, 
                feed_id, 
                MIN(capture_date) as capture_date, 
                MIN(subscribe_datetime) as subscribe_date, 
                source_url, 
                MIN(ip) as ip # false, but we need to pick a value
            FROM
                email_feed_instances e1
                INNER JOIN emails e2 ON e1.email_id = e2.id
            WHERE
                e2.id = :id
            GROUP BY
                email_id, email_address, feed_id, source_url) e
            INNER JOIN feeds f ON e.feed_id = f.id
            LEFT JOIN $attr.email_feed_assignments efa ON e.email_id = efa.email_id
            LEFT JOIN record_data rd ON e.email_id = rd.email_id
            LEFT JOIN $supp.suppression_global_orange sgo ON e.email_address = sgo.email_address
            LEFT JOIN suppression_reasons sr ON sgo.reason_id = sr.id

        UNION

        #First Party
        SELECT
            e.id as eid, 
            e.email_address, 
            rd.first_name, 
            rd.last_name, 
            CONCAT(rd.address, ' ', rd.city, ', ', rd.state, ' ', rd.zip) as address, 
            rd.source_url, 
            rd.capture_date as date, 
            rd.ip, 
            '' as removal_date, 
            dob as birthdate,
            gender,
            rd.subscribe_date,
            f.short_name,
            '' as action,
            IFNULL(last_action_date, '') as action_date,
            0 as suppressed,
            '' as suppression_reason,
            'A' as status,
            '1st party' as attributed_feed

        FROM
            emails e
            LEFT JOIN first_party_record_data rd ON e.id = rd.email_id
            INNER JOIN feeds f ON rd.feed_id = f.id
        WHERE
            e.id = :id2", [

            'id' => $id,
            'id2' => $id
        ]);
    }

    public function transformForMt1($startId) {
        $attr = config('database.connections.attribution.database');
        $supp = config('database.connections.suppression.database');
        $startId = (int)$startId;

        return $this->emailModel
                    ->selectRaw("emails.id as tracking_id,
                        emails.id as email_user_id,
                        efa.feed_id as client_id,
                        emails.email_address as email_addr,
                        email_domain_id as domain_id,
                        DATE(subscribe_date) as subscribe_date,
                        TIME(subscribe_date) as subscribe_time,
                        DATE(sgo.created_at) as unsubscribe_date, 
                        TIME(sgo.created_at) as unsubscribe_time,
                        IF(sgo.id is NULL, 'A', 'U') as status,
                        first_name,
                        last_name,
                        address,
                        address2,
                        city,
                        state,
                        zip,
                        country,
                        dob,
                        IF(gender = 'UNK', '', gender),
                        phone,
                        '' as mobile_phone,
                        '' as work_phone,
                        rd.capture_date,
                        ip as member_source,
                        source_url,
                        last_action_date as emailUserActionDate,
                        null as emailUserActionTypeID,
                        rd.updated_at as lastUpdated")
                    ->leftJoin("$attr.email_feed_assignments as efa", 'emails.id', '=', 'efa.email_id')
                    ->leftJoin("record_data as rd", 'rd.email_id', '=', 'emails.id')
                    ->leftJoin("$supp.suppression_global_orange as sgo", "emails.email_address", '=', 'sgo.email_address')
                    ->whereRaw("emails.id > $startId");
    }
}
