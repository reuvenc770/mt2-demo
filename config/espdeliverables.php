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
            ]
        ]
    ] ,
    "Campaigner" => [
        "pipes" => [
            "default" => [
                'getCampaigns' ,
                'startTicket' ,
                'saveRecords'
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
                'getCampaigns' ,
                'saveRecords'
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
                'splitTypes' ,
                'saveRecords'
            ]
        ]
    ] ,
    "AWeber" => [
        "pipes" => [
            "default" => [
                'getCampaigns' ,
                'splitTypes' ,
                'saveRecords'
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
            ]
        ]
    ]
];
