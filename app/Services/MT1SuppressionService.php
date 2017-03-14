<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\DataModels\LazyMT1SuppressionCheckIterator;
use App\Services\MT1Services\AdvertiserService;
use App\Repositories\MT1Repositories\MD5AdvertiserSuppressListRepo; 
use App\Repositories\MT1Repositories\VendorSuppListRepo;
use App\Repositories\MT1Repositories\VendorSuppListInfoRepo;
use App\Services\Interfaces\IFeedSuppression;
use App\Repositories\FirstPartyOnlineSuppressionListRepo;
use App\Repositories\OfferSuppressionListRepo;

class MT1SuppressionService implements IFeedSuppression {
    protected $advertiser;
    protected $plaintextRepo;
    protected $md5Repo;
    protected $listRepo;
    protected $suppListRepo;
    protected $offerListRepo;

    protected $advertiserId;
    protected $list;
    protected $feedId;
    protected $listIdTypeCache = [];
    protected $listOfferCache = [];
 
    public function __construct ( AdvertiserService $advertiser , 
        VendorSuppListRepo $plaintextRepo , 
        VendorSuppListInfoRepo $suppListRepo,
        MD5AdvertiserSuppressListRepo $md5Repo,
        FirstPartyOnlineSuppressionListRepo $listRepo,
        OfferSuppressionListRepo $offerListRepo) {

        $this->advertiser = $advertiser; 
        $this->plaintextRepo = $plaintextRepo;
        $this->md5Repo = $md5Repo;
        $this->listRepo = $listRepo;
        $this->suppListRepo = $suppListRepo;
        $this->offerListRepo = $offerListRepo;
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

    /**
     *  @param stdClass obj $record
     *  @param Int $advertiserId
     *  @return Bool
     */

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

    /**
     *  @param String $emailAddress
     *  @param Int $listId
     *  @return stdClass obj
     */

    public function emailSuppressedForList($emailAddress, $listId) {
        if (!isset($this->listIdTypeCache[$listId])) {
            $this->listIdTypeCache[$listId] = $this->getSuppressionType($listId);
        }

        if ('md5' === $this->listIdTypeCache[$listId]) {
            /**
                A hack for now. This has to be redone when offer/advertiser suppression is fixed.
            */
            if (!isset($this->listOfferCache[$listId])) {
                $this->listOfferCache[$listId] = $this->offerListRepo->getOfferForList($listId);
            }
            
            return $this->md5Repo->getSuppressed($emailAddress, $this->listOfferCache[$listId]);
        }
        else {
            return $this->plaintextRepo->getSuppressed($emailAddress, $listId);
        }
    }

    public function returnSuppressedEmails(array $emails) {
        $lists = $this->listRepo->getListsForFeed($this->feedId);
        $suppressed = [];

        foreach($emails as $emailAddress) {
            foreach ($lists as $listId) {
                $suppressedEmail = $this->emailSuppressedForList($emailAddress, $listId);
                if ($suppressedEmail) {
                    $suppressed[] = $suppressedEmail;
                    break;
                }
            }
        }

        return $suppressed;
    }

    public function setFeedId($feedId) {
        $this->feedId = $feedId;
    }
}
