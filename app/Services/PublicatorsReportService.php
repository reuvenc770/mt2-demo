<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\Interfaces\IDataService;
use App\Services\AbstractReportService;

use App\Repositories\ReportRepo;
use App\Services\API\PublicatorsApi;
use App\Services\EmailRecordService;

use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;

use Log;

class PublicatorsReportService extends AbstractReportService implements IDataService {
    public function __construct ( ReportRepo $repo , PublicatorsApi $api , EmailRecordService $emailRecord ) {
        parent::__construct( $repo , $api , $emailRecord );
    }

    public function retrieveApiStats ( $date ) {
        $this->api->setDate( $date );

        if ( !$this->api->isAuthenticated() ) {
            try {
                $this->api->authenticate();
            } catch ( \Exception $e ) {
                throw new JobException( "Failed to Retrieve API Stats. " . $e->getMessage() , JobException::CRITICAL , $e );
            }
        }

        $campaigns = $this->api->getCampaigns();
        $campaignDataCollection = [];

        foreach ( $campaigns as $campaign ) {
            $campaign->esp_account_id = $this->api->getEspAccountId();
            $campaign->internal_id = $campaign->ID;
            unset( $campaign->ID );

            $campaignStats = $this->api->getCampaignStats( $campaign->internal_id ); 

            $campaignDataCollection []= array_merge( (array)$campaign , (array)$campaignStats );
        }

        return $campaignDataCollection;
    }

    public function insertApiRawStats ( $data ) {
        if ( !is_array( $data ) ) {
            throw new JobException( "Parameter 1 must be an array of campaign data." , JobException::NOTICE );
        }

        foreach ( $data as $campaignData ) {
            $this->insertStats( $this->api->getEspAccountId() , $campaignData );
        }

        #Event::fire( new RawReportDataWasInserted( $this , $data ) );
    }

    public function mapToRawReport ( $data ) {}

    public function mapToStandardReport ( $data ) {
        return [
            'campaign_name' => '' , #need to go back and see if this is avail
            'external_deploy_id' => '', #need to go back and see if sub id is avail
            'm_deploy_id' => '', #need to go back and see if sub id is avail
            'esp_account_id' => $report['esp_account_id'],
            'esp_internal_id' => $report['internal_id'],
        ];

        /*
        | external_deploy_id | int(11)          | YES  |     | NULL    |                |
            | campaign_name      | varchar(255)     | NO   | UNI |         |                |
            | m_deploy_id        | int(10) unsigned | NO   |     | 0       |                |
            | esp_account_id     | int(10) unsigned | NO   |     | 0       |                |
            | esp_internal_id    | int(11)          | NO   |     | 0       |                |
            | datetime           | datetime         | NO   |     | NULL    |                |
            | m_creative_id      | int(10) unsigned | NO   |     | 0       |                |
            | m_offer_id         | int(10) unsigned | NO   |     | 0       |                |
            | name               | varchar(255)     | YES  |     | NULL    |                |
            | subject            | varchar(255)     | YES  |     | NULL    |                |
            | from               | varchar(255)     | YES  |     | NULL    |                |
            | from_email         | varchar(255)     | YES  |     | NULL    |                |
            | m_sent             | int(10) unsigned | YES  |     | NULL    |                |
            | e_sent             | int(10) unsigned | YES  |     | NULL    |                |
            | delivered          | int(10) unsigned | YES  |     | NULL    |                |
            | bounced            | int(10) unsigned | YES  |     | NULL    |                |
            | optouts            | int(10) unsigned | YES  |     | NULL    |                |
            | m_opens            | int(10) unsigned | YES  |     | NULL    |                |
            | e_opens            | int(10) unsigned | YES  |     | NULL    |                |
            | t_opens            | int(10) unsigned | YES  |     | NULL    |                |
            | m_opens_unique     | int(10) unsigned | YES  |     | NULL    |                |
            | e_opens_unique     | int(10) unsigned | YES  |     | NULL    |                |
            | t_opens_unique     | int(10) unsigned | YES  |     | NULL    |                |
            | m_clicks           | int(10) unsigned | YES  |     | NULL    |                |
            | e_clicks           | int(10) unsigned | YES  |     | NULL    |                |
            | t_clicks           | int(10) unsigned | YES  |     | NULL    |                |
            | m_clicks_unique    | int(10) unsigned | YES  |     | NULL    |                |
            | e_clicks_unique    | int(10) unsigned | YES  |     | NULL    |                |
            | t_clicks_unique    | int(10) unsigned | YES  |     | NULL    |                |
            | conversions        | int(11)          | YES  |     | NULL    |                |
            | cost               | decimal(7,2)     | YES  |     | NULL    |                |
            | revenue            | decimal(7,2)     | YES  |     | NULL    |                |

           return array(
            'campaign_name' => $report['name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $report['esp_account_id'],
            'esp_internal_id' => $report['internal_id'],
            'datetime' => '0000-00-00', //$report[''],
            'name' => $report['name'],
            'subject' => $report['subject'],
            'from' => $report['from_name'],
            'from_email' => $report['from_email'],
            'e_sent' => $report['sent'],
            'delivered' => $report['delivered'],
            'bounced' => (int)$report['hard_bounces'],
            'optouts' => $report['unsubs'],
            'e_opens' => $report['opens'],
            'e_clicks' => $report['clicks']
        ); 
         */
    }
}
