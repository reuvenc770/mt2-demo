#!/usr/bin/perl -w
#use lib ("/tmp/repos/modules");
use lib ("/usr/local/share/modules");
use warnings;
use CGI;
use CGI::Carp qw(fatalsToBrowser); 
use Data::Dumper;
use Application;
use LWP::UserAgent;

my $ua  = new LWP::UserAgent;
my $APP = new Application();
my $cg  = new CGI() ;

print $cg->header() ;

my $check     = $cg->param('check');
my $action    = $cg->param('action');
my $client_id = $cg->param('client_id');
my $user      = 'admin';
my $pass      = 'spsumtin';
my $url       = 'http://209.120.227.3:83/cgi-bin/as_gui_wrapper.cgi';
my $lwp_url   = '';

#trap signal and do nothing with it
$SIG{'INT'} = '';

my $table = "<html>
<head>
	<title>Run Auto Scheduler for certain clients</title>
</head>
<body>\n";

#run script in debug and show output
if (defined $check){

	$req = new HTTP::Request (GET => "$url?check=1&client_id=$client_id");
	$req->authorization_basic($user, $pass);

	$table .= $ua->request($req)->as_string;
	
	$table .= "<center><b><a href='auto_sched_gui.cgi?action=1&client_id=$client_id'>Perform update</a></b></center><br />\n";
	
} #end if

#actually perform update
if (defined $action){
	
	$req = new HTTP::Request (GET => "$url?action=1&client_id=$client_id");
	$req->authorization_basic($user, $pass);

	$table .= "<center><b>Auto Scheduler updated for client id: $client_id.</b></center><br />\n";
	
} #end if

#get clients and ids
my $client_query = qq(SELECT user_id, company FROM user WHERE status='A' AND user_id NOT IN ('109') ORDER BY company);
$client_results  = $APP->getResults($client_query);

#close DB connection
$APP->getDB()->disconnect;

#make select menu of results
my $menu = $APP->makeSelect('client_id', $client_results);

$table .= "
<center>
<form method='post' action='auto_sched_gui.cgi'>
<input type='hidden' name='check' value='1'>
$menu
<br />
<br />
<input type='submit' name='Submit' value='Update'>
</form>
</center>
</body>
</html>";

print $table;
