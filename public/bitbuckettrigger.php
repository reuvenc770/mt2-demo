<?php
    // Do a git checkout to the web root
    $test = null;
    exec('~/.composer/vendor/bin/envoy run deploy --branch=master',$test);
    file_put_contents('storage/logs/deploy.log', date('m/d/Y h:i:s a') . " Deployed branch: " .  "master" . "\n", FILE_APPEND);

print_r($test);
