<?php

return [
    "BlueHornet" => [
        "pipes" => [
            "actions" => [
                'jobSetup' ,
                'getCampaigns' ,
                'startTicket' ,
                'checkTicketStatus' ,
                'downloadTicketFile' ,
                'getTypeList' ,
                'synchronousSaveTypeRecords' ,
                'cleanUp'
            ] ,
            "delivered" => [
                'jobSetup' ,
                'getDeliverableCampaigns' ,
                'startTicket' ,
                'checkTicketStatus' ,
                'downloadTicketFile' ,
                'getTypeList' ,
                'synchronousSaveTypeRecords' ,
                'cleanUp'
            ],
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
            "delivered" => [
                'getCampaigns' ,
                'splitTypes' ,
                'startTicket' ,
                'saveRecords'
            ] ,
            "actions" => [
                'getCampaigns' ,
                'splitTypes' ,
                'startTicket' ,
                'saveRecords'
            ] ,
            "rerun" => [
                'getRerunCampaigns' ,
                'startTicket' ,
                'splitTypes' ,
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
            "actions" => [
                'jobSetup' ,
                'getCampaigns' ,
                'getTypeList' ,
                'splitTypes' ,
                'saveRecords'
            ] ,
            "delivers" => [
                'jobSetup' ,
                'getDeliverableCampaigns' ,
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
                'getRawCampaigns' ,
                'splitTypes' ,
                'savePaginatedRecords'
            ] ,
            "delivered" => [
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
