<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;

use App\Facades\Guzzle;

class PublicatorsApi extends EspBaseAPI {
    const ESP_NAME = 'Publicators';

    const API_BASE_URL = 'https://apiv1.publicators.com';
    const API_AUTH = '/api/Authorization/SetCustomerAuthorization';
    const API_LIST_CAMPAIGNS = '/api/Campaigns/GetCampaignsBasicDetailsByDatesRange';
    const API_CAMPAIGNS_STATS = '/api/Reports/GetCampaignStatisticByCampaignId';
    const API_SENT_STATS = '/api/Reports/GetReportAllSentMailsByCampaignID';
    const API_OPENS_STATS = '/api/Reports/GetReportAllOpenedMailsByCampaignID';
    const API_CLICKS_STATS = '/api/Reports/GetReportAllClickedMailsByCampaignID';
    const API_BOUNCED_STATS = '/api/Reports/GetReportAllBouncedMailsByCampaignID';

    protected $username;
    protected $password;
    protected $token;

    public function __construct ( $espAccountId ) {
        $this->espAccountId = $espAccountId;

        parent::__construct( self::ESP_NAME , $espAccountId );

        $this->loadCreds();
    }

    protected function loadCreds () {
        $creds = EspApiAccount::grabApiUsernameWithPassword( $espAccountId );

        $this->username = $creds[ 'userName' ];
        $this->password = $creds[ 'password' ];
    }
}
