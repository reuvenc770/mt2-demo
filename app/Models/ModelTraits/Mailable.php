<?php
namespace App\Models\ModelTraits;

trait Mailable {

    public function returnApprovalAndStatus() {

        if ((int)$this->is_approved !== 1) {
            return 'unapproved';
        }
        elseif ($this->status !== 'A') {
            return 'inactive';
        }
        else {
            return 'allowed';
        }
    }

}