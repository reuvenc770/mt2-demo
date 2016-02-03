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

        Event::fire( new RawReportDataWasInserted( $this->api->getApiName(), $espAccountId, $convertedRecordCollection ) );
    }

    public function mapToStandardReport ( $data ) {
        return array(
            "internal_id" => $data[ 'campaign_id' ] ,
            "esp_account_id" => $this->api->getEspAccountId() ,
            "name" => $data[ 'name' ] ,
            "subject" => $data[ 'subject' ] ,
            "opens" => $data[ 'opens' ] ,
            "clicks" => $data[ 'total_clicks' ]
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
