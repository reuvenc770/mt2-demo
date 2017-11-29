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
            'ProcessFirstPartyMissedFeedRecordsJob' => '15m',
            'ProcessFirstPartyFeedRecordsJob' => '5m',
            'ProcessThirdPartyMissedFeedRecordsJob' => '15m',
            'ProcessThirdPartyFeedRecordsJob' => '5m',
            'GenerateEspUnsubReport' => '20m',
            'SendSuppressionsToMT1' => '20m',
            'CheckMt1RealtimeFeedProcessingJob' => '30m',
            'SharePublicatorsUnsubsJob' => '1h',
            'domainExpirationNotifications' => '1m',
            'ScheduledNotificationQueueJob' => '1m',
            'ScheduledNotificationWorkerJob' => '1m',
            'ExportActionsJob' => '1h',
            'UpdateMissingMaroCampaignsJob' => '1m',
            'SyncModelsWithNewFeedsJob' => '1m',
            'AttributionValidationJob' => '10h',
            'VacuumRedshiftJob' => '10m',
            'RedshiftDataValidationJob' => '1h',
            'ProcessMt1BatchFeedFilesJob' => '5m',
            'ProcessMt1RealtimeFeedFilesJob' => '10m',
            'UpdateFeedCountJob' => '1m',
            'CheckMt1BatchFeedProcessingJob' => '20m',
            'FirstPartyReprocessingJob' => '10m',
            'DataConsistencyValidationJob' => '6h'
    ]
];
