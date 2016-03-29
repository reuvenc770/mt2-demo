<?php

return [
    "BlueHornet" => [
        "pipes" => [
            "default" => [
                [ 'name' => 'jobSetup' ] ,
                [
                    'name' => 'splitTypes' ,
                    'arguments' => [
                        [ 'open' , 'click' , 'optout' ]
                    ]
                ] , 
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'startTicket' ] ,
                [ 'name' => 'checkTicketStatus' ] ,
                [ 'name' => 'downloadTicketFile' ] ,
                [ 'name' => 'saveRecords' ]
            ] ,
            "delivered" => [
                [ 'name' => 'jobSetup' ] ,
                [
                    'name' => 'splitTypes' ,
                    'arguments' => [
                        [ 'deliverable' ]
                    ]
                ] , 
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'startTicket' ] ,
                [ 'name' => 'checkTicketStatus' ] ,
                [ 'name' => 'downloadTicketFile' ] ,
                [ 'name' => 'saveRecords' ]
            ]
        ]
    ] ,
    "Campaigner" => [
        "pipes" => [
            "default" => [
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'startTicket' ] ,
                [ 'name' => 'saveRecords' ]
            ]
        ]
    ] ,
    "Maro" => [
        "pipes" => [
            "default" => [
                [ 'name' => 'splitTypes' ] ,
                [ 'name' => 'savePaginatedRecords' ]
            ] , 
            "delivered" => [
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'saveRecords' ]
            ]
        ]
    ] ,
    "EmailDirect" => [
        "pipes" => [
            "default" => [
                [
                    'name' => 'splitTypes' ,
                    'arguments' => [
                        [ 'opens' , 'clicks', 'unsubscribes', 'complaints' ]
                    ]
                ] ,
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'saveRecords' ]
            ] ,
            "delivered" => [
                [
                    'name' => 'splitTypes' ,
                    'arguments' => [
                        [ 'deliveries' ]
                    ]
                ] ,
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'saveRecords' ]
            ]
        ]
    ],
    "Ymlp" => [
        "pipes" => [
            "default" => [
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'splitTypes' ] ,
                [ 'name' => 'saveRecords' ]
            ]
        ]
    ] ,
    "AWeber" => [
        "pipes" => [
            "default" => [
                [ 'name' => 'getCampaigns' ] ,
                [ 'name' => 'splitTypes' ] ,
                [ 'name' => 'saveRecords' ]
            ]
        ]
    ]
];
