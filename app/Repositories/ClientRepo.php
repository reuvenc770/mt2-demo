<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class ClientRepo {
  
    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function isActive($id) {
        return $this
                ->client
                ->select('status')
                ->where('id', $id)
                ->get()[0]['status'] === 'Active';
    }

    public function getMaxClientId() {
        return (int)$this->client->orderBy('id', 'desc')->first()['id'];
    }

    public function insert($data) {
        $this->client->insert($data);
    }

}