<?php

namespace App\Services\API;
use App\Facades\EspApiAccount;
use App\Facades\Guzzle;
use App\Repositories\OAuthTokensRepo;

/**
 * Class InfusionSoft
 * @package App\Services\API
 */
class InfusionSoftApi extends EspBaseAPI
{

    const ESP_NAME = "InfusionSoft";
    const REFRESH_TOKEN_URL = "https://api.infusionsoft.com/token";
    const BASE_URL = 'https://api.infusionsoft.com/crm/rest/v1';
    const CAMPAIGN_ENDPOINT = '/campaigns';

    public function __construct($espAccountId, OAuthTokensRepo $oAuthRepo)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
        $this->espAccount = EspApiAccount::find($espAccountId);
        $this->oAuthRepo = $oAuthRepo;
    }

    public function sendApiRequest()
    {
        // we recommend requests carry Accept: application/json, */* as Accept header and value.
        try {
            $oAuth = $this->getOAuthData();

            $data = Guzzle::get(self::BASE_URL . self::CAMPAIGN_ENDPOINT, [
                'Authorization' => $oAuth->oAuthToken
            ]);

            // need to parse this out

            return $data;
        }
        catch (\Exception $e) {
            echo "EXCEPTION:";
            var_dump($e); // not sure what $e is just yet

            /*
            $this->refreshTokenCall();
            $data = Guzzle::get(self::BASE_URL . self::CAMPAIGN_ENDPOINT, [
                'Authorization' => $oAuth->oAuthToken
            ]);

            return $data;
            */ 
        }

        
    }

    public function refreshTokenCall() {
        $oAuth = $this->getOAuthData($this->espAccount);

        $data = Guzzle::post(self::REFRESH_TOKEN_URL, [
            'client_id' => $oAuth->clientId,
            'client_secret' => $oAuth->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $oAuth->lastRefreshToken,
            'Header:Authorization' => "Basic" . base64_encode($oAuth->clientId . ':' . $oAuth->clientSecret)
        ]);

        var_dump($data);

        // So there needs to be a listener which will check for a refresh token event
        // and then rerun any particular job
        /*
        $newRefreshToken = $data->refreshToken;
        $accessToken = $data->accessToken

        $this->oAuthRepo->updateAccessToken($this->espAccountId, $accessToken);
        $this->espAccountRepo->setKeys($this->espAccountId, $oAuth->clientId, $newRefreshToken);
        
        */
    }

    public function generateNewToken($code) {
        $oAuth = $this->getOAuthData($this->espAccount);

        Guzzle::post(self::REFRESH_TOKEN_URL, [
            'client_id' => $oAuth->clientId,
            'client_secret' => $oAuth->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $oAuth->redirectUrl
        ]);

    }

    private function getOAuthData($espAccount) {
        $oAuth = $espAccount->OAuthTokens;

        if ($oAuth) {
            $return = [
                'clientId' =>  $this->espAccount->key_1,
                'lastRefreshToken' => $this->espAccount->key_2,
                'redirectUri' => $oAuth->redirect_uri,
                'clientSecret' => $oAuth->access_secret,
                'oAuthToken' => $oAuth->access_token
            ];

            return (object)$return;
        }
        else {
            throw new \Exception("Could not obtain InfusionSoft OAuth token because OAuth not set up for esp account #{$this->espAccountId}");
        }
    }

    public function addContact($info) {}

}
