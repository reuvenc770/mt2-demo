<?php

namespace App\Services\Validators;

use App\Services\Interfaces\IValidate;
use App\Exceptions\ValidationException;

class EmailOversightApiValidator implements IValidate {
    private $emailAddress;
    private $feedId;
    private $emailOversightListId;

    public function __construct () {}

    public function getRequiredData () {
        return [ 'emailAddress' , 'feedId' ];
    }

    public function setData ( array $data ) {
        $this->emailAddress = $data[ 'emailAddress' ];
        $this->feedId = $data[ 'feedId' ];
        $this->emailOversightListId = $this->getListIdFromFeed( $this->feedId );
    }

    public function validate () {
        if ( is_null( $this->emailOversightListId ) ) {
            return; #Feed ID does not have Email Oversight Validation enabled
        }

        $api = \App::make( \App\Services\EmailOversightApiService::class );

        if ( false === $api->verifyEmail( $this->emailOversightListId , $this->emailAddress ) ) {
            throw new ValidationException( "The email address '{$this->emailAddress}' did not pass EmailOversight validation. " . $api->getLastMessage() );
        }
    }

    public function returnData () {
        return [
            'emailAddress' => $this->emailAddress ,
            'feedId' => $this->feedId
        ];
    }

    protected function getListIdFromFeed ( $feedId ) {
        $result = \App\Models\Feed::find( $feedId );

        if (
            !is_null( $result )
            && !is_null( $result->emailOversightListId() )
            && !is_null( $record = $result->emailOversightListId()->first() ) )
        {
            return $record->list_id;
        }
    }
}
