<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

use App\Models\Page;
use App\Models\Permission;
use App\Services\PageService;
use App\Services\PermissionService;
use App\Services\PagePermissionService;

class PagePermissionSeeder extends Seeder
{
    public $pageService;
    public $pagePermissionService;
    public $permissionService;

    public function __construct ( PageService $pageService , PermissionService $permissionService , PagePermissionService $pagePermissionService ) {
        $this->pageService = $pageService;
        $this->permissionService = $permissionService;
        $this->pagePermissionService = $pagePermissionService;
    }


    public $pagePermissionMap = [
        "client.attribution" => [
            "api.client.attribution.list" , 
            "client.attribution" , 
            "api.pager" , 
            "api.attachment.upload" , 
            "api.attribution.store" , 
            "api.attribution.bulk"
        ] ,
        "client.list" => [
            "client.list" , 
            "client.add" , 
            "client.edit" , 
            "api.pager" , 
            "api.mt1.clientstatsgrouping.index" , 
            "api.client.index" , 
            "api.client.store" , 
            "api.client.show" , 
            "api.client.update" , 
            "api.client.destroy" , 
            "api.mt1.client.generatelinks" , 
            "api.mt1.client.types"
        ] ,
        "clientgroup.list" => [
            "api.clientgroup.search" , 
            "api.clientgroup.all" , 
            "api.clientgroup.copy" , 
            "clientgroup.list" , 
            "clientgroup.add" , 
            "clientgroup.edit" , 
            "api.pager" , 
            "api.mt1.clientgroup.clients" , 
            "api.clientgroup.store" , 
            "api.clientgroup.show" , 
            "api.clientgroup.update" , 
            "api.clientgroup.destroy" , 
            "api.mt1.clientgroup.index" , 
            "api.mt1.clientgroup.show"
        ] ,
        "datacleanse.list" => [
            "datacleanse.list" , 
            "datacleanse.add" , 
            "datacleanse.edit" , 
            "api.pager" , 
            "api.datacleanse.index" , 
            "api.datacleanse.store" , 
            "api.mt1.advertiser.get" , 
            "api.mt1.country.get" , 
            "api.mt1.offercategory.get"
        ] ,
        "dataexport.list" => [
            "dataexport.update" , 
            "dataexport.list" , 
            "dataexport.add" , 
            "dataexport.edit" , 
            "api.pager" , 
            "api.mt1.uniqueprofiles.index" , 
            "api.mt1.clientgroup.index" , 
            "api.mt1.esps.index" , 
            "api.dataexport.index" , 
            "api.dataexport.store" , 
            "api.dataexport.show" , 
            "api.dataexport.update" , 
            "api.dataexport.destroy"
        ] ,
        "devtools.jobs" => [
            "devtools.jobs" , 
            "api.jobEntry.index"
        ] ,
        "espapi.list" => [
            "api.espapi.index" , 
            "api.espapi.store" , 
            "api.espapi.show" , 
            "api.pager" , 
            "espapi.list" , 
            "espapi.add" , 
            "espapi.edit" , 
            "api.espapi.update" , 
            "api.espapi.destroy"
        ] ,
        "home" => [
            "root" , 
            "home"
        ] ,
        "listprofile.list" => [
            "api.listprofile.copy" , 
            "api.listprofile.isps" , 
            "api.listprofile.sources" , 
            "api.listprofile.seeds" , 
            "api.listprofile.zips" , 
            "listprofile.list" , 
            "listprofile.add" , 
            "listprofile.edit" , 
            "api.pager" , 
            "api.mt1.uniqueprofiles.show" , 
            "api.listprofile.index" , 
            "api.listprofile.store" , 
            "api.listprofile.show" , 
            "api.listprofile.update" , 
            "api.listprofile.destroy" , 
            "api.isp.index" , 
            "api.mt1.uniqueprofiles.index"
        ] ,
        "role.list" => [
            "role.list" , 
            "role.add" , 
            "role.edit" , 
            "api.pager" , 
            "api.role.permissions" , 
            "api.role.permissions.tree" , 
            "api.role.index" , 
            "api.role.store" , 
            "api.role.show" , 
            "api.role.update" , 
            "api.role.destroy"
        ] ,
        "tools.bulksuppression" => [
            "bulksuppression.update" , 
            "bulksuppression.transfer" , 
            "tools.bulksuppression" , 
            "api.attachment.upload" , 
            "api.bulksuppression.store"
        ] ,
        "tools.recordlookup" => [
            "tools.recordlookup" , 
            "api.showinfo.store" , 
            "api.showinfo.show" , 
            "api.suppressionReason.index"
        ] ,
        "user.list" => [
            "user.list" , 
            "user.add" , 
            "user.edit" , 
            "login" , 
            "forget.getemail" , 
            "forget.postemail" , 
            "password.reset" , 
            "password.store" , 
            "sessions.create" , 
            "sessions.store" , 
            "sessions.destroy" , 
            "api.pager" , 
            "logout" , 
            "myprofile" , 
            "api.profile.update" , 
            "api.user.index" , 
            "api.user.store" , 
            "api.user.show" , 
            "api.user.update" , 
            "api.user.destroy"
        ] ,
        "ymlpcampaign.list" => [
            "ymlpcampaign.add" , 
            "api.pager" , 
            "ymlpcampaign.list" , 
            "ymlpcampaign.edit" , 
            "api.ymlp-campaign.index" , 
            "api.ymlp-campaign.store" , 
            "api.ymlp-campaign.show" , 
            "api.ymlp-campaign.update" , 
            "api.ymlp-campaign.destroy"
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = Page::all();
        $permissions = Permission::all();


        foreach ( $this->pagePermissionMap as $currentPage => $pageRoutes ) {
            $currentPageId = $this->pageService->getPageId( $currentPage ); 

            foreach ( $pageRoutes as $currentRoute ) {
                $currentPermissionId = $this->permissionService->getId( $currentRoute ); 

                $this->pagePermissionService->addPagePermission( $currentPageId , $currentPermissionId );
            }
        }
    }
}
