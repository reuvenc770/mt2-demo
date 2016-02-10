#!/usr/bin/perl
#===============================================================================
# Name   : sm2_send_alllist.cgi - lists all test_campaigns of campaign_type='TEST' 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;
use Lib::Database::Perl::Interface::Server;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;

my $everyday_flag=$query->param('everyday');
if ($everyday_flag eq "")
{
	$everyday_flag=0;
}
#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $userDataRestrictionWhereClause = '';

$util->getUserData({'userID' => $user_id});

my $isExternalUser = $util->getUserData()->{'isExternalUser'};

if($util->getUserData()->{'isExternalUser'} == 1)
{
	$userDataRestrictionWhereClause = qq|
        userID = $user_id AND
    |;
}

#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<title>View Send All</title>

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
h4 {
	font-weight: normal;
	font-size: .8em;
	padding-top: 1em;
	margin: 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

#container {
	width: 80%;
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
tr.inactive {
	color: #aaa;
}

td.label {
	width: 40%;
	text-align: right;
  }

td.field {
	width: 60%;
  }
</style>
</head>

<body>
<center>
<div id="container">
end_of_html
print "<h1>List of Send All Tests</h1>\n";
print "<h2><a href=\"/sm2_build_test.html\">build a test campaign</a> | <a href=\"sm2_list.cgi?type=D\">see all deployed campaigns</a> | <a href=\"sm2_list.cgi?type=F\">see all freestyle campaigns</a> | <a href=\"/sm2_send_all.html\">Send All Test</a> | <a href=\"/sm2_send_all_free.html\">Send All FreeStype Test</a> | <a href=\"mainmenu.cgi\">go home</a></h2>\n";
print<<"end_of_html";
	<div id="form">
		<table style="border: 1px solid #999; ">
		  <tr>
end_of_html
print "<td width=\"10%\"><strong>Date Sent</strong></td>\n";
print "<td width=\"10%\"><strong>Time Sent</strong></td>\n";
print "<td width=\"10%\"><strong>Test ID</strong></td>\n";
print "<td width=\"10%\"><strong>Server</strong></td>\n";
print "<td width=\"10%\"><strong>Email</strong></td>\n";
print<<"end_of_html";
			<td width="50%"><strong>Campaign Name</strong></td>
			<td width="10%"></td>
		  </tr>
end_of_html
if ($everyday_flag)
{
	$sql="select test_id,date_format(send_date,'%m/%d/%Y'),campaign_name,server_id,status,send_date,curdate(),send_time,email_addr from test_campaign where $userDataRestrictionWhereClause send_date = curdate() and mainTestID=0 and campaign_type='SENDALL' and schedule_everyday='Y' order by send_date";
}
else
{
	$sql="select test_id,date_format(send_date,'%m/%d/%Y'),campaign_name,server_id,status,send_date,curdate(),send_time,email_addr from test_campaign where $userDataRestrictionWhereClause (send_date >= date_sub(curdate(),interval 1 day) or (send_date is null)) and mainTestID=0 and campaign_type in ('SENDALL','SENDALL-FREESTYLE') order by send_date";
}
$sth=$dbhq->prepare($sql);
$sth->execute();
my $test_id;
my $cdate;
my $cname;
my $cid;
my $cstatus;
my $sid;
my $sname;
my $sdate;
my $curdate;
my $tcnt;
my $stime;
my $em;
#$ENV{'DATABASE_HOST'}="slavedb.i.routename.com";
#$ENV{'DATABASE_USER'}="db_readuser";
#$ENV{'DATABASE_PASSWORD'}="Tr33Wat3r";
$ENV{'DATABASE_HOST'}="masterdb.i.routename.com";
$ENV{'DATABASE_USER'}="db_user";
$ENV{'DATABASE_PASSWORD'}="sp1r3V";
$ENV{'DATABASE'}="new_mail";
my $serverInterface     = Lib::Database::Perl::Interface::Server->new();
while (($test_id,$cdate,$cname,$sid,$cstatus,$sdate,$curdate,$stime,$em)=$sth->fetchrow_array())
{
	if ($sid > 0)
	{
        my $params={};
        $params->{serverID}=$sid;
        my ($errors,$results)=$serverInterface->getMtaServers($params);
		$sname=$results->[0]->{'hostname'};
	}
	else
	{
		$sname="ALL";
	}
	my @T=split(',',$em);
	$em=$T[0];
	$sql="select count(*) from test_campaign where $userDataRestrictionWhereClause mainTestID=? and status not in ('PULLED','CANCELLED') and campaign_type in ('SENDALL','SENDALL-FREESTYLE')";
	my $sth1=$dbhq->prepare($sql);
	$sth1->execute($test_id);
	($tcnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($tcnt > 0)
	{
		$cstatus="PENDING";
	}
print<<"end_of_html";
		  	<td width="10%">$cdate</td>
		  	<td width="10%">$stime</td>
			<td>$test_id</td>
		  	<td width="10%">$sname</td>
		  	<td width="10%">$em</td>
			<td>$cname</td>
			<td>
end_of_html
	if (($cstatus ne "PULLED") and ($cstatus ne "CANCELLED")) 
	{
		print "<a href=\"sm2_send_all_cancel.cgi?tid=$test_id\">Cancel</a>";
	}
	if ($everyday_flag)	
	{
		print "&nbsp;&nbsp;<a href=\"sm2_send_all_delete.cgi?tid=$test_id\">Delete</a>";
	}
	print "</td></tr>";
	$sql="select test_id from test_campaign where $userDataRestrictionWhereClause mainTestID=?";
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($test_id);
	my $tstr="";
	my $tid;
	while (($tid)=$sth1->fetchrow_array())
	{
		$tstr=$tstr.$tid." ";
	}
	$sth1->finish();
	if ($tstr ne "")
	{
		print "<tr><td></td><td colspan=2>Sub Test Ids: </td><td colspan=3>$tstr</td></tr>\n";
	}
}
$sth->finish();
print<<"end_of_html";
		</table>

	</div>

</div>
</center>
</body>
</html>
end_of_html
