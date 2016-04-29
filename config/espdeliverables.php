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
                'getRerunCampaigns'
            ] ,
            "test" -> [
                'startTicket' ,
                'checkTicketStatus' ,
                'downloadTicketFile' ,
                'getRerunTypeList' ,
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
            ] , 
            "rerun" => [
                'getRerunCampaigns' ,
                'startTicket' ,
                'getRerunTypeList' ,
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
            ] , 
            "rerun" => [
                'getRerunTypeList' ,
                'savePaginatedRecords'
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
            ] , 
            "rerun" => [
                'jobSetup' ,
                'getRerunCampaigns' ,
                'getRerunTypeList' ,
                'splitTypes' ,
                'saveRecords'
            ]
        ]
    ]
];
