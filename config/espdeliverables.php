<?php

return [
    "BlueHornet" => [
        "filters" => [
            'getTickets' ,
            'saveRecords'
        ]
    ] ,
    "Campaigner" => [
        "filters" => [
            'getTickets' ,
            'saveRecords'
        ]
    ] ,
    "Maro" => [
        "filters" => [
            'splitTypes' ,
            'saveRecords'
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
