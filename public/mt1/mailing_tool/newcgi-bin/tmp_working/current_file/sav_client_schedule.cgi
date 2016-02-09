#!/usr/bin/perl

# *****************************************************************************************
# sav_client_schedule.cgi
#
# this page saves the client schedule information 
#
# History
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
my $sid;
my $dbh;
my $list_id;
my $iopt;
my $sdate;
my $camp_id;
my $rows;
my $errmsg;
my $campaign_id;
my $id;
my $campaign_name;
my $k;
my $cname;
my $status;
my $aid;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $list_cnt;
my $camp_cnt;
my $aol_cnt;
my $daily_cnt;
my $rotating_cnt;
my $third_cnt;
my $tid;
my $i;
my $bid;
my $cstatus;
my $pid;
my $temp_pid;
my $am_pm;
my $hour;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $cid= $query->param('cid');

# Update the information in the tables
$sql = "delete from schedule_info where client_id=$cid";
$rows = $dbh->do($sql);

$sql = "select campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt from network_schedule where client_id=$cid"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($camp_cnt,$aol_cnt,$daily_cnt,$rotating_cnt,$third_cnt) = $sth->fetchrow_array();
$sth->finish();
$i=1;
while ($i <= $camp_cnt)
{
   	$tid = $query->param("tid_C_$i");
   	$pid = $query->param("pid_C_$i");
   	$bid = $query->param("bid_C_$i");
   	$cstatus = $query->param("stat_C_$i");
   	$am_pm= $query->param("am_pm_C_$i");
	if ($am_pm eq "PM")
	{
		$hour = $tid + 12;
	}
	else
	{
		$hour = $tid;
		if ($hour < 10)
		{
			$hour = "0" . $hour;
		}
	}
	my $thour = $hour . ":00:00";
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id,status) values($cid,$i,'C','$thour',$pid,$bid,'$cstatus')";
	$rows = $dbh->do($sql);
    if ($hour == 12)
    {
    	$hour = $hour - 12;
    }
    elsif ($hour == 24)
   	{
       	$hour = $hour - 12;
   	}
	#
	# Update all campaigns for this client and slot
	#
	$sql = "select campaign_id,schedule_date from camp_schedule_info where client_id=$cid and slot_id=$i and slot_type='C' and schedule_date >= curdate()";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($camp_id,$sdate) = $sth->fetchrow_array())
	{
    	$sdate = $sdate . " " . $hour . ":00";
		$sql="update campaign set profile_id=$pid,brand_id=$bid,scheduled_datetime='$sdate' where campaign_id=$camp_id and status='S' and deleted_date is null";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
	$i++;
}
$i=1;
while ($i <= $third_cnt)
{
   	$tid = $query->param("tid_3_$i");
   	$pid = $query->param("pid_3_$i");
   	$bid = $query->param("bid_3_$i");
   	$cstatus = $query->param("stat_3_$i");
   	$am_pm= $query->param("am_pm_3_$i");
	if ($am_pm eq "PM")
	{
		$hour = $tid + 12;
	}
	else
	{
		$hour = $tid;
		if ($hour < 10)
		{
			$hour = "0" . $hour;
		}
	}
	my $thour = $hour . ":00:00";
	my $mailer_id;
	($mailer_id,$temp_pid) = split('\|',$pid);
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id,third_party_id,status) values($cid,$i,'3','$thour',$temp_pid,$bid,$mailer_id,'$cstatus')";
	$rows = $dbh->do($sql);
    if ($hour == 12)
    {
    	$hour = $hour - 12;
    }
    elsif ($hour == 24)
   	{
       	$hour = $hour - 12;
   	}
	#
	# Update all campaigns for this client and slot
	#
	$sql = "select campaign_id,schedule_date from camp_schedule_info where client_id=$cid and slot_id=$i and slot_type='3' and schedule_date >= curdate()";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($camp_id,$sdate) = $sth->fetchrow_array())
	{
    	$sdate = $sdate . " " . $hour . ":00";
		$sql="update campaign set profile_id=$temp_pid,brand_id=$bid,scheduled_datetime='$sdate' where campaign_id=$camp_id and status='S' and deleted_date is null";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
	$i++;
}
$i=1;
while ($i <= $aol_cnt)
{
   	$tid = $query->param("tid_A_$i");
   	$pid = $query->param("pid_A_$i");
   	$bid = $query->param("bid_A_$i");
   	$cstatus = $query->param("stat_A_$i");
   	$am_pm= $query->param("am_pm_A_$i");
	if ($am_pm eq "PM")
	{
		$hour = $tid + 12;
	}
	else
	{
		$hour = $tid;
		if ($hour < 10)
		{
			$hour = "0" . $hour;
		}
	}
	$hour = $hour . ":00:00";
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id,status) values($cid,$i,'A','$hour',$pid,$bid,'$cstatus')";
	$rows = $dbh->do($sql);
	$i++;
}
$i=1;
while ($i <= $daily_cnt)
{
   	$tid = $query->param("tid_D_$i");
   	$pid = $query->param("pid_D_$i");
   	$bid = $query->param("bid_D_$i");
   	$cstatus = $query->param("stat_D_$i");
   	$am_pm= $query->param("am_pm_D_$i");
	if ($am_pm eq "PM")
	{
		$hour = $tid + 12;
	}
	else
	{
		$hour = $tid;
		if ($hour < 10)
		{
			$hour = "0" . $hour;
		}
	}
	$hour = $hour . ":00:00";
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id,status) values($cid,$i,'D','$hour',$pid,$bid,'$cstatus')";
	$rows = $dbh->do($sql);
	$i++;
}
print "Location: upd_client_schedule.cgi?client_id=$cid&camp_cnt=$camp_cnt&daily_cnt=$daily_cnt&aol_cnt=$aol_cnt&third_cnt=$third_cnt&rotating_cnt=$rotating_cnt\n\n";
$util->clean_up();
exit(0);
