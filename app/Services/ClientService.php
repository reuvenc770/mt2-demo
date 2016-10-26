<?php

namespace App\Services;

use App\Repositories\ClientRepo;
use App\Services\Interfaces\IFtpAdmin;
use Artisan;
use DB;

class ClientService implements IFtpAdmin
{
    private $repo;

    public function __construct( ClientRepo $clientRepo ) {
        $this->repo = $clientRepo;
    }

    public function get() {
        return $this->repo->get();
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
