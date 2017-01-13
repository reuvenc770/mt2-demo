<?php

return [

    'expiration' => [
        "set" => ["recent_import" => true],
        "expire" => ["recent_import" => false],
    ],
    "activity" => [
        "set" => ["has_action" => true, "action_expired" => false],
        "expire" => ["has_action" => false, "action_expired" => true]
    ],

];
