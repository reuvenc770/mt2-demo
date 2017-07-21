<?php

return [
    'cake' => [
        'lookback' => 5
    ],
    'uas' => [
        'lookback' => 75 // minutes
    ],
    'defaultLookback' => 5,
    'maxAttempts' => 10,

    //NOTE: command line signature parameter --runtime-threshold=xx if specified will override these job scoped default values
    //FORMAT: XX(s|m|h), e.g., 2s (seconds), 2m (minutes), 3h (hours)
    'runtimeThreshold' => [
            'SimpleTestJob' => '10s',
            'BestMoneySearchGetResponseContactUploadJob' => '30m',
            'DataProcessingJob' => '10m',
            'DownloadSuppressionFromESP' => '10m',
            'DownloadUnsubTicket' => '6m',
            'ExportDeployCombineJob' => '1h',
            'ListProfileBaseExportJob' => '1h',
            'ProcessNewActionsJob' => '30m',
            'RetrieveApiReports' => '5m',
            'RetrieveDeliverableReports' => '5m',
            'RetrieveTrackingDataJob' => '1m',
            'RunTimeMonitorJob' => '1m',
            'S3RedshiftExportJob' => '1h',
            'ScheduledFilterResolver' => '20m'
    ]
];