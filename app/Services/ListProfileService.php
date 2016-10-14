<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/8/16
 * Time: 11:31 AM
 */

namespace App\Services;


use App\Repositories\ListProfileRepo;
use App\Repositories\FeedRepo;
use App\Services\MT1Services\ClientStatsGroupingService;
use App\Services\MT1Services\ClientService;

class ListProfileService
{
    protected $profileRepo;
    protected $feedRepo;
    protected $clientService;
    protected $mt1ClientService;

    public function __construct( ListProfileRepo $repo , FeedRepo $feedRepo , ClientStatsGroupingService $clientService , ClientService $mt1ClientService )
    {
        $this->profileRepo = $repo;
        $this->feedRepo = $feedRepo;
        $this->clientService = $clientService;
        $this->mt1ClientService = $mt1ClientService;
    }


    public function getActiveListProflies(){
        return $this->profileRepo->returnActiveProfiles();
    }

    public function getFeeds () {
        return $this->feedRepo->getFeeds()->keyBy( 'id' )->toArray();
    }

    public function getClients () {
        return $this->clientService->getListGroups()->keyBy( 'value' )->toArray();
    }

    public function getClientFeedMap () {
        $feeds = $this->feedRepo->getFeeds()->keyBy( 'id' )->toArray();
        $clients = $this->clientService->getListGroups()->keyBy( 'value' )->toArray();

        $clientFeedMap = [];

        foreach ( $clients as $currentClientId => $currentClient ) {
            $clientFeedList = $this->mt1ClientService->getClientFeedsForListOwner( $currentClientId );

            foreach ( $clientFeedList as $currentFeedId ) {
                if ( !isset( $clientFeedMap[ $currentClientId ] ) ) {
                    $clientFeedMap[ $currentClientId ] = [];
                }

                $clientFeedMap[ $currentClientId ] []= $currentFeedId;
            }
        }

        return $clientFeedMap;
    }
}
