<?php

namespace App\Services;

use App\Repositories\ClientRepo;

class ClientService {
    
    private $repo;

    public function __construct(ClientRepo $repo) {
        $this->repo = $repo;
    }


    public function getAllClientsArray() {
        return $this->repo->getAllClientsArray();
    }


    public function getClientFeedMap () {
        $map = [];
        $clients = $this->repo->get();

        foreach ($clients as $client) {
            $feeds = [];

            foreach ($client->feeds as $feed) {
                $feeds[] = $feed->id;
            }

            $map[$client->id] = $feeds;
        }

        return $map;
    }

    public function get() {
        return $this->repo->get();
    }

}
