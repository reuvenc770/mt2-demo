<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;
use App\Repositories\Attribution\FeedReportRepo;
use App\Services\EmailFeedAssignmentService;
use App\Services\EmailFeedInstanceService;
use App\Exceptions\AggregatorServiceException;

class FeedAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;
    protected $emailFeedAssignmentService;
    protected $emailFeedInstanceService;
    protected $feedRepo;

    protected $modelId;

    public function __construct ( RecordReportService $recordReport , EmailFeedAssignmentService $emailFeedAssignmentService , EmailFeedInstanceService $emailFeedInstanceService , FeedReportRepo $feedRepo ) {
        $this->recordReport = $recordReport;
        $this->emailFeedAssignmentService = $emailFeedAssignmentService;
        $this->emailFeedInstanceService = $emailFeedInstanceService;
        $this->feedRepo = $feedRepo;
    }

    public function setModelId ( $modelId ) {
        $this->modelId = $modelId;

        $this->feedRepo->setModelId( $modelId );
        $this->emailFeedAssignmentService->setLevelModel( $modelId );
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new AggregatorServiceException( 'RecordReportService needed. Please inject a service.' );
        }

        if ( !isset( $this->emailFeedAssignmentService ) ) {
            throw new AggregatorServiceException( 'EmailFeedAssignmentService needed. Please inject a service.' );
        }

        if ( !isset( $this->emailFeedInstanceService ) ) {
            throw new AggregatorServiceException( 'EmailFeedInstanceService needed. Please inject a service.' );
        }

        if ( !isset( $this->feedRepo ) ) {
            throw new AggregatorServiceException( 'FeedReportRepo needed. Please inject a repo.' );
        }

        $this->setDateRange( $dateRange );

        $this->buildRecords();

        $this->flattenStruct( 1 );

        $this->saveReport();
    }

    protected function getBaseRecords () {
        return $this->recordReport->getByDateRange( $this->dateRange );
    }

    protected function processBaseRecord ( $baseRecord ) {
        $date = $baseRecord->date;
        $feedId = $this->emailFeedAssignmentService->getAssignedFeed( $baseRecord->email_id , $this->modelId );
        
        $this->createRowIfMissing( $date , $feedId );

        $currentRow = &$this->getCurrentRow( $date , $feedId );

        if ( is_null( $this->modelId ) ) {
            if ( is_null( $currentRow[ 'mt1_uniques' ] ) ) {
                $currentRow[ 'mt1_uniques' ] = (int)$this->emailFeedInstanceService->getMt1UniqueCountForFeedAndDate( $feedId , $date );
            }

            if ( is_null( $currentRow[ 'mt2_uniques' ] ) ) {
                $currentRow[ 'mt2_uniques' ] = (int)$this->emailFeedInstanceService->getMt2UniqueCountForFeedAndDate( $feedId , $date );
            }
        }

        $currentRow[ "revenue" ] = (
            ( $currentRow[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER ) + ( $baseRecord[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER )
        ) / parent::WHOLE_NUMBER_MODIFIER;
    }

    protected function formatRecordToSqlString ( $record ) {
        return "( 
            '{$record[ 'feed_id' ]}' ,
            '{$record[ 'revenue' ]}' ,
            '{$record[ 'mt1_uniques' ]}' ,
            '{$record[ 'mt2_uniques' ]}' ,
            '{$record[ 'date' ]}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        $this->feedRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $feedId ) {
        if ( !isset( $this->recordStruct[ $date ][ $feedId ] ) ) {
            $this->recordStruct[ $date ][ $feedId ] = [
                "feed_id" => $feedId ,
                "revenue" => 0 ,
                "mt1_uniques" => null ,
                "mt2_uniques" => null ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $feedId ) {
        return $this->recordStruct[ $date ][ $feedId ];
    }
}
