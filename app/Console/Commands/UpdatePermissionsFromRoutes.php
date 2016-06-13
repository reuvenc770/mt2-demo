<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Route;
use Log;

use App\Services\PageService;
use App\Services\PermissionService;
use App\Services\PagePermissionService;
use App\Services\RoleService;

use App\Models\Permission; 

class UpdatePermissionsFromRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update {--C|confirm : Enable confirmation while processing permissions. } {--P|permissionName= : Permission to adjust. } {--G|grant : Grant Access} {--R|revoke : Revoke Access} {--O|crudOperation= : CRUD operation to grant access to.} {--p|page= : Page to assign CRUD access to.} {--r|role= : Role to grant/revoke permissions for.} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will compare permissions with current routes and update permissions for new routes. If --confirm is used, each new route will ask for confirmation before being added.';

    protected $route;
    protected $pageService;
    protected $permissionService;
    protected $pagePermissionService;
    protected $roleService;

    protected $routeTypeSearchMap = [
        "create" => [ "add" , "store" , "copy" , "upload" , "create" ] ,
        "update" => [ "edit" , "update" ] ,
        "delete" => [ "destroy" ]
    ];

    protected $validCrudOperations = []; 
    protected $confirmUser = false;
    protected $permissionName;
    protected $crudOperation;
    protected $pageRoute;
    protected $role;
    protected $privilege;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        Route $route ,
        PageService $pageService ,
        PermissionService $permissionService ,
        PagePermissionService $pagePermissionService ,
        RoleService $roleService
    ) {
        parent::__construct();

        $this->route = $route;
        $this->pageService = $pageService;
        $this->permissionService = $permissionService;
        $this->pagePermissionService = $pagePermissionService;
        $this->roleService = $roleService;

        $this->validCrudOperations = collect( [
            Permission::TYPE_CREATE ,
            Permission::TYPE_READ ,
            Permission::TYPE_UPDATE , 
            Permission::TYPE_DELETE
        ] );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processOptions();

        try {
            if ( $this->permissionRoleAndPrivilegePresent() ) {
                $this->info( ( $this->privilege === 'grant' ? 'Granting' : 'Revoking' ) . " '{$this->permissionName}' for '{$this->role}'" );
                
                $this->adjustPermissionForRole();
            } elseif ( $this->roleCrudPageAndPrivilegePresent() ) {
                $this->info( ( $this->privilege === 'grant' ? 'Granting' : 'Revoking' ) . " '{$this->crudOperation}' access to '{$this->pageRoute}' for '{$this->role}'." );

                $this->adjustPagePermissionsForRole();
            } elseif ( $this->permissionPresent() ){
                $this->info( "Processing '{$this->permissionName}'..." );

                $this->processPermission();
            } else {
                $this->info( "Looking for new permissions..." );

                $this->processNewRoutes();
            }
        } catch ( \Exception $e ) {
            Log::error( $e->getMessage() );

            return 1;
        }

        return 0;
    }

    protected function processOptions () {
        $this->confirmUser = $this->option( 'confirm' );
        $this->permissionName = $this->option( 'permissionName' );
        $this->pageRoute = $this->option( 'page' );
        $this->role = $this->option( 'role' );

        $this->crudOperation = $this->option( 'crudOperation' );

        if ( $this->invalidCrudOperation() ) {
            $errorMessage = "'{$this->crudOperation}' is invalid. Valid Operations: " . $this->validCrudOperations->toJSON();
            $this->error( $errorMessage );

            throw new \Exception( $errorMessage );
        }

        if ( $this->option( 'grant' ) ) {
            $this->privilege = 'grant';
        } elseif ( $this->option( 'revoke' ) ) {
            $this->privilege = 'revoke';
        }
    }

    protected function invalidCrudOperation () {
        return ( !is_null( $this->crudOperation ) && !$this->validCrudOperations->contains( $this->crudOperation ) );
    }

    protected function permissionRoleAndPrivilegePresent () {
        return (
            !is_null( $this->permissionName )
            && !is_null( $this->role )
            && !is_null( $this->privilege )
        );
    }

    protected function adjustPermissionForRole () {
        $this->roleService->adjustPermission( $this->role , $this->permissionName , $this->privilege );    
    }

    protected function roleCrudPageAndPrivilegePresent () {
        return (
            !is_null( $this->role )
            && !is_null( $this->crudOperation )
            && !is_null( $this->pageRoute )
        );
    }

    protected function adjustPagePermissionsForRole () {
        $pageId = $this->pageService->getPageId( $this->pageRoute );
        $permissions = $this->pagePermissionService->getCrudPermissions( $pageId , $this->crudOperation );

        foreach ( $permissions as $pagePermission ) {
            $this->roleService->adjustPermission( $this->role , $pagePermission , $this->privilege );    
        }
    }

    protected function permissionPresent () {
        return !is_null( $this->permissionName );
    }

    protected function processPermission() {
        if ( $this->isNewRoute( $this->permissionName ) ) {
            $permissionId = $this->permissionService->addPermission( $this->permissionName );
        } else {
            $permissionId = $this->permissionService->getId( $this->permissionName );

            if ( !is_int( $permissionId ) ) {
                $errorMessage = "Could not find ID for permission '{$this->permissionName}'";
                $this->error( $errorMessage );

                throw new \Exception( $errorMessage );
            }
        }

        $this->assignPermissionToPage( $permissionId );
    }

    protected function processNewRoutes () {
        $missingRoutes = $this->getMissingRoutes();

        $bar = $this->output->createProgressBar( count( $missingRoutes ) );

        foreach ( $missingRoutes as $newRoute ) {
            if (
                $this->confirmUser === false
                || $this->confirm( "Would you like to add {$newRoute} to the permission list?" )
            ) {

                $permissionId = $this->permissionService->addPermission( $newRoute );
                if ( $this->confirmUser === true ) {
                    $this->assignPermissionToPage( $permissionId );
                }
            }

            $bar->advance();
            $this->info( '' );
        }

        $bar->finish();
    }

    protected function assignPermissionToPage ( $permissionId ) {
        $pageList = $this->pageService->getAllPageNames();

        $finishedMapping = false;
        
        while ( !$finishedMapping ) {
            $chosenPage = $this->choice(
                'Which page does this permission belong to?' ,
                array_flatten( $pageList->toArray() ) , 
                0
            );

            $pageId = $this->pageService->getPageId( $chosenPage );

            $this->pagePermissionService->addPagePermission( $pageId , $permissionId );

            $finishedMapping = !$this->confirm( 'Would you like to add this permission to another page?' );
        }
    }

    protected function isNewRoute ( $routeName ) {
        $routes = $this->getCurrentRoutes(); 

        return !in_array( $routeName , $routes );
    }

    protected function getMissingRoutes () {
        $routes = $this->getCurrentRoutes(); 
        $permissions = $this->permissionService->getCurrentPermissionNames()->toArray();

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
}
