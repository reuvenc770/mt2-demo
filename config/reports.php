<?php

return [

    'bhSuppression' => [
        'destination' => 'hornet7',
        'service' => 'BlueHornetSuppressionExportReportService',
        'model' => 'Suppression',
        'repo' => 'SuppressionRepo',
    ],

    'emailsForOpensClicks' => [
        'destination' => 'espdata',
        'service' => 'GenericExportReportService',
        'model' => 'EmailAction',
        'repo' => 'EmailActionsRepo',
    ],
    
];