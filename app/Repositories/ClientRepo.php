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

    public function getAll () {
        return $this->client->get();
    }
}
