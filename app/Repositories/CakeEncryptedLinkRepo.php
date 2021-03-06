<?php

namespace App\Repositories;

use App\Models\CakeEncryptedLink;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CakeEncryptedLinkRepo {

    private $model;

    public function __construct(CakeEncryptedLink $model) {
        $this->model = $model;
    }

    public function getHash($affiliateId, $cakeCreativeId) {
        try {
            return $this->model
                        ->where('affiliate_id', $affiliateId)
                        ->where('creative_id', $cakeCreativeId)
                        ->select('encrypted_hash')
                        ->firstOrFail()
                        ->encrypted_hash;
        }
        catch (ModelNotFoundException $e) {
            // give it a better message than the default one
            throw new ModelNotFoundException("No encrypted hash found for affiliate_id $affiliateId and cake creative $cakeCreativeId");
        }

    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate([
            'affiliate_id' => $data['affiliate_id'], 
            'creative_id' => $data['creative_id']
            ], $data);
    }

    public function prepareTableForSync() {}
}