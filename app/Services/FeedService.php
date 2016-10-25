<?php

namespace App\Services;

use App\Repositories\FeedRepo;

class FeedService {
    
    private $repo;

    public function __construct(FeedRepo $repo) {
        $this->repo = $repo;
    }

    public function getAllFeedsArray() {
        return $this->repo->getAllFeedsArray();
    }

    
    public function getFeeds () {
        return $this->repo->getFeeds();
    }


    public function getClientFeedMap () {
        $map = [];
        $clients = $this->repo->getAllClients();

        foreach ($clients as $client) {
            $feeds = [];

            foreach ($client->feeds as $feed) {
                $feeds[] = $feed->id;
            }

            $map[$client->id] = $feeds;
        }

        return $map;
    }
}