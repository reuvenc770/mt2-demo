<?php
$payload = json_decode($_POST['payload']);

if (empty($payload->commits)){
    // When merging and pushing to bitbucket, the commits array will be empty.
    // In this case there is no way to know what branch was pushed to, so we will do an update.
    $update = true;
} else {
    foreach ($payload->commits as $commit) {
        $branch = $commit->branch;
        if ($branch === 'master' || isset($commit->branches) && in_array('master', $commit->branches)) {
            $update =	true;
            break;
        }
    }
}

if ($update) {
    // Do a git checkout to the web root
    exec('~/.composer/vendor/bin/envoy run deploy --branch=master');
    file_put_contents('storage/logs/deploy.log', date('m/d/Y h:i:s a') . " Deployed branch: " .  $branch . "\n", FILE_APPEND);
}
