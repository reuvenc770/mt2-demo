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

    public function getAllClientsArray() {
        return $this->client->orderBy('id')->get()->toArray();
    }

    public function getAllClients() {
        return $this->client->get();
    }

}