<?php

return [
    "BlueHornet" => [
        "pipes" => [
            "default" => [
                'getTickets' ,
                'saveRecords'
            ]
        ]
    ] ,
    "Campaigner" => [
        "pipes" => [
            "default" => [
                'getTickets' ,
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
                'getCampaigns' ,
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
    ]
];
