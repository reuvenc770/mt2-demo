<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon::now();

        DB::insert('insert into pages (id, name)

            values

            ("1", "home"), 
            ("2", "espapi.list"), 
            ("3", "feed.list"), 
            ("4", "role.list"), 
            ("5", "user.list"), 
            ("6", "clientgroup.list"), 
            ("7", "tools.recordlookup"), 
            ("8", "listprofile.list"), 
            ("9", "dataexport.list"), 
            ("10", "ymlpcampaign.list"), 
            ("11", "devtools.jobs"), 
            ("12", "tools.bulksuppression"), 
            ("14", "datacleanse.list"), 
            ("15", "dba.list"), 
            ("16", "proxy.list"), 
            ("17", "registrar.list"), 
            ("18", "domain.list"), 
            ("19", "mailingtemplate.list"), 
            ("20", "report.list"), 
            ("21", "deploy.list"), 
            ("22", "cfs.list"), 
            ("23", "attribution.list"), 
            ("24", "esp.list"), 
            ("25", "isp.list"), 
            ("26", "ispgroup.list"), 
            ("27", "feedgroup.list"), 
            ("28", "tools"), 
            ("29", "client.list"), 
            ("30", "tools.sourceurlsearch") ');
    }
}
