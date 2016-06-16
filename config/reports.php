<?php

return [

    'bhSuppression' => [
        'destination' => 'hornet7',
        'service' => 'BlueHornetSuppressionExportReport',
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