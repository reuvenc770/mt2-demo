#!/usr/bin/perl
#===============================================================================
# Purpose: Display of the ComplaintSetup Table 
# Name   : complaint_monitor_edit.cgi 
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
my $name;
my $sql;
my $sth;
my $sth1;
my $dbh;
my $cday=$query->param('cday');
my ($cid,$pid,$usa_id,$template_id,$ctime,$drops);
my $clientgroup;
my $profile_name;
my $usa_name;
my $template_name;
my $tcid;
my @DAY=("","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
my ($dbhq,$dbhu)=$util->get_dbh();
#
#
$sql = "select client_group_id,profile_id,usa_id,template_id,send_time,num_drops from ComplaintSetup where cday=?"; 
$sth = $dbhq->prepare($sql);
$sth->execute($cday);
($cid,$pid,$usa_id,$template_id,$ctime,$drops)=$sth->fetchrow_array();
$sth->finish();

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Complaint Monitor Edit</title>
<!-- <link rel="stylesheet" type="text/css" href= "/stylesheet.css" /> -->
</head>
<body>
<center><h2>Complaint Monitor Edit - $DAY[$cday]</h2>
<br>
<br>
<form method=post name=mainform action="/cgi-bin/complaint_monitor_save.cgi">
<input type=hidden name=cday value=$cday>
<table width=80% class="tbl top" border="1" cellspacing="3" id="table6">
<tr><td>Client Group:</td><td><select name=client_group_id>
end_of_html
$sql="select client_group_id,group_name from ClientGroup order by group_name";
$sth1=$dbhu->prepare($sql);
$sth1->execute();
while (($tcid,$clientgroup)=$sth1->fetchrow_array())
{
	if ($tcid == $cid)
	{
		print "<option selected value=$tcid>$clientgroup</option>\n";
	}
	else
	{
		print "<option value=$tcid>$clientgroup</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
</select></td></tr>
<tr><td>Profile:</td><td><select name=pid>
end_of_html
$sql="select profile_id,profile_name from UniqueProfile where status='A' order by profile_name";
$sth1=$dbhu->prepare($sql);
$sth1->execute();
while (($tcid,$profile_name)=$sth1->fetchrow_array())
{
	if ($tcid == $pid)
	{
		print "<option selected value=$tcid>$profile_name</option>\n";
	}
	else
	{
		print "<option value=$tcid>$profile_name</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
</select></td></tr>
<tr><td>Advertiser</td><td><select name=usa_id>
end_of_html
$sql="select usa_id,name from UniqueScheduleAdvertiser order by name";
$sth1=$dbhu->prepare($sql);
$sth1->execute();
while (($tcid,$usa_name)=$sth1->fetchrow_array())
{
	if ($tcid == $usa_id)
	{
		print "<option selected value=$tcid>$usa_name</option>\n";
	}
	else
	{
		print "<option value=$tcid>$usa_name</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
</select></td></tr>
<tr><td>Template:</td><td><select name=template_id>
end_of_html
$sql="select template_id,template_name from brand_template where status='A' order by template_name";
$sth1=$dbhu->prepare($sql);
$sth1->execute();
while (($tcid,$template_name)=$sth1->fetchrow_array())
{
	if ($tcid == $template_id)
	{
		print "<option selected value=$tcid>$template_name</option>\n";
	}
	else
	{
		print "<option value=$tcid>$template_name</option>\n";
	}
}
$sth1->finish();
print<<"end_of_html";
</select></td></tr>
<tr><td>Time:</td><td><select name=ctime>
end_of_html
my $i=0;
my $thour;
my $ntime;
my $nval;
while ($i <= 23)
{
	if (length($i) == 1)
	{
		$thour="0".$i;
	}
	else
	{
		$thour=$i;
	}
	$ntime=$thour.":00:00";
	if ($i < 12)
	{
		$nval=$i.":00 AM";
	}
	else
	{
		$nval=$i-12;
		if ($nval == 0)
		{
			$nval="12";
		}
		$nval.=":00 PM";
	}
	if ($ntime eq $ctime)
	{
		print "<option selected value=$ntime>$nval</option>\n";
	}
	else
	{
		print "<option value=$ntime>$nval</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
<tr><td>Drops:</td><td><select name=drops>
end_of_html
my $i=1;
while ($i <= 3)
{
	if ($i == $drops)
	{
		print "<option selected value=$i>$i</option>\n";
	}
	else
	{
		print "<option value=$i>$i</option>\n";
	}
	$i++;
}
print<<"end_of_html";
</select></td></tr>
	</table>

<p align="center">
						<a href="/cgi-bin/complaint_monitor.cgi" target="_top">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"></a>
						<input type="image" src="/images/save.gif" border="0" name="I1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</form>
</body>

</html>
end_of_html
