<?php

return [

    'bhSuppression' => [
        'destination' => 'hornet7',
        'service' => 'BlueHornetSuppressionExportReportService',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
        'model2' => 'SuppressionReason',
    ],

    'emailsForOpensClicks' => [
        'destination' => 'espdata',
        'service' => 'GenericExportReportService',
        'model' => 'EmailAction',
        'repo' => 'EmailActionsRepo',
    ],
    
];