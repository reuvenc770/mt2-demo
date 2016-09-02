<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/2/16
 * Time: 12:30 PM
 */

namespace App\Repositories\MT1Repositories;


use App\Models\MT1Models\UniqueProfile;
use Exception;
use Log;
use DB;

class UniqueProfileRepo
{
    protected $profile;

    public function __construct(UniqueProfile $profile)
    {
        $this->profile = $profile;
    }

    public function getProfilesNameAndId(){
        return $this->profile->select('profile_id as id', "profile_name as name")->where('status', '=', 'A')->get();
    }

    public function getProfileById ( $id ) {
        return $this->profile->find( $id );
    }

    public function getModel () { return $this->profile->orderBy( 'profile_id' , 'desc' ); }

    public function getIspsByProfileId ( $profileId ) {
        try {
            return DB::connection( 'mt1mail' )->table( 'UniqueProfileIsp' )
                ->join( 'email_class' , 'email_class.class_id' , '=' , 'UniqueProfileIsp.class_id' )
                ->select( 'email_class.class_id as id' , 'email_class.class_name as name' )
                ->where( 'UniqueProfileIsp.profile_id' , $profileId )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "UniqueProfileRepo error: " . $e->getMessage() );
        }
    }

    public function getSourcesByProfileId ( $profileId ) {
        try {
            return DB::connection( 'mt1mail' )->table( 'UniqueProfileUrl' )
                ->select( 'UniqueProfileUrl.source_url' )
                ->where( 'UniqueProfileUrl.profile_id' , $profileId )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "UniqueProfileRepo error: " . $e->getMessage() );
        }
    }

    public function getSeedsByProfileId ( $profileId ) {
        try {
            return DB::connection( 'mt1mail' )->table( 'UniqueProfileSid' )
                ->select( 'UniqueProfileSid.sid' )
                ->where( 'UniqueProfileSid.profile_id' , $profileId )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "UniqueProfileRepo error: " . $e->getMessage() );
        }
    }

    public function getZipsByProfileId ( $profileId ) {
        try {
            return DB::connection( 'mt1mail' )->table( 'UniqueProfileZip' )
                ->select( 'UniqueProfileZip.zip' )
                ->where( 'UniqueProfileZip.profile_id' , $profileId )
                ->get();
        } catch ( \Exception $e ) {
            Log::error( "UniqueProfileRepo error: " . $e->getMessage() );
        }

    }

    public function getAll () {
        try {
            return $this->profile->get();
        } catch ( \Exception $e ) {
            Log::error( "UniqueProfileRepo Error:: " . $e->getMessage() );
        }
    }

    public function pullForSync($lookback) {
        return $this->profile
                    ->where('last_updated', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"));
    }
}
