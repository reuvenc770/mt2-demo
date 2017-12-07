<?php

namespace App\Repositories;

use App\Models\OAuthTokens;

class OAuthTokensRepo {

    private $model;

    public function __construct(OAuthTokens $model) {
        $this->model = $model;
    }

    public function updateAccessToken($espAccountId, $accessToken) {
        $this->model->where('esp_account_id', $espAccountId)
            ->update([
                'access_token' => $accessToken
            ]);
    }
}