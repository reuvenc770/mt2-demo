<?php

namespace App\Repositories;

use App\Models\OfferSuppressionList;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class OfferSuppressionListRepo {

    private $model;

    public function __construct(OfferSuppressionList $model) {
        $this->model = $model;
    }

    public function updateOrCreate($data) {
        DB::connection('suppression')->statement("INSERT INTO offer_suppression_lists (offer_id, suppression_list_id)
            VALUES (:offer_id, :suppression_list_id)
        ON DUPLICATE KEY UPDATE
            offer_id = offer_id,
            suppression_list_id = suppression_list_id", [
                ':offer_id' => $data['offer_id'],
                ':suppression_list_id' => $data['suppression_list_id']

            ]);
    }

    public function prepareTableForSync() {}

    public function getOfferForList($listId) {
        // Just takes the first
        $offer = $this->model
                    ->whereRaw("suppression_list_id = $listId")
                    ->select('offer_id')
                    ->first();

        
        if ($offer) {
            return $offer->offer_id;
        }
        else {
            return null;
        }
    }
}