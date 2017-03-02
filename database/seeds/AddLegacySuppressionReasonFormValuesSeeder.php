<?php

use Illuminate\Database\Seeder;

use App\Models\SuppressionReason;

class AddLegacySuppressionReasonFormValuesSeeder extends Seeder
{
    const TABLE = 'suppression_reasons';
    const MT2_LEGACY_REASON = 'MT2IM';

    public $legacyValues = [
        'CA' => 'CA Address' ,
        'PROFO' => 'Flagged Words' ,
        'NA001' => 'Suppression because of ESP - AllInclusive' ,
        'ESPBO' => 'Suppression because of ESP - Bounce' ,
        'CCALL' => 'Suppression because of ESP - ConstantContact AllInclusive' ,
        'E' => 'Suppression because of ESP - DEPRECATED' ,
        'EVB' => 'Suppression because of ESP - EmailVision bounce' ,
        'EVC' => 'Suppression because of ESP - EmailVision complaint' ,
        'KMB' => 'Suppression because of ESP - KobeMail bounce' ,
        'KMC' => 'Suppression because of ESP - KobeMail complaint' ,
        'MNBB' => 'Suppression because of ESP - MyNewsletterBuilder bounce' ,
        'MNBC' => 'Suppression because of ESP - MyNewsletterBuilder complaint' ,
        'NAB' => 'Suppression because of ESP - Netatlantic bounce' ,
        'NAC' => 'Suppression because of ESP - Netatlantic complaint' ,
        'SMTPB' => 'Suppression because of ESP - SMTP bounce' ,
        'SMTPC' => 'Suppression because of ESP - SMTP complaint' ,
        'I' => 'Suppression because of ImpressionWise' ,
        'ZB' => 'Suppression because of Zeta bounce' ,
        'ZC' => 'Suppression because of Zeta complaint' ,
        'B' => 'Suppression because of a bounce' ,
        'C' => 'Suppression because of a complaint' ,
        'ADVS' => 'Suppression via the mailing tool - Advertiser Screamer' ,
        'M' => 'Suppression via the mailing tool - DEPRECATED' ,
        'IPCMP' => 'Suppression via the mailing tool - IP Provider Complaint' ,
        'LOS' => 'Suppression via the mailing tool - List Owner Screamer' ,
        'SPAMT' => 'Suppression via the mailing tool - Spamtrap' ,
        'SKET' => 'Suppression via the mailing tool - Supersketch Spamtrap' ,
        'UNSUB' => 'Suppression via unsub account' ,
        'CAB' => 'Suppression because of ESP - Campaigner bounce' ,
        'CAC' => 'Suppression because of ESP - Campaigner complaint' ,
        'EMARO' => 'Suppression because of ESP - Maro' ,
        'ESPPU' => 'Suppression because of ESP - PUB'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Log::info( 'Starting to run seeder....' );

        foreach ( $this->legacyValues as $value => $label ) {
            SuppressionReason::where( 'legacy_status' , $label )->update( [ 'legacy_form_value' => $value ] );
        }

        \Log::info( 'Finsihed mapping....setting defaults....' );

        SuppressionReason::where( 'legacy_form_value' , '' )->update( [ 'legacy_form_value' => self::MT2_LEGACY_REASON ] );

        \Log::info( 'seeding done...' );
    }
}
