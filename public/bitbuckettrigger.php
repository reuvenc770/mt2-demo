<?php
    // Do a git checkout to the web root
    exec('~/.composer/vendor/bin/envoy run deploy --branch=master');
    file_put_contents('storage/logs/deploy.log', date('m/d/Y h:i:s a') . " Deployed branch: " .  "master" . "\n", FILE_APPEND);

