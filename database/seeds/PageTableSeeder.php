<?php

use Illuminate\Database\Seeder;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table( 'pages' )->insert(
            [ 'name' => 'home' ] ,
            [ 'name' => 'espapi.list' ] ,
            [ 'name' => 'client.list' ] ,
            [ 'name' => 'role.list' ] ,
            [ 'name' => 'user.list' ] ,
            [ 'name' => 'clientgroup.list' ] ,
            [ 'name' => 'tools.recordlookup' ] ,
            [ 'name' => 'listprofile.list' ] ,
            [ 'name' => 'dataexport.list' ] ,
            [ 'name' => 'ymlpcampaign.list' ] ,
            [ 'name' => 'devtools.jobs' ] ,
            [ 'name' => 'tools.bulksuppression' ] ,
            [ 'name' => 'client.attribution' ] ,
            [ 'name' => 'datacleanse.list' ]
        );
    }
}
