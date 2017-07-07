<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Http\Validators;

use Carbon\Carbon;
use Hash;
use Sentinel;
use App\Repositories\RawFeedEmailRepo;

class CustomValidatorHelper {
    protected $rawFeed;

    public function __construct ( RawFeedEmailRepo $rawFeed ) {
        $this->rawFeed = $rawFeed;
    }

    public function europeanDate ( $attribute , $value , $parameters , $validator ) {
        $date = $this->rawFeed->convertEuropeanDate( $value );

        return ( is_null( $date ) ? false : true );
    }

    public function europeanDateNotFuture ( $attribute , $value , $parameters , $validator ) {
        $date = $this->rawFeed->convertEuropeanDate( $value );

        if ( !is_null( $date ) && Carbon::parse( $date )->isFuture() ) {
            $date = null;
        }

        return ( is_null( $date ) ? false : true );
    }

    public function hash ( $attribute , $value , $parameters , $validator ) {
        if ( $parameters[ 0 ] == 'userPassword' ) {
            $user = Sentinel::getUser();

            return Hash::check( $value , $user->getUserPassword() );
        } else {
            return Hash::check( $value , $parameters[ 0 ] );
        }
    }
}
