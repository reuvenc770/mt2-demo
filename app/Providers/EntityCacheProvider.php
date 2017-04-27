<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EntityCacheProvider extends ServiceProvider
{
    protected $models = [];
    protected $events = [ 'created' , 'updated' , 'deleted' ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadModelsFromConfig();

        foreach ( $this->models as $classname ) {
            foreach ( $this->events as $currentEvent ) {
                $classname::$currentEvent( [ __CLASS__ , 'clearEntityCache' ] );
            }
        }
    }

    #part of interface
    public function register(){}

    public static function clearEntityCache ( $model ) {
        $service = \App::make( \App\Services\EntityCacheService::class );

        $service->forgetForModel( get_class( $model ) );
    }

    protected function loadModelsFromConfig () {
        $configDetails = config( \App\Services\EntityCacheService::CONFIG_FILE );

        foreach ( $configDetails as $repoClass => $currentDetails ) {
            $this->models = array_merge( $this->models , $currentDetails[ 'models' ] );
        }

        array_unique( $this->models );
    }
}
