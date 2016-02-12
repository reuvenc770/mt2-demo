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
    protected $signature = 'permissions:update --confirm';

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
    public function __construct( Route $route  )
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
        $test = $this->route;
        $routeCollection = $test::getRoutes();

        $this->info( 'Routes:' );
        foreach ( $routeCollection as $value ) {
            $this->info( $value->getName());
        }

        $this->info( 'Current Permissions:' );

        $this->info( \App\Models\Permission::all() );

    }
}
