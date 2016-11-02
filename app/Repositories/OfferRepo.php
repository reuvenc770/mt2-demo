<?php

namespace App\Repositories;

use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class OfferRepo {
  
    private $offer;

    public function __construct(Offer $offer) {
        $this->offer = $offer;
    }

    public function all () {
        return $this->offer->where( [ [ 'is_approved' , '=' , 1 ] , [ 'status' , '=' , 'A' ] ] )->orderBy( 'id' , 'desc' )->get();
    }

    public function updateOrCreate($data) {
        $this->offer->updateOrCreate(['id' => $data['id']], $data);
    }

    public function getAdvertiserName($offerId) {
        $result = $this->offer
             ->join('advertisers as a', 'offers.advertiser_id', '=', 'a.id')
             ->where('offers.id', $offerId)
             ->first();

        if ($result) {
            return $result->name;
        }
        else {
            return '';
        }
    }

    public function fuzzySearchBack($term){
        return $this->offer->where('name', 'like', $term . '%')->select("id","name","exclude_days")->get();
    }

    public function offerCanBeMailedOnDay($offerId, $date) {
        // exclude_days is a 7 char string of Y/N
        $days = $this->offer->find($offerId)->exclude_days;

        // value below is 0-indexed with Sun as 0 and Sat as 6
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;

        // 'N' means that the offer is not excluded and can be mailed
        return $days[$dayOfWeek] === 'N';
    }

    public function getSuppressionListIds($id) {
        $suppDb = config('database.connections.suppression.database');
        $lists = $this->offer
                    ->join("$suppDb.offer_suppression_lists as osl", 'offers.id', '=', 'osl.offer_id')
                    ->where('offers.id', $id)
                    ->select('suppression_list_id')
                    ->get()
                    ->toArray();

        $output = [];

        foreach ($lists as $list) {
            $output[] = $list['suppression_list_id'];
        }

        return $output;
    }

}
