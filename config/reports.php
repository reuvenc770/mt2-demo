<?php

return [

    'SuppressionReport' => [
        'destination' => 'hornet7',
        'service' => 'SuppressionExportReport',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
        'setRange' => false,
    ],

    'emailsForOpensClicks' => [
        'destination' => 'espdata',
        'service' => 'GenericExportReport',
        'model' => 'EmailAction',
        'repo' => 'EmailActionsRepo',
    ],
    
];