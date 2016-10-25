<?php

namespace App\Services;

use App\Models\CakeVertical;
use App\Models\FeedType;
use App\Repositories\CountryRepo;
use App\Repositories\FeedRepo;
use App\Services\ServiceTraits\PaginateList;

class FeedService
{
    use PaginateList;

    private $verticals;
    private $feedTypes;
    private $countryRepo;
    private $feedRepo;

    public function __construct( CakeVertical $cakeVerticals , CountryRepo $countryRepo , FeedRepo $feedRepo , FeedType $feedTypes ) {
        $this->verticals = $cakeVerticals;
        $this->feedTypes = $feedTypes;
        $this->countryRepo = $countryRepo;
        $this->feedRepo = $feedRepo;
    }

    public function getFeeds () {
        return $this->feedRepo->getFeeds();
    }

    public function getFeed($id) {
        return $this->feedRepo->fetch($id);
    }

    public function getClientTypes() {
        return $this->verticals->get();
    }

    public function getFeedTypes() {
        return $this->feedTypes->get();
    }

    public function getCountries() {
        return $this->countryRepo->get();
    }

    public function getModel() {
        return $this->feedRepo->getModel();
    }

    public function updateOrCreate ( $data ) {
        $this->feedRepo->updateOrCreate( $data );
    }

    public function getType () {
        return 'Feed';
    }
}
