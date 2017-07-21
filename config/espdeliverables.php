<?php

return [
    "BlueHornet" => [
        "pipes" => [
            "actions" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getCampaigns', 'runtimeThreshold' => '5m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '5m'],
                ['name' => 'checkTicketStatus' , 'runtimeThreshold' => '5m'],
                ['name' => 'downloadTicketFile' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'synchronousSaveTypeRecords' , 'runtimeThreshold' => '5m'],
                ['name' => 'cleanUp' , 'runtimeThreshold' => '5m']
            ] ,
            "delivered" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getDeliverableCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '5m'],
                ['name' => 'checkTicketStatus' , 'runtimeThreshold' => '5m'],
                ['name' => 'downloadTicketFile' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'synchronousSaveTypeRecords' , 'runtimeThreshold' => '5m'],
                ['name' => 'cleanUp' , 'runtimeThreshold' => '5m']
            ],
            "rerun" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getRerunCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '5m'],
                ['name' => 'checkTicketStatus' , 'runtimeThreshold' => '5m'],
                ['name' => 'downloadTicketFile' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'synchronousSaveTypeRecords' , 'runtimeThreshold' => '5m'],
                ['name' => 'removeDeploys', 'runtimeThreshold' => '5m'],
                ['name' => 'cleanUp',  'runtimeThreshold' => '5m']
            ]
        ]
    ] ,
    "Campaigner" => [
        "pipes" => [
            "delivered" => [
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords',  'runtimeThreshold' => '5m']
            ] ,
            "actions" => [
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '5m']
            ] ,
            "rerun" => [
                ['name' => 'getRerunCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords', 'runtimeThreshold' => '5m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '5m']
            ]
        ]
    ] ,
    "Maro" => [
        "pipes" => [
            "default" => [
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedRecords' ,  'runtimeThreshold' => '5m']
            ] ,
            "delivered" => [
                ['name' => 'getDeliverableCampaigns', 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedCampaignRecords' ,  'runtimeThreshold' => '5m']
            ],
            "rerun" => [
                ['name' => 'getRerunCampaigns', 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes', 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedCampaignRecords', 'runtimeThreshold' => '5m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '5m']
            ]
        ]
    ] ,
    "EmailDirect" => [
        "pipes" => [
            "default" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '5m']
            ]
        ]
    ],
    "Ymlp" => [
        "pipes" => [
            "default" => [
                ['name' => 'getCampaigns', 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList', 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '5m']
            ]
        ]
    ] ,
    "AWeber" => [
        "pipes" => [
            "default" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedAWeberRecords', 'runtimeThreshold' => '5m'],
                ['name' => 'saveOpenAWeberRecords' ,  'runtimeThreshold' => '5m']
            ]
        ]
    ] ,
    "Publicators" => [
        "pipes" => [
            "actions" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '5m']
            ] ,
            "delivers" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getDeliverableCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '5m']
            ] ,
            "rerun" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getRerunCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'saveRecords', 'runtimeThreshold' => '5m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '5m']
            ]

        ]
    ],
    "Bronto" => [
        "pipes" => [
            "actions" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '5m'],
                ['name' => 'getRawCampaigns' , 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedRecords' , 'runtimeThreshold' => '5m']
            ] ,
            "delivered" => [
                ['name' => 'getSplitDeliverableCampaigns', 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedCampaignRecords' , 'runtimeThreshold' => '5m']
            ],
            "rerun" => [
                ['name' => 'getBrontoRerunCampaigns', 'runtimeThreshold' => '5m'],
                ['name' => 'splitTypes', 'runtimeThreshold' => '5m'],
                ['name' => 'savePaginatedCampaignRecords', 'runtimeThreshold' => '5m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '5m']
            ]
        ]
    ] ,
];
