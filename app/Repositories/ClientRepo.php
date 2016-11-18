<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class ClientRepo {

    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function updateOrCreate($data) {
        $this->client->updateOrCreate(['id' => $data['id']], $data);
    }

    public function getModel () {
        return $this->client;
    }

    public function get() {
        return $this->client->orderBy('name')->get();
    }

    public function getAll () {
        return $this->client->get();
    }

    public function getAccount ($id) {
        return $this->client->find( $id );
    }

    public function getFeeds ( $id ) {
        return $this->client->find( $id )->feeds()->get();
    }

    public function getAllClientsArray() {
        return $this->client->orderBy('name')->get()->toArray();
    }

    public function get() {
        return $this->client->orderBy('name')->get();
    }
}
