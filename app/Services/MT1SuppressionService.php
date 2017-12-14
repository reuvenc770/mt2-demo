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

class MT1SuppressionService implements IFeedSuppression {
    protected $advertiser;
    protected $plaintextRepo;
    protected $md5Repo;
    protected $suppListRepo;

    protected $advertiserId;
    protected $list;
    protected $splitTypes = null;
    protected $listOfferCache = [];
 
    public function __construct ( AdvertiserService $advertiser , 
        VendorSuppListRepo $plaintextRepo , 
        VendorSuppListInfoRepo $suppListRepo,
        MD5AdvertiserSuppressListRepo $md5Repo) {

        $this->advertiser = $advertiser; 
        $this->plaintextRepo = $plaintextRepo;
        $this->md5Repo = $md5Repo;
        $this->suppListRepo = $suppListRepo;
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
        $this->list = $this->getAdvertiserList($advertiserId);
    }

    public function getAdvertiserList($advertiserId) {
        $listResult = $this->advertiser->getSuppressionListId($advertiserId);

        if ( $listResult->count() <= 0 ) {
            throw new \Exception( "Advertiser {$advertiserId} is missing list." );
        }

        if (!isset($this->listOfferCache[$listId])) {
            $listId = $listResult->first()->id;
            $this->listOfferCache[$listId] = $advertiserId;
        }
        
        return $listResult->first();
    }

    private function getListAdvertiser($listId) {
        $listResult = $this->advertiser->getAdvertiserForSuppressionList($listId);

        if ( $listResult->count() <= 0 ) {
            throw new \Exception( "List $listId is missing MT1 Advertiser." );
        }

        return $listResult->first()->advertiser_id;
    }

    /**
     *  @param Int[] $advertiserIds
     *  @return Object[]
     */

    public function getSuppressionLists(array $advertiserIds) {
        $output = ['md5' => [], 'plaintext' => [], 'count' => 0];

        foreach ($advertiserIds as $advertiserId) {
            $list = $this->getAdvertiserList($advertiserId);
            if (1 == $list->md5) {
                $output['md5'][] = $list->id;
            }
            else {
                $output['plaintext'][] = $list->id;
            }
            $output['count']++;
        }

        return (object)$output;
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

        if ( (int)$this->list->md5 === 1 ) {
            $record->lower_case_md5 = md5(strtolower($record->email_address));
            return $this->md5Repo->isSuppressed( $record , $this->list->id );
        } else {
            return $this->plaintextRepo->isSuppressed( $record , $this->list->id );
        }
    }


    public function suppressedByOffers(array $emailAddresses, $splitTypes) {
        $plaintextOutput = [];
        $md5Output = [];

        if (count($splitTypes->md5) > 0) {
            foreach ($this->md5Repo->getEmailsSuppressedForAdvertisers($emailAddresses, $splitTypes->md5) as $e) {
                $md5Output[] = $e->email_addr;
            }
        }

        $emailAddresses = array_diff($emailAddresses, $md5Output);
        
        if (count($splitTypes->plaintext) > 0) {
            foreach ($this->plaintextRepo->getEmailsSuppressedForLists($emailAddresses, $splitTypes->plaintext) as $e) {
                $plaintextOutput[] = $e->email_addr;
            }
        }
        
        return array_merge($md5Output, $plaintextOutput);
    }


    public function returnSuppressedEmails(array $emails) {
        if (null === $this->splitTypes) {
            throw new \Exception("MT1SuppressionService does not have offers set");
        }

        $output = [];

        if (count($splitTypes->md5) > 0) {
            foreach ($this->md5Repo->getEmailsSuppressedForAdvertisers($emailAddresses, $splitTypes->md5) as $e) {
                $output[$e->email_addr] = $e->advertisers;
            }
        }
        
        if (count($splitTypes->plaintext) > 0) {
            foreach ($this->plaintextRepo->getEmailsSuppressedForLists($emailAddresses, $splitTypes->plaintext) as $e) {
                if (isset($output[$e->email_addr])) {
                    $tmp = $output[$e->email_addr];
                    $output[$e->email_addr] = array_merge($tmp, $this->transformListsToOffers($e->lists));
                }
                else {
                    $output[$e->email_addr] = $this->transformListsToOffers($e->lists);
                }
            }
        }

        return $output;
    }

    public function setOffersWithTypes($splitTypes) {
        $this->splitTypes = $splitTypes;
    }

    private function transformListsToOffers($listIds) {
        // $listIds is a string
        $output = [];
        $lists = explode(',', $listIds);

        foreach($lists as $listId) {
            if (isset($this->listOfferCache[$listId])) {
                $output[] = $this->listOfferCache[$listId];
            }
            else {
                $advertiserId = $this->getListAdvertiser($listId);
                $this->listOfferCache[$listId] = $advertiserId;
                $output[] = $advertiserId;
            }
        }
        
        return $output;
    }

}
