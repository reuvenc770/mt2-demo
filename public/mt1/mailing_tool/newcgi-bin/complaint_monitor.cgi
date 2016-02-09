#!/usr/bin/perl
#===============================================================================
# Purpose: Display of the ComplaintSetup Table 
# Name   : complaint_monitor.cgi 
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
my ($cid,$pid,$usa_id,$template_id,$ctime,$drops);
my $clientgroup;
my $profile_name;
my $usa_name;
my $template_name;
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
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Complaint Monitor Setup</title>
<!-- <link rel="stylesheet" type="text/css" href= "/stylesheet.css" /> -->
</head>
<body>
<center><h2>Complaint Monitor Setup</h2>
<br>
<br>
<table width=100% class="tbl top" border="1" cellspacing="3" id="table6">
<tr><th>Day</th><th>Client Group</th><th>Profile</th><th>Advertiser</th><th>Template</th><th>Time</th><th>Drops</th><th></th></tr>
end_of_html
my $i=1;
while ($i <= 7)
{
	print "<tr><td><a href=\"complaint_monitor_edit.cgi?cday=$i\">$DAY[$i]</a></td>";
	$sql = "select client_group_id,profile_id,usa_id,template_id,send_time,num_drops from ComplaintSetup where cday=?"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute($i);
	if (($cid,$pid,$usa_id,$template_id,$ctime,$drops)=$sth->fetchrow_array())
	{
		$sql="select group_name from ClientGroup where client_group_id=?";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($cid);
		($clientgroup)=$sth1->fetchrow_array();
		$sth1->finish();

		$sql="select profile_name from UniqueProfile where profile_id=?";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($pid);
		($profile_name)=$sth1->fetchrow_array();
		$sth1->finish();

		$sql="select name from UniqueScheduleAdvertiser where usa_id=?";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($usa_id);
		($usa_name)=$sth1->fetchrow_array();
		$sth1->finish();

		$sql="select template_name from brand_template where template_id=?";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($template_id);
		($template_name)=$sth1->fetchrow_array();
		$sth1->finish();
		print "<td>$clientgroup</td><td>$profile_name</td><td>$usa_name</td><td>$template_name</td><td>$ctime</td><td>$drops</td><td><a href=\"/cgi-bin/complaint_monitor_del.cgi?cday=$i\">Delete</a></td>";
	}
	$sth->finish();
	print "</tr>\n";
	$i++;
}
print<<"end_of_html";
	</table>

<p align="center">
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img src="/images/cancel.gif" border="0"></a><img height="1" src="/images/spacer.gif" width="40" border="0"></a>
						<input type="image" src="/images/save.gif" border="0" name="I1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="/cgi-bin/mainmenu.cgi" target="_top">
						<img border="0" src="/images/home_blkline.gif" width="76" height="23"></a></p>
</body>

</html>
end_of_html
