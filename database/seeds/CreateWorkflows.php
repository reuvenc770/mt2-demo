<?php

use Illuminate\Database\Seeder;
use App\Models\EspWorkflow;
use App\Models\EspWorkflowStep;

class CreateWorkflows extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $br001 = EspApiAccount::getEspAccountDetailsByName('BR001')->id;
        $cy001 = EspApiAccount::getEspAccountDetailsByName('CY001')->id;


        /**
            Workflow 1: Section 8
        */

        $w1 = new EspWorkflow();
        $w1->name = 'Section 8';
        $w1->esp_account_id = $br001;
        $w1->status = 0;
        $w1->save();

        // Steps

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (1, 1, 1334859, 9208),
        (1, 2, 1343477, 17227),
        (1, 3, 1334897, 10390),
        (1, 4, 1334927, 10513),
        (1, 5, 1341531, 17189),
        (1, 6, 1338989, 16699),
        (1, 7, 1334903, 9208),
        (1, 8, 1334942, 12107),
        (1, 9, 1343512, 17227),
        (1, 10, 1343469, 17242),
        (1, 11, 1334922, 12451),
        (1, 12, 1345701, 16471),
        (1, 13, 1334881, 12451),
        (1, 14, 1341530, 15945),
        (1, 15, 1344025, 16834),
        (1, 16, 1334862, 12930),
        (1, 17, 1341569, 14498),
        (1, 18, 1344793, 15179),
        (1, 19, 1344637, 9132),
        (1, 20, 1336578, 15213),
        (1, 21, 1343450, 16354),
        (1, 22, 1344097, 16961),
        (1, 23, 1346147, 17409),
        (1, 24, 1346153, 15929),
        (1, 25, 1334937, 11549),
        (1, 26, 1334944, 9208)");


        /**
            Workflow 2: ARI
        */

        $w2 = new EspWorkflow();
        $w2->name = 'ARI';
        $w2->esp_account_id = $cy001;
        $w2->status = 0;
        $w2->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (2, 1, 1344715, 16226),
        (2, 2, 1346429, 12402),
        (2, 3, 1344716, 16834),
        (2, 4, 1344770, 14427),
        (2, 5, 1346430, 10390),
        (2, 6, 1344752, 16259),
        (2, 7, 1344861, 15230),
        (2, 8, 1344863, 16634),
        (2, 9, 1344772, 16269)");

        /**
            Workflow 3: ARI2
        */

        $w3 = new EspWorkflow();
        $w3->name = 'ARI2';
        $w3->esp_account_id = $cy001;
        $w3->status = 0;
        $w3->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (3, 1, 1344712, 12402),
        (3, 2, 1344714, 16226),
        (3, 3, 1344717, 16834),
        (3, 4, 1344865, 16848),
        (3, 5, 1344750, 13842),
        (3, 6, 1344866, 16634),
        (3, 7, 1344869, 10390),
        (3, 8, 1346431, 15230),
        (3, 9, 1346433, 16269)");

        /**
            Workflow 4: GNS
        */

        $w4 = new EspWorkflow();
        $w4->name = 'GNS';
        $w4->esp_account_id = $cy001;
        $w4->status = 0;
        $w4->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (4, 1, 1347715, 16005),
        (4, 2, 1347716, 15999),
        (4, 3, 1347734, 16834),
        (4, 4, 1347733, 13723),
        (4, 5, 1347749, 16457),
        (4, 6, 1347751, 13842),
        (4, 7, 1347756, 15230),
        (4, 8, 1347759, 14427)");
    

    /**
        Workflow 5: RMP Completes
    */

        $w5 = new EspWorkflow();
        $w5->name = 'RMP-Completes-Reponders';
        $w5->esp_account_id = $br001; // Goes to Bronto of some kind (see 669)
        $w5->status = 0;
        $w5->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (5, 1, 1338621, 9208),
        (5, 2, 1343546, 17226),
        (5, 3, 1338618, 15929),
        (5, 4, 1338616, 15794),
        (5, 5, 1338615, 15832),
        (5, 6, 1338620, 12451),
        (5, 7, 1338626, 16473),
        (5, 8, 1339561, 16968),
        (5, 9, 1338630, 10057)");

        /**
            Workflow 6: RMP Completes
        */

        $w6 = new EspWorkflow();
        $w6->name = 'RMP-Completes';
        $w6->esp_account_id = $br001;
        $w6->status = 0;
        $w6->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (6, 1, 1338621, 9208)");

        /**
            Workflow 7: RMP Abandons
        */


        $w7 = new EspWorkflow();
        $w7->name = 'RMP-Abandons';
        $w7->esp_account_id = $br001;
        $w7->status = 0;
        $w7->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (7, 1, 1338639, 17150),
        (7, 2, 1338640, 16838),
        (7, 3, 1338641, 12084),
        (7, 4, 1338642, 9086)");

        /**
            Workflow 8: RMP Abandons Responders
        */

        $w8 = new EspWorkflow();
        $w8->name = 'RMP-Abandons-Responders';
        $w8->esp_account_id = $br001;
        $w8->status = 0;
        $w8->save();

        DB::statement("INSERT INTO esp_workflow_steps (first_party_workflow_id, step, deploy_id, offer_id) 
        VALUES
        (8, 1,1338674, 9208),
        (8, 2,1338637, 16838),
        (8, 3,1343519, 17226),
        (8, 4,1338645, 13458),
        (8, 5,1341904, 15945),
        (8, 6,1338644, 12084),
        (8, 7,1343760, 17150),
        (8, 8,1338678, 15794),
        (8, 9,1338679, 15929),
        (8, 10,1338681, 10963),
        (8, 11,1338773, 15832)");
    }
}
