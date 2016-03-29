<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Library\AWeber\AWeberAPI;
class GenOauth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'GenOauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        # replace XXX with your real keys and secrets
        $consumerKey = env("AWEBER_KEY");
        $consumerSecret = env("AWEBER_SECRET");

# create new instance of AWeberAPI
        $application = new AWeberAPI($consumerKey, $consumerSecret);

# get a request token using oob as the callback URL
        list($requestToken, $tokenSecret) = $application->getRequestToken('oob');

# prompt user to go to authorization URL
        echo "Go to this url in your browser: {$application->getAuthorizeUrl()}\n";

# get the verifier code
        echo 'Type code here: ';
        $code = trim(fgets(STDIN));

# turn on debug mode for more information
        $application->adapter->debug = true;

# exchange request token + verifier code for an access token
        $application->user->requestToken = $requestToken;
        $application->user->tokenSecret = $tokenSecret;
        $application->user->verifier = $code;
        list($accessToken, $accessSecret) = $application->getAccessToken();

# show your access token
        $this->info("access token:: {$accessToken}");
        $this->info("access secret:: {$accessSecret}");
    }
}
