<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\YamlProcessingService;
use Storage;

class ProcessYamlFiles extends Command
{
    const PROCESSMODE_ERROR_MESSAGE = 'Process mode is required. Please indicate --import or --export.';

    private $service;
    private $processMode;

    private $models = [ 'NavigationParent' , 'Page' , 'FrontendFeature' ] ;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processYaml:permissions {--import} {--export}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command syncs records from the indicated table and the associated YAML file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( YamlProcessingService $yamlService )
    {
        parent::__construct();
        $this->service = $yamlService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processOptions();

        if ( $this->processMode === 'export' ) {
            $this->exportPermissions();
        } else {
            $this->importPermissions();
        }
    }

    private function processOptions()
    {

        if ( $this->option( 'export' ) ) {
            $this->processMode = 'export';
        } elseif ( $this->option( 'import') ) {
            $this->processMode = 'import';
        } else {
            $this->error( self::PROCESSMODE_ERROR_MESSAGE );

            return;
        }
    }

    private function exportPermissions()
    {
        foreach ( $this->models as $modelName ) {
            $model = \App::make( '\\App\Models\\' . $modelName );

            $this->service->exportToYaml( $model , strtolower($modelName) . '.yaml' );
        }
    }

    private function importPermissions()
    {
        foreach ( $this->models as $modelName ) {
            $model = \App::make( '\\App\Models\\' . $modelName );

            try {
                $this->service->importYaml( $model , strtolower($modelName) . '.yaml' );
            } catch ( \Exception $e ) {
                $this->error( $e->getMessage() );
            }
        }
    }
}
