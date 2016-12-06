<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\DataModels\LazyMT1SuppressionCheckIterator;
use App\Services\MT1Services\AdvertiserService;
use App\Repositories\MT1Repositories\MD5AdvertiserSuppressListRepo; 
use App\Repositories\MT1Repositories\VendorSuppListRepo;

class MT1SuppressionService {
    protected $advertiser;
    protected $plaintextRepo;
    protected $md5Repo;

    protected $advertiserId;
    protected $list;
 
    public function __construct ( AdvertiserService $advertiser , VendorSuppListRepo $plaintextRepo , MD5AdvertiserSuppressListRepo $md5Repo ) {
        $this->advertiser = $advertiser; 
        $this->plaintextRepo = $plaintextRepo;
        $this->md5Repo = $md5Repo;
    }

    public function getValidRecordGenerator ( $advertiserId , $recordModel ) {
        $this->setAdvertiser( $advertiserId );

        return new LazyMT1SuppressionCheckIterator( $this , $recordModel );
    }

    public function getSuppressedRecordGenerator ( $advertiserId , $recordModel ) {
        $this->setAdvertiser( $advertiserId );

        return new LazyMT1SuppressionCheckIterator( $this , $recordModel , true );
    }

    public function setAdvertiser ( $advertiserId ) {
        $this->advertiserId = $advertiserId;

        $listResult = $this->advertiser->getSuppressionListId( $advertiserId );

        if ( $listResult->count() <= 0 ) {
            throw new \Exception( "Advertiser {$advertiserId} is missing list." );
        }

        $this->list = $listResult->first(); 
    }

    public function isSuppressed ( $record , $advertiserId = null ) {
        if ( !is_null( $advertiserId ) ) {
            $this->setAdvertiser( $advertiserId );
        }

        if ( $this->list->isMD5 ) {
            return $this->md5Repo->isSuppressed( $record , $this->list->id );
        } else {
            return $this->plaintextRepo->isSuppressed( $record , $this->list->id );
        }
    }
}
