<?php

use Illuminate\Database\Seeder;
use App\Models\EtlPickup;

class RedshiftExportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run() {
        $pickup = new EtlPickup();
        $pickup->name = 'Email-s3';
        $pickup->stop_point = 0;
        $pickup->save();

        $pickup = new EtlPickup();
        $pickup->name = 'EmailDomain-s3';
        $pickup->stop_point = 0;
        $pickup->save();

        $pickup = new EtlPickup();
        $pickup->name = 'Feed-s3';
        $pickup->stop_point = 0;
        $pickup->save();

        $pickup = new EtlPickup();
        $pickup->name = 'SuppressionGlobalOrange-s3';
        $pickup->stop_point = 18445430;
        $pickup->save();

        $pickup = new EtlPickup();
        $pickup->name = 'ListProfileFlatTable-s3';
        $pickup->stop_point = 20170104;
        $pickup->save();

        $pickup = new EtlPickup();
        $pickup->name = 'DomainGroup-s3';
        $pickup->stop_point = 0;
        $pickup->save();

        // RecordData and EmailFeedAssignments can't use pickup values

    }
}
