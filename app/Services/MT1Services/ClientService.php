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


    public function getAllTypes(){
        return $this->clientRepo->getClientTypes();
    }

    public function getType () {
        return 'client';
    }

    public function saveFtpUser ( $credentials ) {
        Log::info( 'Saving user credentials to db. Creds: ' . json_encode( $credentials ) );

        DB::connection( 'mt1mail' )->table( 'user' )
            ->where( 'username' , $credentials[ 'username' ] )
            ->update( [ 'ftp_pw' => $credentials[ 'password' ] ] );
    }

    public function findNewFtpUsers () {
        return DB::connection( 'mt1mail' )->table( 'user' )
            ->select( 'username' )
            ->where( [ 'newClient' => 1 , 'ftp_user' => '' ] )
            ->get();
    }
}
