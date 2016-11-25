<?php

namespace App\Repositories;

use App\Models\Email;
#use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use DB;

/**
 *
 */
class EmailRepo {

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

}
