#!/usr/bin/perl
#===============================================================================
# Name   : unique_server_rpt.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sth;
my $dbh;
my $id;
my $colo;
my $server;
my $threads;
my $hthreads;
my $cstatus;
my $cnt;
my $THREAD;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Unique Server Report</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
	font-size: 1.0em;
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

.centered {
	text-align: center;
  }

#container {
	width: 100%;
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

div.submit {
	text-align: center;
	padding: 1em 0;
  }

input.submit {
	margin-top: .25em;
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

<h1>Server Unique Thread Report</h1>
	<h2><a href="unique_list.cgi" target=_top>view/edit/deploy uniques campaigns</a> | <a href="/newcgi-bin/unique_deploy_list.cgi">deployed unique campaigns</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=1">GSM/DD/CHA deploys</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=2">Hotmail deploys</a> | <a href="/newcgi-bin/unique_deploy_list.cgi?gsm=3">Chunking</a> | <a href="/newcgi-bin/mainmenu.cgi" target=_top>go home</a></h2>
<center>
<table border=1>
<tr><th>Server</th><th>Colo</th><th>Threads</th><th>Hotmail<br>Threads</th><th>Unused<br>Threads</th><th>START</th><th>INJECTING</th><th>PAUSED</th><th>PENDING</th><th>PRE-PULLING</th><th>SLEEPING</th></tr>
end_of_html
my $unused_cnt;
$sql="select id,server,threadsDeploy,threadsHotmailDeploy,colo from server_config where threadsDeploy > 0 or threadsHotmailDeploy > 0 order by server"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($id,$server,$threads,$hthreads,$colo)=$sth->fetchrow_array())
{
	$unused_cnt=$threads + $hthreads;
	$THREAD->{"START"}=0;
	$THREAD->{"PENDING"}=0;
	$THREAD->{"PAUSED"}=0;
	$THREAD->{"PRE-PULLING"}=0;
	$THREAD->{"SLEEPING"}=0;
	$THREAD->{"INJECTING"}=0;
	$sql="select status,count(*) from unique_campaign where server_id=? and status not in ('PULLED','CANCELLED') and send_date >= date_sub(curdate(),interval 2 day) and send_date <= curdate() group by 1";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($id);
	while (($cstatus,$cnt)=$sth1->fetchrow_array())
	{
		$unused_cnt=$unused_cnt - $cnt;
		$THREAD->{"$cstatus"}=$cnt;
	}
	$sth1->finish();	
	print "<tr><td>$server</td><td>$colo</td><td>$threads</td><td>$hthreads</td><td>$unused_cnt</td>";
	print "<td>$THREAD->{\"START\"}</td>";
	print "<td>$THREAD->{\"INJECTING\"}</td>";
	print "<td>$THREAD->{\"PAUSED\"}</td>";
	print "<td>$THREAD->{\"PENDING\"}</td>";
	print "<td>$THREAD->{\"PRE-PULLING\"}</td>";
	print "<td>$THREAD->{\"SLEEPING\"}</td></tr>";
}
$sth->finish();
print<<"end_of_html";
		</table>

	</div>
</body>
</html>
end_of_html
