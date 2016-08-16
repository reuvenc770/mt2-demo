<?php

use Illuminate\Database\Seeder;

class PermissionsCrudUpdateSeeder extends Seeder
{
    public $routeCrudMap = [
        "root" => "read" , 
        "login" => "read" , 
        "forget.getemail" => "read" , 
        "forget.postemail" => "read" , 
        "password.reset" => "read" , 
        "password.store" => "create" , 
        "sessions.create" => "create" , 
        "sessions.store" => "create" , 
        "sessions.destroy" => "delete" , 
        "home" => "read" , 
        "logout" => "read" , 
        "myprofile" => "read" , 
        "espapi.list" => "read" , 
        "espapi.add" => "create" , 
        "espapi.edit" => "update" , 
        "tools.recordlookup" => "read" , 
        "tools.bulksuppression" => "read" , 
        "ymlpcampaign.list" => "read" , 
        "ymlpcampaign.edit" => "update" , 
        "ymlpcampaign.add" => "create" , 
        "devtools.jobs" => "read" , 
        "user.list" => "read" , 
        "user.add" => "create" , 
        "user.edit" => "update" , 
        "feed.list" => "read" , 
        "feed.add" => "create" , 
        "feed.edit" => "update" , 
        "feed.attribution" => "read" , 
        "clientgroup.list" => "read" , 
        "clientgroup.add" => "create" , 
        "clientgroup.edit" => "update" , 
        "datacleanse.list" => "read" , 
        "datacleanse.add" => "create" , 
        "datacleanse.edit" => "update" , 
        "listprofile.list" => "read" , 
        "listprofile.add" => "create" , 
        "listprofile.edit" => "update" , 
        "role.list" => "read" , 
        "role.add" => "create" , 
        "role.edit" => "update" , 
        "dataexport.list" => "read" , 
        "dataexport.add" => "create" , 
        "dataexport.edit" => "update" , 
        "api.pager" => "read" , 
        "api.profile.update" => "update" , 
        "api.attachment.upload" => "create" , 
        "api.feed.attribution.list" => "read" , 
        "dataexport.update" => "update" , 
        "api.clientgroup.search" => "read" , 
        "api.clientgroup.all" => "read" , 
        "api.clientgroup.copy" => "create" , 
        "api.listprofile.copy" => "create" , 
        "api.listprofile.isps" => "read" , 
        "api.listprofile.sources" => "read" , 
        "api.listprofile.seeds" => "read" , 
        "api.listprofile.zips" => "read" , 
        "bulksuppression.update" => "update" , 
        "bulksuppression.transfer" => "read" , 
        "api.espapi.index" => "read" , 
        "api.espapi.store" => "create" , 
        "api.espapi.show" => "read" , 
        "api.espapi.update" => "update" , 
        "api.espapi.destroy" => "delete" , 
        "api.ymlp-campaign.index" => "read" , 
        "api.ymlp-campaign.store" => "create" , 
        "api.ymlp-campaign.show" => "read" , 
        "api.ymlp-campaign.update" => "update" , 
        "api.ymlp-campaign.destroy" => "delete" , 
        "api.feed.index" => "read" , 
        "api.feed.store" => "create" , 
        "api.feed.show" => "read" , 
        "api.feed.update" => "update" , 
        "api.feed.destroy" => "delete" , 
        "api.clientgroup.store" => "create" , 
        "api.clientgroup.show" => "read" , 
        "api.clientgroup.update" => "update" , 
        "api.clientgroup.destroy" => "delete" , 
        "api.user.index" => "read" , 
        "api.user.store" => "create" , 
        "api.user.show" => "read" , 
        "api.user.update" => "update" , 
        "api.user.destroy" => "delete" , 
        "api.datacleanse.index" => "read" , 
        "api.datacleanse.store" => "create" , 
        "api.listprofile.index" => "read" , 
        "api.listprofile.store" => "create" , 
        "api.listprofile.show" => "read" , 
        "api.listprofile.update" => "update" , 
        "api.listprofile.destroy" => "delete" , 
        "api.showinfo.store" => "create" , 
        "api.showinfo.show" => "read" , 
        "api.attribution.store" => "create" , 
        "api.bulksuppression.store" => "create" , 
        "api.attribution.bulk" => "read" , 
        "api.dataexport.index" => "read" , 
        "api.dataexport.store" => "create" , 
        "api.dataexport.show" => "read" , 
        "api.dataexport.update" => "update" , 
        "api.dataexport.destroy" => "delete" , 
        "api.isp.index" => "read" , 
        "api.role.permissions" => "read" , 
        "api.role.permissions.tree" => "read" , 
        "api.role.index" => "read" , 
        "api.role.store" => "create" , 
        "api.role.show" => "read" , 
        "api.role.update" => "update" , 
        "api.role.destroy" => "delete" , 
        "api.jobEntry.index" => "read" , 
        "api.mt1.clientgroup.clients" => "read" , 
        "api.mt1.client.generatelinks" => "read" , 
        "api.mt1.client.types" => "read" , 
        "api.mt1.advertiser.get" => "read" , 
        "api.mt1.country.get" => "read" , 
        "api.mt1.offercategory.get" => "read" , 
        "api.suppressionReason.index" => "read" , 
        "api.mt1.clientstatsgrouping.index" => "read" , 
        "api.mt1.clientgroup.index" => "read" , 
        "api.mt1.clientgroup.show" => "read" , 
        "api.mt1.uniqueprofiles.index" => "read" , 
        "api.mt1.uniqueprofiles.show" => "read" , 
        "api.mt1.esps.index" => "read"
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ( $this->routeCrudMap as $route => $crudType ) {
            DB::table( 'permissions' )
                ->where( 'name' , $route )
                ->update( [ 'crud_type' => $crudType ] );
        }
    }
}
