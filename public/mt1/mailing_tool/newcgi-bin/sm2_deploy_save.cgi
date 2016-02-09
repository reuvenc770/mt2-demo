#!/usr/bin/perl

# *****************************************************************************************
# sm2_deploy_save.cgi
#
# this page updates the test_campaign to deploy a deal 
#
# History
# Jim Sobeck, 08/02/07, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $images = $util->get_images_url;
my $client_id;
my $brand_id;
my $cname;
my $aid;
my $creative_id;
my $camp_id;

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
open(LOG,">/tmp/k.k");
my $tid = $query->param('tid');
my $profile_id = $query->param('profile_id');
my $mta_id = $query->param('mta_id');
$sql="select client_id,brand_id,campaign_name,advertiser_id,creative_id from test_campaign where test_id=?";
$sth=$dbhu->prepare($sql);
print LOG "<$sql>\n";
$sth->execute($tid);
($client_id,$brand_id,$cname,$aid,$creative_id)=$sth->fetchrow_array();
$sth->finish();
#
# Add campaign record
#
$sql="insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,scheduled_date,scheduled_time,advertiser_id,profile_id,brand_id,campaign_type,id) values($client_id,'$cname','A',now(),now(),curdate(),'01:00:00',$aid,$profile_id,$brand_id,'STRONGMAIL','$tid')";
my $rows=$dbhu->do($sql);
print LOG "<$sql>\n";
#
# Get campaign id and add record to 3rdparty campaign table
#
$sql="select max(campaign_id) from campaign where campaign_name='$cname' and scheduled_date=curdate()"; 
$sth=$dbhu->prepare($sql);
print LOG "<$sql>\n";
$sth->execute();
($camp_id)=$sth->fetchrow_array();
$sth->finish();
#
$sql="insert into current_campaigns(campaign_id,scheduled_date,scheduled_time,campaign_type,priority) values($camp_id,curdate(),'01:00:00','STRONGMAIL',1)";
$rows=$dbhu->do($sql);
print LOG "<$sql>\n";
#
$sql="update test_campaign set campaign_type='DEPLOYED',status='START',profile_id=$profile_id,send_date=now(),campaign_id=$camp_id where test_id=$tid"; 
$rows=$dbhu->do($sql);
print LOG "<$sql>\n";
close(LOG);
#
# Display the confirmation page
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<title>Build a StrongMail Test Campaign</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: .9em;
	color: #4d4d4d;
  }

h1 {
	text-align: center;
	font-weight: normal;
	font-size: 1.5em;
  }

h2 {
	text-align: center;
	font-weight: normal;
	font-size: 1em;
  }

#container {
	width: 70%;
	padding-top: 5%;
	margin: 0 auto;
  }

#form {
	margin: 0 auto;
	width: 100%;
	padding: 1em;
	text-align: left;
  }

#form table {
	width: 100%;
	margin: 0 auto;
	margin-top: .5em;
	margin-bottom: .5em;
  }

#form td {
	padding: .25em;
  }

td.label {
	width: 40%;
	text-align: right;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	margin-top: 1em;
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }

</style>

</head>

<body>
<div id="container">

	<h1>Campaign $tid has been deployed.</h1>

	<h2><a href="sm2_build_test.cgi">build another </a> | <a href="sm2_list.cgi">view all tests</a> | <a href="sm2_list.cgi?type=D">view deployed campaigns</a> | <a href="mainmenu.cgi">go home</a>

</div>
</body>
</html>
end_of_html
$util->clean_up();
exit(0);
