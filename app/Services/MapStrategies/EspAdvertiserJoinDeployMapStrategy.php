<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;
use App\Repositories\OfferRepo;
use App\Repositories\EspApiAccountRepo;

class EspAdvertiserJoinDeployMapStrategy implements IMapStrategy {

    private $offerRepo;
    private $espAccountRepo;

    public function __construct(OfferRepo $offerRepo, EspApiAccountRepo $espAccountRepo) {
        $this->offerRepo = $offerRepo;
        $this->espAccountRepo = $espAccountRepo;
    }

    public function map($record) {
        $accountResult = $this->espAccountRepo->getIdFromName($record['espName']);
        $espAccountId = $accountResult ? (int)$accountResult->id : 0;
        $notes = $espAccountId > 0 ? '' : $record['espName'];
        return [
            'id' => $record['subAffiliateID'],
            'external_deploy_id' => $record['subAffiliateID'],
            'send_date' => $record['sendDate'],
            'esp_account_id' => $espAccountId,
            'offer_id' => $record['advertiserID'],
            'creative_id' => $record['creativeID'],
            'from_id' => $record['fromID'],
            'subject_id' => $record['subjectID'],
            'cake_affiliate_id' => $record['affiliateID'],
            'deployment_status' => 1,
            'content_domain_id' => 0,
            'mailing_domain_id' => 0,
            'template_id' => 0,
            'deploy_name' => ($this->buildName($record)),
            'encrypt_cake' => 1,
            'fully_encrypt' => 1,
            'url_format' => '',
            'notes' => $notes
        ];
    }

    protected function buildName($record) {
        $advertiserName = $this->offerRepo->getAdvertiserName($record['advertiserID']);
        return "{$record['subAffiliateID']}_{$record['espName']}_[]_US_" . strtoupper($advertiserName);
    }
}