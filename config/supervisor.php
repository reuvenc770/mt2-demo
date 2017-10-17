<?php

return [
    // Map between program/group names and queue names. A reverse of the below.
    'program' => [
        'laravel-worker-group' => [
            'queueName' => 'default',
            'canModify' => false,
            'processName' => 'laravel-worker-group_'
        ],
        'Bronto' => [
            'queueName' => 'Bronto',
            'canModify' => true,
            'processName' => 'Bronto_'
        ],
        'Maro' => [
            'queueName' => 'Maro',
            'canModify' => true,
            'processName' => 'Maro_'
        ],
        'laravel-worker-fileDownloads' => [
            'queueName' => 'fileDownloads',
            'canModify' => true,
            'processName' => 'laravel-worker-fileDownloads_'
        ],
        'laravel-worker-BlueHornet' => [
            'queueName' => 'BlueHornet',
            'canModify' => true,
            'processName' => 'BlueHornet_'
        ],
        'Daddy-Warbucks' => [
            'queueName' => 'orphanage',
            'canModify' => true,
            'processName' => 'Daddy-Warbucks_'
        ],
        'Campaigner' => [
            'queueName' => 'Campaigner',
            'canModify' => true,
            'processName' => 'Campaigner_'
        ],
        'Publicators' => [
            'queueName' => 'Publicators',
            'canModify' => true,
            'processName' => 'Publicators_'
        ],
        'attribution' => [
            'queueName' => 'attribution',
            'canModify' => true,
            'processName' => 'attribution_'
        ],
        'filters' => [
            'queueName' => 'filters',
            'canModify' => true,
            'processName' => 'filters_'
        ],
        'AWeber' => [
            'queueName' => 'AWeber',
            'canModify' => true,
            'processName' => 'AWeber_'
        ],
        'Monitor' => [
            'queueName' => 'Monitor',
            'canModify' => true,
            'processName' => 'Monitor_'
        ],
        'Notifications' => [
            'queueName' => 'scheduled_notifications',
            'canModify' => false,
            'processName' => 'Notifications_'
        ],
        'rawFeedProcessing' => [
            'queueName' => 'rawFeedProcessing',
            'canModify' => false,
            'processName' => 'rawFeedProcessing_'
        ],
        'RecordProcessing' => [
            'queueName' => 'RecordProcessing',
            'canModify' => false,
            'processName' => 'RecordProcessing_'
        ],
        'ListProfiles' => [
            'queueName' => 'ListProfile',
            'canModify' => true,
            'processName' => 'ListProfiles_'
        ],

    ],

    // A list of queues
    'queueProgramMap' => [
        'default' => 'laravel-worker-group',
        'Bronto' => 'Bronto',
        'Maro' => 'Maro',
        'fileDownloads' => 'laravel-worker-fileDownloads',
        'BlueHornet' => 'laravel-worker-BlueHornet',
        'orphanage' => 'Daddy-Warbucks',
        'Campaigner' => 'Campaigner',
        'Publicators' => 'Publicators',
        'attribution' => 'attribution',
        'filters' => 'filters',
        'AWeber' => 'AWeber',
        'Monitor' => 'Monitor',
        'scheduled_notifications' => 'Notifications',
        'rawFeedProcessing' => 'rawFeedProcessing',
        'RecordProcessing' => 'RecordProcessing',
        'ListProfile' => 'ListProfiles',
    ],


];