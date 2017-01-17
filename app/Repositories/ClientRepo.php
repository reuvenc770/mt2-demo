<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;
use App\Repositories\RepoInterfaces\IAwsRepo;

class ClientRepo implements IAwsRepo {

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
        return $this->client->get()->sortBy( 'name' );
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

    public function extractForS3Upload($stopPoint) {
        return $this->client->where('id', '>', $stopPoint);
    }

    public function mapForS3Upload($row) {
        return [
            'id' => $row->id,
            'name' => $row->name,
            'address' => $row->address,
            'address2' => $row->address2,
            'city' => $row->city,
            'state' => $row->state,
            'zip' => $row->zip,
            'email_address' => $row->email_address,
            'phone' => $row->phone,
            'status' => $row->status,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at
        ];
    }
}
