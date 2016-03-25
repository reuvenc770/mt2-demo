<?php

return [
    "BlueHornet" => [
        "filters" => [
            'jobSetup' ,
            'splitTypes' ,
            'getCampaigns' ,
            'startTicket' ,
            'checkTicketStatus' ,
            'downloadTicketFile' ,
            'saveRecords'
        ]
    ] ,
    "Campaigner" => [
        "filters" => [
            'getCampaigns' ,
            'startTicket' ,
            'saveRecords'
        ]
    ] ,
    "Maro" => [
        "filters" => [
            'splitTypes' ,
            'savePaginatedRecords'
        ]
    ] ,
    "EmailDirect" => [
        "filters" => [
            'getCampaigns' ,
            'splitTypes' ,
            'saveRecords'
        ]
    ],
    "Ymlp" => [
        "filters" => [
            'getCampaigns',
            'splitTypes' ,
            'saveRecords'
        ]
    ] ,
    "AWeber" => [
        "filters" => [
            'getCampaigns' ,
            'splitTypes' ,
            'saveRecords'
        ]
    ]
];
