<?php

return [

    'BhSuppressionReport' => [
        'type' => 'esp',
        'data' => [
            'esp' => 'BlueHornet',
            'accounts' => []
        ],
        'destination' => 'hornet7',
        'service' => 'SuppressionExportReport',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
        'setRange' => false,
    ],

    'CampaignerSuppressionReport' => [
        'type' => 'esp',
        'data' => [
            'esp' => 'Campaigner',
            'accounts' => [] # empty would mean all
        ],
        'destination' => 'hornet7',
        'service' => 'SuppressionExportReport',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
        'setRange' => false,
    ],

    'emailsForOpensClicks' => [
        'type' => 'esp',
        'data' => [
            'esp' => 'Publicators',
            'accounts' => ['PUB007']
        ],

        'destination' => 'espdata',
        'service' => 'GenericExportReport',
        'model' => 'EmailAction',
        'repo' => 'EmailActionsRepo',
    ],

    'ZxUnsubExport' => [
        'type' => 'offer',
        'data' => [
            'advertisers' => ['Sprint', 'Esurance']
        ],
        'destination' => 'ZxUnsubFtp',
        'service' => 'ZxSuppressionExportReport',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
    ],
    
];