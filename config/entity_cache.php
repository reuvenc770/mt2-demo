<?php

return [
    \App\Repositories\FeedRepo::class => [
        'models' => [ \App\Models\Feed::class ] ,
        'array' => 'getAllFeedsArray' ,
        'partyMap' => 'getPartyFeedMap' ,
        'countryMap' => 'getCountryFeedMap' ,
    ] ,
    \App\Repositories\FeedGroupRepo::class => [
        'models' => [ App\Models\FeedGroup::class ] ,
        'array' => 'getAllFeedGroupsArray'
    ] ,
    \App\Repositories\ClientRepo::class => [
        'models' => [
            App\Models\Client::class ,
            App\Models\Feed::class
        ] ,
        'array' => 'getAllClientsArray' ,
        'feedMap' => 'getClientFeedMap'
    ]
];
