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
        return $this->client->whereRaw("id > $stopPoint");
    }

    public function extractAllForS3() {
        return $this->client;
    }


    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->id) . ','
            . $pdo->quote($row->name) . ','
            . $pdo->quote($row->address) . ','
            . $pdo->quote($row->address2) . ','
            . $pdo->quote($row->city) . ','
            . $pdo->quote($row->state) . ','
            . $pdo->quote($row->zip) . ','
            . $pdo->quote($row->email_address) . ','
            . $pdo->quote($row->phone) . ','
            . $pdo->quote($row->status) . ','
            . $pdo->quote($row->created_at) . ','
            . $pdo->quote($row->updated_at);
    }

    public function getConnection() {
        return $this->client->getConnectionName();
    }
}
