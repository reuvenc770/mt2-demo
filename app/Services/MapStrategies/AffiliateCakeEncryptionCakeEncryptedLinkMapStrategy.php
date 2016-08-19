<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AffiliateCakeEncryptionCakeEncryptedLinkMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'affiliate_id' => $record['affiliateID'],
            'creative_id' => $record['creativeID'],
            'encrypt_hash' => $record['encryptHash'],
            'created_at' => $record['lastUpdated'],
            'updated_at' => $record['lastUpdated']
        ];
    }
}