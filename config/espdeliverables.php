<?php

return [
    "BlueHornet" => [
        "pipes" => [
            "actions" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getCampaigns', 'runtimeThreshold' => '1m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '1m'],
                ['name' => 'checkTicketStatus' , 'runtimeThreshold' => '1m'],
                ['name' => 'downloadTicketFile' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'synchronousSaveTypeRecords' , 'runtimeThreshold' => '1m'],
                ['name' => 'cleanUp' , 'runtimeThreshold' => '1m']
            ] ,
            "delivered" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getDeliverableCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '1m'],
                ['name' => 'checkTicketStatus' , 'runtimeThreshold' => '1m'],
                ['name' => 'downloadTicketFile' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'synchronousSaveTypeRecords' , 'runtimeThreshold' => '1m'],
                ['name' => 'cleanUp' , 'runtimeThreshold' => '1m']
            ],
            "rerun" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getRerunCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '1m'],
                ['name' => 'checkTicketStatus' , 'runtimeThreshold' => '1m'],
                ['name' => 'downloadTicketFile' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'synchronousSaveTypeRecords' , 'runtimeThreshold' => '1m'],
                ['name' => 'removeDeploys', 'runtimeThreshold' => '1m'],
                ['name' => 'cleanUp',  'runtimeThreshold' => '1m']
            ]
        ]
    ] ,
    "Campaigner" => [
        "pipes" => [
            "delivered" => [
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords',  'runtimeThreshold' => '1m']
            ] ,
            "actions" => [
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '1m']
            ] ,
            "rerun" => [
                ['name' => 'getRerunCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'startTicket' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords', 'runtimeThreshold' => '1m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '1m']
            ]
        ]
    ] ,
    "Maro" => [
        "pipes" => [
            "default" => [
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedRecords' ,  'runtimeThreshold' => '1m']
            ] ,
            "delivered" => [
                ['name' => 'getDeliverableCampaigns', 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedCampaignRecords' ,  'runtimeThreshold' => '1m']
            ],
            "rerun" => [
                ['name' => 'getRerunCampaigns', 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes', 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedCampaignRecords', 'runtimeThreshold' => '1m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '1m']
            ]
        ]
    ] ,
    "EmailDirect" => [
        "pipes" => [
            "default" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '1m']
            ]
        ]
    ],
    "Ymlp" => [
        "pipes" => [
            "default" => [
                ['name' => 'getCampaigns', 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList', 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '1m']
            ]
        ]
    ] ,
    "AWeber" => [
        "pipes" => [
            "default" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedAWeberRecords', 'runtimeThreshold' => '1m'],
                ['name' => 'saveOpenAWeberRecords' ,  'runtimeThreshold' => '1m']
            ]
        ]
    ] ,
    "Publicators" => [
        "pipes" => [
            "actions" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '1m']
            ] ,
            "delivers" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getDeliverableCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords' ,  'runtimeThreshold' => '1m']
            ] ,
            "rerun" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getRerunCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'getTypeList' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'saveRecords', 'runtimeThreshold' => '1m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '1m']
            ]

        ]
    ],
    "Bronto" => [
        "pipes" => [
            "actions" => [
                ['name' => 'jobSetup' , 'runtimeThreshold' => '1m'],
                ['name' => 'getRawCampaigns' , 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes' , 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedRecords' , 'runtimeThreshold' => '1m']
            ] ,
            "delivered" => [
                ['name' => 'getSplitDeliverableCampaigns', 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedCampaignRecords' , 'runtimeThreshold' => '1m']
            ],
            "rerun" => [
                ['name' => 'getBrontoRerunCampaigns', 'runtimeThreshold' => '1m'],
                ['name' => 'splitTypes', 'runtimeThreshold' => '1m'],
                ['name' => 'savePaginatedCampaignRecords', 'runtimeThreshold' => '1m'],
                ['name' => 'removeDeploys' ,  'runtimeThreshold' => '1m']
            ]
        ]
    ] ,
];
