<?php

namespace App\Repositories;

use App\Models\EmailOversightValidCache;
use Carbon\Carbon;
use Maknz\Slack\Facades\Slack;

class EmailOversightValidCacheRepo {
    protected $cache;
    protected $slackChannel = '#cmp_hard_start_errors';

    public function __construct ( EmailOversightValidCache $cache ) {
        $this->cache = $cache;
    }

    public function cacheEmail ( $email ) {
        try {
            if ( !$this->emailExists( $email ) ) {
                $this->cache->create( [
                    'email' => $email ,
                    'created_at' => Carbon::now()->toDateString()
                ] );
            }

            return true;
        } catch ( \Exception $e ) {
            \Log::error( $e );

            $notification = "EmailOversightValidCacheRepo::cacheEmail() - Failed to cache '{$email}'. " . $e->getMessage();

            Slack::to( $this->slackChannel )->send( $notification );

            return false;
        }
    }

    public function emailExists ( $email ) {
        $result = $this->cache->find( $email );

        return !is_null( $result );
    }

    public function clearPriorToDate ( $date ) {
        try {
            $cleanDate = Carbon::parse( $date )->toDateString();

            return $this->cache->where( 'created_at' , '<' , $cleanDate )->delete();
        } catch ( \Exception $e ) {
            $notification = "EmailOversightValidCacheRepo::clearPriorToDate() - '{$date}' is an invalid date. " . $e->getMessage();

            Slack::to( $this->slackChannel )->send( $notification );

            throw $e;
        }
    }
}
