#!/usr/bin/perl
#===============================================================================
# Name   : replace_advertiser.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

# include Perl Modules
use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $query = CGI->new;
my ($sth, $sql, $dbh, $errmsg ) ;
my $sth1;
my $aname;
my $new_aname;
my $taid;
my $exclude_days;
my $cid;
my $sdate;
my $rows;

#----------- check for login ------------------
my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}

my $aid= $query->param('aid');
my $new_aid= $query->param('new_aid');
my @CLIENT= $query->param('client_id');
my $ctype=$query->param('ctype');
if ($ctype eq "")
{
	$ctype="ALL";
}
my $ind=0;
#
#------ connect to the util database ------------------
my ($dbhq,$dbhu)=$util->get_dbh();
$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($aname) = $sth1->fetchrow_array();
$sth1->finish;
$sql = "select advertiser_name,exclude_days from advertiser_info where advertiser_id=$new_aid";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($new_aname,$exclude_days) = $sth1->fetchrow_array();
$sth1->finish;
#
#	Change all Triggers, Daily Deals, and Scheduled Deals
#
if (($ctype eq "ALL") or ($ctype eq "TRIGGER"))
{
	$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='T' and deleted_date is null";
	$rows = $dbhu->do($sql);
	$sql = "update creative set advertiser_id=$new_aid where advertiser_id=$aid and trigger_flag='Y'";
	$rows = $dbhu->do($sql);
}

open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "Advertiser replaced <info\@zetainteractive.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: group.scheduling\@zetainteractive.com\n";
print MAIL "Subject: Advertiser Replaced - $aname\n";
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";
print MAIL "X-Priority: 1\n";
print MAIL "X-MSMail-Priority: High\n";
print MAIL "Advertiser $aname($aid) replaced with $new_aname($new_aid)\n\n";
while ($ind <= $#CLIENT)
{
	if ($CLIENT[$ind] == 0)
	{
		print MAIL "All Clients\n";
		if (($ctype eq "ALL") or ($ctype eq "DAILY"))
		{
			$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='W' and deleted_date is null";
			$rows = $dbhu->do($sql);
		}
		if ($ctype eq "ALL") 
		{
			$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='C' and deleted_date is null and scheduled_datetime > now()"; 
			$rows = $dbhu->do($sql);
			$_=$exclude_days;
			if (/Y/)
			{
				$sql="select campaign_id,scheduled_date from campaign where scheduled_date >= curdate() and deleted_date is null and advertiser_id=$new_aid and status='C' and substr('$exclude_days',dayofweek(date_add(scheduled_date,interval 6 day)),1)='Y'";
				my $sth=$dbhu->prepare($sql);
				$sth->execute();
				while (($cid,$sdate)=$sth->fetchrow_array())
				{
					$sql="update camp_schedule_info set status='D' where campaign_id=$cid and schedule_date='$sdate'";
					$rows = $dbhu->do($sql);
					$sql="update campaign set deleted_date=now() where campaign_id=$cid and schedule_date='$sdate'";
					$rows = $dbhu->do($sql);
				}
				$sth->finish();	
			}
		}
	}
	else
	{
		print MAIL "Client: $CLIENT[$ind]\n";
		if (($ctype eq "ALL") or ($ctype eq "DAILY"))
		{
			$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='W' and deleted_date is null and campaign_id in (select campaign_id from daily_deals where client_id=$CLIENT[$ind])";
			$rows = $dbhu->do($sql);
		}
		if ($ctype eq "ALL") 
		{
			$sql = "update campaign set advertiser_id=$new_aid,campaign_name='$new_aname' where advertiser_id=$aid and status='C' and deleted_date is null and scheduled_datetime > now() and user_id=$CLIENT[$ind]";
			$rows = $dbhu->do($sql);
			$_=$exclude_days;
			if (/Y/)
			{
				$sql="select campaign_id,scheduled_date from campaign where scheduled_date >= curdate() and deleted_date is null and advertiser_id=$new_aid and status='C' and substr('$exclude_days',dayofweek(date_add(scheduled_date,interval 6 day)),1)='Y' and profile_id in (select profile_id from list_profile where client_id=$CLIENT[$ind])";
				my $sth=$dbhu->prepare($sql);
				$sth->execute();
				while (($cid,$sdate)=$sth->fetchrow_array())
				{
					$sql="update camp_schedule_info set status='D' where campaign_id=$cid and schedule_date='$sdate' and client_id=$CLIENT[$ind]";
					$rows = $dbhu->do($sql);
					$sql="update campaign set deleted_date=now() where campaign_id=$cid and schedule_date='$sdate'";
					$rows = $dbhu->do($sql);
				}
				$sth->finish();	
			}
		}
	}
	$ind++;
}
close MAIL;
$util->clean_up();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
</head>
<body>
<center>
<h3>Replace Advertiser - Confirm</h3>
<br>
end_of_html
if ($ctype eq "ALL")
{
	print "Advertiser <b>$aname</b> replaced with <b>$new_aname</b> in all Trigger and Daily and Scheduled Campaigns for specified clients\n";
}
elsif ($ctype eq "DAILY")
{
	print "Advertiser <b>$aname</b> replaced with <b>$new_aname</b> in all Daily Campaigns for specified clients\n";
}
elsif ($ctype eq "TRIGGER")
{
	print "Advertiser <b>$aname</b> replaced with <b>$new_aname</b> in all Trigger Campaigns for specified clients\n";
}
print<<"end_of_html";
<br>
<a href="/cgi-bin/advertiser_list.cgi"><img src="/images/advertisers.gif" border=0></a>&nbsp;&nbsp;&nbsp;<a href="/cgi-bin/mainmenu.cgi"><img src="/images/home.gif" border=0></a>
</body>
</html>
end_of_html
exit(0);

