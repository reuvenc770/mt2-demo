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
            'DataProcessingJob' => '20m',
            'DownloadSuppressionFromESP' => '20m',
            'DownloadUnsubTicket' => '10m',
            'ExportDeployCombineJob' => '1h',
            'ListProfileBaseExportJob' => '1h',
            'ProcessNewActionsJob' => '30m',
            'RetrieveApiReports' => '10m',
            'RetrieveDeliverableReports' => '10m',
            'RetrieveTrackingDataJob' => '2m',
            'RunTimeMonitorJob' => '1m',
            'S3RedshiftExportJob' => '1h',
            'ScheduledFilterResolver' => '20m',
            'ProcessFeedRecordsJob' => '1h',
            'GenerateEspUnsubReport' => '20m',
            'SendSuppressionsToMT1' => '20m',
            'CheckMt1RealtimeFeedProcessingJob' => '30m'
    ]
];
