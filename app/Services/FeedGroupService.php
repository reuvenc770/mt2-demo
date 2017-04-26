<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\ServiceTraits\PaginateList;
use App\Repositories\FeedGroupRepo;

class FeedGroupService {
    use PaginateList;

    protected $repo;

    public function __construct ( FeedGroupRepo $repo ) {
        $this->repo = $repo;
    }

    public function getModel () {
        return $this->repo->getModel();
    }

    public function updateOrCreate ( $data ) {
        return $this->repo->updateOrCreate( $data );
    }

    public function updateFeeds ( $data ) {
        $this->repo->updateFeeds( $data );
    }

    public function getName ( $id ) {
        return $this->repo->getName( $id );
    }

    public function getFeeds ( $id ) {
        return $this->repo->getFeeds( $id );
    }

    public function getAllFeedGroupsArray(){
        return $this->repo->getAllFeedGroupsArray();
    }
}
