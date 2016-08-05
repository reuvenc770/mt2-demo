<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\AbstractEtlService;
use App\Repositories\Attribution\RecordReportRepo;
use App\Services\CakeConversionService;
use App\Repositories\Attribution\AttributionEmailActionsRepo;
use App\Services\EmailRecordService;
use App\Services\SuppressionService;
use App\Services\StandardReportService;
use App\Repositories\EtlPickupRepo;
use App\Models\Suppression;

class RecordAggregatorService extends AbstractEtlService {
    const JOB_NAME = 'PopulateAttributionRecordReport';

    protected $recordRepo;
    protected $conversionService;
    protected $actionsRepo;
    protected $emailService;
    protected $suppressionService;
    protected $standardReportService;
    protected $etlPickupRepo;

    public function __construct (
        RecordReportRepo $recordRepo ,
        CakeConversionService $conversionService ,
        AttributionEmailActionsRepo $actionsRepo ,
        EmailRecordService $emailService ,
        SuppressionService $suppressionService ,
        StandardReportService $standardReportService ,
        EtlPickupRepo $etlPickupRepo
    ) {
        $this->recordRepo = $recordRepo;
        $this->conversionService = $conversionService;
        $this->actionsRepo = $actionsRepo;
        $this->emailService = $emailService;
        $this->suppressionService = $suppressionService;
        $this->standardReportService = $standardReportService;
        $this->etlPickupRepo = $etlPickupRepo;
    }

    public function extract($lookback = null) {
        $startPoint = $this->etlPickupRepo->getLastInsertedForName( self::JOB_NAME );
        $endPoint = $this->actionsRepo->minId();
        $segmentEnd = $startPoint - 1000;

        $totalRecords = 0;
        while ( $startPoint > $endPoint && $totalRecords < 50000 ) {
            $limit = 1000;
            $segmentEnd = $this->actionsRepo->prevNRows($startPoint, $limit);

            echo "Segment End: " . $segmentEnd . PHP_EOL;

            // If we've overshot, $segmentEnd will be null
            $segmentEnd = $segmentEnd ? $segmentEnd : $endPoint;

            echo "Starting " . self::JOB_NAME . " collection at row $startPoint, ending at $segmentEnd" . PHP_EOL;
            $data = $this->actionsRepo->pullAggregatedActions( $segmentEnd , $startPoint);

            if ($data) {
                $insertData = [];
                foreach ($data as $row) {
                    $insertData []= $this->mapToAttributionRecordTable($row);
                }

                $this->recordRepo->massInsertActions($insertData);
                $startPoint = $segmentEnd;

                $totalRecords += count( $data );
            }
            else {
                // if no data received
                echo "No data received" . PHP_EOL;
                continue;
            }
        }

        $this->etlPickupRepo->updatePosition(self::JOB_NAME, $segmentEnd);
    }

    public function load() {}

    protected function mapToAttributionRecordTable ( $row ) {
        $conversions = $this->conversionService->getByDeployEmailDate( $row->deploy_id , $row->email_id , $row->date );
        $suppressions = $this->getSuppressionData( $row->deploy_id , $row->email_id , $row->date );

        return [
            'email_id' => $row->email_id ,
            'deploy_id' => $row->deploy_id ,
            'date' => $row->date ,
            'delivered' => $row->delivered ,
            'opened' => $row->opened ,
            'clicked' => $row->clicked ,
            'converted' => $conversions[ 'conversions' ] ,
            'revenue' => $conversions[ 'revenue' ] ,
            'unsubbed' => $suppressions[ 'unsubbed' ] ,
            'bounced' => $suppressions[ 'bounced' ]
        ];
    }

    protected function getSuppressionData ( $deployId , $emailId , $date ) {
        $emailAddress = $this->emailService->getEmailAddress( $emailId );
        $internalEspId = $this->standardReportService->getInternalEspId( $deployId );

        $suppressions = $this->suppressionService->getByInternalEmailDate( $internalEspId , $emailAddress , $date );

        $results = [ "unsubbed" => 0 , "bounced" => 0 ];

        foreach ( $suppressions as $currentSuppression ) {
            switch ( $currentSuppression->type_id ) {
                case Suppression::TYPE_UNSUB:
                    $results[ "unsubbed" ]++;
                break;

                case Suppression::TYPE_HARD_BOUNCE:
                    $results[ "bounced" ]++;
                break;
            }
        }

        return $results;
    }
}
