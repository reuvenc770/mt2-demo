<?php

namespace App\Services;

use App\Repositories\FeedRepo;
use App\Models\CakeVertical;
use App\Models\FeedType;
use App\Repositories\CountryRepo;
use App\Services\ServiceTraits\PaginateList;
use App\Services\Interfaces\IFtpAdmin;

class FeedService implements IFtpAdmin
{
    use PaginateList;
    
    private $feedRepo;
    private $verticals;
    private $feedTypes;
    private $countryRepo;

    public function __construct(FeedRepo $feedRepo, CakeVertical $cakeVerticals , CountryRepo $countryRepo , FeedType $feedTypes) {
        $this->feedRepo = $feedRepo;
        $this->verticals = $cakeVerticals;
        $this->feedTypes = $feedTypes;
        $this->countryRepo = $countryRepo;
    }

    public function getAllFeedsArray() {
        return $this->feedRepo->getAllFeedsArray();
    }

    
    public function getFeeds () {
        return $this->feedRepo->getFeeds();
    }


    public function getClientFeedMap () {
        $map = [];
        $clients = $this->feedRepo->getAllClients();

        foreach ($clients as $client) {
            $feeds = [];

            foreach ($client->feeds as $feed) {
                $feeds[] = $feed->id;
            }

            $map[$client->id] = $feeds;
        }

        return $map;
    }

    public function getFeed($id) {
        return $this->feedRepo->fetch($id);
    }

    public function getVerticals() {
        return $this->verticals->get();
    }

    public function getFeedTypes() {
        return $this->feedTypes->get();
    }

    public function getCountries() {
        return $this->countryRepo->get();
    }

    public function getModel() {
        return $this->feedRepo->getModel();
    }

    public function updateOrCreate ( $data , $id = null ) {
        $this->feedRepo->updateOrCreate( $data , $id );
    }

    public function getType () {
        return 'Feed';
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
