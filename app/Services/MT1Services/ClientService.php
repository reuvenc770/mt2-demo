<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:35 PM
 */

namespace App\Services\MT1Services;

use App\Services\Interfaces\IFtpAdmin;

use App\Repositories\MT1Repositories\ClientRepo;
use App\Services\API\MT1Api;

use App\Services\ServiceTraits\PaginateMT1;
use Artisan;
use DB;
use Log;

class ClientService implements IFtpAdmin
{
    use PaginateMT1;
    protected $clientRepo;

    public function __construct(ClientRepo $clientRepo, MT1Api $apiService)
    {
        $this->clientRepo = $clientRepo;
        $this->pageName  = "clients_list";
        $this->api = $apiService;
    }

    public function getClientFeedsForListOwner ( $listOwnerId ) {
        $results = DB::connection( 'mt1_data' )->table( 'user' )
            ->where( 'clientStatsGroupingID' , $listOwnerId )
            ->pluck( 'user_id' );

        if ( count( $results ) > 0 ) {
            return $results;
        } else {
            return collect( [] );
        }
    }

    public function getAssignedListOwnerId ( $feedId ) {
        $results = DB::connection( 'mt1_data' )->table( 'user' )
            ->select( 'clientStatsGroupingID' )
            ->where( 'user_id' , $feedId )
            ->get();

        if ( count( $results ) > 0 ) {
            return $results[ 0 ]->clientStatsGroupingID;
        } else {
            return 0;
        }
    }

    public function getFeedName ( $feedId ) {
        $results = DB::connection( 'mt1_data' )->table( 'user' )->where( 'user_id' , $feedId )->pluck( 'username' );

        $name = '';
        if ( !empty( $results ) && is_array( $results ) ) {
            $name = array_pop( $results );
        }

        return $name;
    }

    public function getAllTypes(){
        return $this->clientRepo->getClientTypes();
    }

    public function getType () {
        return 'client';
    }

    public function saveFtpUser ( $credentials ) {
        Log::info( 'Saving user credentials to db. Creds: ' . json_encode( $credentials ) );

        DB::connection( 'mt1_data' )->table( 'user' )
            ->where( 'username' , $credentials[ 'username' ] )
            ->update( [ 'ftp_pw' => $credentials[ 'password' ],
                'ftp_user' => $credentials[ 'username' ],
                'ftp_url' => $credentials['ftp_url'],
                'newClient' => 0 ] );
    }

    public function findNewFtpUsers () {
        return DB::connection( 'mt1_data' )->table( 'user' )
            ->select( 'username' )
            ->where( [ 'newClient' => 1 , 'ftp_user' => '' ] )
            ->get();
    }

    public function resetPassword($username){
        Artisan::queue('ftp:admin', [
            '-H' => "52.205.67.250",
            '-U' => 'root',
            '-k' => '~/.ssh/mt2ftp.pub',
            '-K' => '~/.ssh/mt2ftp',
            '-u' => $username,
            '-s' => "Client",
            '-r' => true
        ]);
        return true;
    }
}
