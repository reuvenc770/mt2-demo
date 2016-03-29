<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Route;

class UpdatePermissionsFromRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update {--confirm}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will compare permissions with current routes and update permissions for new routes. If --confirm is used, each new route will ask for confirmation before being added.';

    protected $route;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( Route $route )
    {
        parent::__construct();

        $this->route = $route;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $missingRoutes = $this->getMissingRoutes();
        $disableConfirmation = ( $this->option( 'confirm' ) !== true );

        foreach ( $missingRoutes as $newRoute ) {
            if ( $disableConfirmation || $this->confirm( "Would you like to add {$newRoute} to the permission list?" ) ) {
                \App\Models\Permission::insert( [ 'name' => $newRoute ] );
                $this->info( "\tAdded {$newRoute} to permission list." );
            }
        }
    }

    protected function getMissingRoutes () {
        $routes = $this->getCurrentRoutes(); 
        $permissions = $this->getCurrentPermissions();

        return array_diff( $routes , $permissions );
    }

    protected function getCurrentRoutes () {
        $test = $this->route;

        $routeCollection = $test::getRoutes();

        $collector = [];
        foreach ( $routeCollection as $route ) {
            $collector []= $route->getName();
        }

        return $collector;
    }

    protected function getCurrentPermissions () {
        $permissions = \App\Models\Permission::addSelect( 'name' )->get();

        $collector = [];
        foreach ( $permissions as $permission ) {
            $collector []= $permission->name;
        }

        return $collector;
    }
}
