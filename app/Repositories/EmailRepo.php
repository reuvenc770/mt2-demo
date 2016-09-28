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

    public function __construct(Email $emailModel) {
        $this->emailModel = $emailModel;
    }

    public function getEmailId($emailAddress) {
        return $this->emailModel->select( 'id' )->where( 'email_address' , $emailAddress )->get();
    }

    public function getAttributedFeed($identifier) {
        if (is_numeric($identifier)) {
            return $this->getAttributionForId($identifier);
        }
        elseif (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return $this->getAttributedFeedForAddress($identifier);
        }
        else {
            throw new \Exception("Invalid identification type for email");
        }
    }

    public function insertCopy($emailData) {
        #$this->emailModel->updateOrCreate($emailData);
        DB::statement(
            "INSERT INTO emails (id, email_address, email_domain_id)
            VALUES(:id, :addr, :domain_id)
            ON DUPLICATE KEY UPDATE
            id = id, email_address=email_address, email_domain_id=email_domain_id ",
            array(
                ':id' => $emailData['id'],
                ':addr' => $emailData['email_address'],
                ':domain_id' => $emailData['email_domain_id']
            )
        );
    }

    private function getAttributedFeedForAddress($emailAddr) {
        # TODO: flesh out attribution. This will return a feed_id
        return 1;
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

    public function getSetAttributionLevel($emailId) {
        $attributionLevel = $this->emailModel->find($emailId)->feedAssignment->feed->attributionLevel;

        if ($attributionLevel) {
            return $attributionLevel->level;
        }
        else {
            return 1000;
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

}
