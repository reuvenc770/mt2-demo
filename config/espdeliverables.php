<?php

return [
    "BlueHornet" => [
        "pipes" => [
            "default" => [
                'jobSetup' ,
                'getCampaigns' ,
                'startTicket' ,
                'checkTicketStatus' ,
                'downloadTicketFile' ,
                'getTypeList' ,
                'synchronousSaveTypeRecords' ,
                'cleanUp'
            ] ,
            "rerun" => [
                'jobSetup' ,
                'getRerunCampaigns' ,
                'startTicket' ,
                'checkTicketStatus' ,
                'downloadTicketFile' ,
                'getTypeList' ,
                'synchronousSaveTypeRecords' ,
                'removeDeploys',
                'cleanUp'
            ]
        ]
    ] ,
    "Campaigner" => [
        "pipes" => [
            "default" => [
                'getCampaigns' ,
                'startTicket' ,
                'saveRecords'
            ] ,
            "rerun" => [
                'getRerunCampaigns' ,
                'startTicket' ,
                'saveRecords',
                'removeDeploys'
            ]
        ]
    ] ,
    "Maro" => [
        "pipes" => [
            "default" => [
                'splitTypes' ,
                'savePaginatedRecords'
            ] , 
            "delivered" => [
                'getDeliverableCampaigns',
                'savePaginatedCampaignRecords'
            ],
            "rerun" => [
                'getRerunCampaigns',
                'splitTypes',
                'savePaginatedCampaignRecords',
                'removeDeploys'
            ]
        ]
    ] ,
    "EmailDirect" => [
        "pipes" => [
            "default" => [
                'jobSetup' ,
                'getCampaigns' ,
                'getTypeList' ,
                'splitTypes' ,
                'saveRecords'
            ]
        ]
    ],
    "Ymlp" => [
        "pipes" => [
            "default" => [
                'getCampaigns',
                'getTypeList',
                'splitTypes' ,
                'saveRecords'
            ]
        ]
    ] ,
    "AWeber" => [
        "pipes" => [
            "default" => [
                'jobSetup' ,
                'getCampaigns' ,
                'splitTypes' ,
                'savePaginatedAWeberRecords',
                'saveOpenAWeberRecords'
            ]
        ]
    ] ,
    "Publicators" => [
        "pipes" => [
            "default" => [
                'jobSetup' ,
                'getCampaigns' ,
                'getTypeList' ,
                'splitTypes' ,
                'saveRecords'
            ] ,
            "rerun" => [
                'jobSetup' ,
                'getRerunCampaigns' ,
                'getTypeList' ,
                'splitTypes' ,
                'saveRecords',
                'removeDeploys'
            ]

        ]
    ],
    "Bronto" => [
        "pipes" => [
            "actions" => [
                'jobSetup' ,
                'splitTypes' ,
                'savePaginatedRecords'
            ] ,
            "delivered" => [
                'jobSetup' ,
                'getSplitDeliverableCampaigns',
                'savePaginatedCampaignRecords'
            ],
            "rerun" => [
                'getBrontoRerunCampaigns',
                'splitTypes',
                'savePaginatedCampaignRecords',
                'removeDeploys'
            ]
        ]
    ] ,
];
