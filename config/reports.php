<?php

return [

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

    'ZxSprintUnsubExport' => [
        'type' => 'offer',
        'data' => [
            'advertisers' => ['Sprint'],
            'formatStrategy' => 'SprintFormatStrategy'
        ],
        'destination' => 'ZxUnsubFtp',
        'service' => 'ZxSuppressionExportReport',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
    ],

    'ZxEsuranceUnsubExport' => [
        'type' => 'offer',
        'data' => [
            'advertisers' => ['Esurance'],
            'formatStrategy' => 'JustEmailFormatStrategy'
        ],
        'destination' => 'ZxUnsubFtp',
        'service' => 'ZxSuppressionExportReport',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
    ],
    
];