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

    public function prepareTableForSync() {}

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

    public function fuzzyCpmSearchBack ( $term ) {
        return $this->offer->whereRaw("name like '%$term%'")
            ->where( 'name' , 'NOT LIKE' , "PAUSED%" )
            ->where( 'name' , 'NOT LIKE' , "%INACTIVE%" )
            ->where( 'offer_payout_type_id' , 1 )
            ->where( [ [ 'is_approved' , '=' , 1 ] , [ 'status' , '=' , 'A' ] ] )
            ->select( "id" , "name" )->orderBy('name')->get();
    }

    public function fuzzySearchBack($term, $day = null) {
        if (null !== $day) {
            return $this->offer
                        ->whereRaw("(name like '%$term%' AND is_approved = 1 AND status = 'A' AND SUBSTR( exclude_days, {$day} , 1 ) = 'N' ) OR (name like '%$term%' AND name like '%SUPPRESSION%' AND SUBSTR( exclude_days, {$day} , 1 ) = 'N' )")
                        ->select("id","name")
                        ->orderBy('name')
                        ->get();
        }
        else {
            return $this->offer
                        ->whereRaw("(name like '%$term%' AND is_approved = 1 AND status = 'A') OR (name like '%$term%' AND name like '%SUPPRESSION%')")
                        ->select("id","name")
                        ->orderBy('name')
                        ->get();
        }
    }

    public function searchByDay($day){
        return $this->offer
            ->where(DB::raw("SUBSTR(exclude_days, {$day},1)"),'N')
            ->where( [ [ 'is_approved' , '=' , 1 ] , [ 'status' , '=' , 'A' ] ] )
            ->select("id","name")->orderBy('name')->get();
    }

    public function offerCanBeMailedOnDay($offerId, $date) {
        // exclude_days is a 7 char string of Y/N
        $days = $this->offer->find($offerId)->exclude_days;

        // value below is 0-indexed with Monday as 0 and Sun as 6

        $dayOfWeek = date('N',strtotime($date)) - 1;

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

    public function getOfferName($id){
        return $this->offer->find($id)->name;
    }

}
