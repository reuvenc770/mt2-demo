<?php
/**
 *
 */

namespace App\Services;
use App\Services\API\EspBaseApi;
use App\Services\API\EmailDirectApi;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;

/**
 *
 */
class EmailDirectReportService extends AbstractReportService implements IDataService {
    private $invalidFields = array( 'Publication' , 'Links' );

    public function __construct ( ReportRepo $reportRepo , EmailDirectApi $api) {
        parent::__construct($reportRepo, $api);
    }

    public function retrieveApiStats ( $date ) {
        try {
            $this->api->setDate(array( 'date' => $date ));
            return $this->api->sendApiRequest();
        } catch ( Exception $e ) {
            throw $e;
        }
    }

    public function insertApiRawStats ( $rawStats ) {
        $convertedRecordCollection = array();
        $espAccountId = $this->api->getEspAccountId();

        foreach ( $rawStats as $rawCampaignStats ) {
            $convertedRecord = $this->mapToRawReport( $rawCampaignStats );
            $this->insertStats( $espAccountId , $convertedRecord );
            $convertedRecordCollection []= $convertedRecord;
        }

        Event::fire(new RawReportDataWasInserted($this, $convertedRecordCollection ) );
    }

    public function mapToStandardReport ( $data ) {
        $formatedData = $this->mapToRawReport( $data );

        return array(
            'deploy_id' => $data[ 'name' ],
            'sub_id' => $this->parseSubID($data['name']),
            'esp_account_id' => $this->api->getEspAccountId(),
            'datetime' => $data[ 'scheduled_date' ],
            'name' => $data[ 'campaign_id' ],
            'subject' => $data[ 'subject' ],
            'from' => $data[ 'from_name' ],
            'from_email' => $data[ 'from_email' ],
            'delivered' => $data[ 'delivered' ],
            'bounced' => (int)$data['hard_bounces'],
            'e_opens' => $data[ 'opens' ],
            'e_clicks' => $data[ 'total_clicks' ],
            'e_clicks_unique' => $data[ 'unique_clicks' ],
        );
    }

    public function mapToRawReport ( $rawData ) {
        $formattedData = array();

        array_walk( $rawData , function ( $campaignDatapoint , $key ) use ( &$formattedData ) {
            $isValidField = !in_array( $key , $this->invalidFields );

            if ( $isValidField ) {
                switch ( $key ) {
                    case 'CampaignID' :
                        $formattedData[ 'campaign_id' ] = $campaignDatapoint;
                        $formattedData[ 'internal_id' ] = $campaignDatapoint;
                        break;

                    case 'ArchiveURL' :
                        $formattedData[ 'archive_url' ] = $campaignDatapoint;
                        break;

                    case 'CTR' :
                        $formattedData[ 'ctr' ] = $campaignDatapoint;
                        break;

                    case 'Creative' :
                        $formattedData[ 'creative_id' ] = $campaignDatapoint[ 'CreativeID' ];
                        break;

                    default :
                        $formattedData[ snake_case( $key ) ] = $campaignDatapoint;
                }
            }
        } );

        return $formattedData;
    }

}
