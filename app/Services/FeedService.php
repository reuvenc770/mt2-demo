<?php

namespace App\Services;

use App\Repositories\FeedRepo;
use App\Models\CakeVertical;
use App\Models\FeedType;
use App\Repositories\CountryRepo;
use App\Services\ServiceTraits\PaginateList;

class FeedService {

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
}