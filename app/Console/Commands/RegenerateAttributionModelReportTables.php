<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\AttributionModel;

use App\Models\AttributionLevel;
use App\Repositories\AttributionLevelRepo;

use App\Models\EmailFeedAssignment;
use App\Repositories\EmailFeedAssignmentRepo;

use App\Models\AttributionFeedReport;
use App\Repositories\AttributionFeedReportRepo;

use DB;

class RegenerateAttributionModelReportTables extends Command
{
    const TABLE_TYPE_LEVEL = 'level';
    const TABLE_TYPE_ASSIGN = 'assign';
    const TABLE_TYPE_REPORT = 'report';

    protected $levelTables = [];
    protected $assignTables = [];
    protected $reportTables = [];


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attribution:generateModelTables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will automatically generate any missing model tables.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ( AttributionModel::all() as $currentModel ) {
            if ( $this->levelTableMissing( $currentModel->id ) ) {
                $this->info( 'Generating level table for model ' . $currentModel->id );

                AttributionLevelRepo::generateTempTable( $currentModel->id );
            }

            if ( $this->assignTableMissing( $currentModel->id ) ) {
                $this->info( 'Generating assignment table for model ' . $currentModel->id );

                EmailFeedAssignmentRepo::generateTempTable( $currentModel->id );
            }

            if ( $this->reportTableMissing( $currentModel->id ) ) {
                $this->info( 'Generating report table for model ' . $currentModel->id );

                AttributionFeedReportRepo::generateTempTable( $currentModel->id );
            }
        }
    }

    protected function levelTableMissing ( $modelId ) {
        if ( empty( $this->levelTables ) ) {
            $this->levelTables = $this->getTables( self::TABLE_TYPE_LEVEL );
        }

        return !in_array( AttributionLevel::BASE_TABLE_NAME . $modelId , $this->levelTables );
    }

    protected function assignTableMissing ( $modelId ) {
        if ( empty( $this->assignTables ) ) {
            $this->assignTables = $this->getTables( self::TABLE_TYPE_ASSIGN );
        }

        return !in_array( EmailFeedAssignment::BASE_TABLE_NAME . $modelId , $this->assignTables );
    }

    protected function reportTableMissing ( $modelId ) {
        if ( empty( $this->reportTables ) ) {
            $this->reportTables = $this->getTables( self::TABLE_TYPE_REPORT );
        }

        return !in_array( AttributionFeedReport::BASE_TABLE_NAME . $modelId , $this->reportTables );
    }

    protected function getTables ( $type ) {
        $tableCollection = [];

        switch ( $type ) {
            case self::TABLE_TYPE_LEVEL :
                $tableCollection = DB::select( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'attribution_levels_model\_%'" );
            break;

            case self::TABLE_TYPE_ASSIGN :
                $tableCollection = DB::select( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'email_feed_assignments_model\_%'" );
            break;

            case self::TABLE_TYPE_REPORT :
                $tableCollection = DB::select( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'attribution_feed_report\_%'" );
            break;
        }

        $tableNames = [];
        foreach ( $tableCollection as $current ) {
            $tableNames []= $current->TABLE_NAME;
        }

        return $tableNames;
    }
}
