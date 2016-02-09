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
my $chunk_cnt;
my $tid;
my $tmin;
my $i;
my $bid;
my $mta_id;
my $performance;
my $log_camp;
my $source_url;
my $vsgID;
my $cstatus;
my $pid;
my $temp_pid;
my $am_pm;
my $hour;

# connect to the util database

###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

my $cid= $query->param('cid');
my $special_flag= $query->param('special_flag');

# Update the information in the tables
$sql = "delete from schedule_info where client_id=$cid";
$rows = $dbhu->do($sql);

$sql = "select campaign_cnt,aol_cnt,daily_cnt,rotating_cnt,3rdparty_cnt,chunk_cnt from network_schedule where client_id=$cid"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($camp_cnt,$aol_cnt,$daily_cnt,$rotating_cnt,$third_cnt,$chunk_cnt) = $sth->fetchrow_array();
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
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id, status) values($cid,$i,'C','$thour',$pid,$bid, '$cstatus')";
	$rows = $dbhu->do($sql);
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
	$sql = "select campaign_id,schedule_date from camp_schedule_info where client_id=$cid and slot_id=$i and slot_type='C' and schedule_date >= curdate() and camp_schedule_info.status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($camp_id,$sdate) = $sth->fetchrow_array())
	{
    	$sdate = $sdate . " " . $hour . ":00";
		$sql="update campaign set profile_id=$pid,brand_id=$bid,scheduled_datetime='$sdate',scheduled_date=date('$sdate'),scheduled_time=time('$sdate') where campaign_id=$camp_id and status='S' and deleted_date is null";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
	$i++;
}
$i=1;
while ($i <= $third_cnt)
{
   	$tid = $query->param("tid_3_$i");
   	$tmin = $query->param("tmin_3_$i");
   	$pid = $query->param("pid_3_$i");
   	$bid = $query->param("bid_3_$i");
   	$mta_id = $query->param("mta_3_$i");
   	$performance = $query->param("perf_3_$i");
   	$log_camp = $query->param("log_3_$i");
   	$vsgID = $query->param("vsgID_3_$i");
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
    	if ($hour == 12)
    	{
    		$hour = $hour - 12;
    	}
	}
	if (length($tmin) == 1)
	{
		$tmin="0".$tmin;
	}
	my $thour = $hour . ":".$tmin.":00";
	my $mailer_id;
	($mailer_id,$temp_pid) = split('\|',$pid);
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id, vsgID,third_party_id,status,mta_id,performance,log_campaign) values($cid,$i,'3','$thour',$temp_pid,$bid,'$vsgID', $mailer_id,'$cstatus',$mta_id,$performance,'$log_camp')";
	$rows = $dbhu->do($sql);
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
	$sql = "select campaign_id,schedule_date from camp_schedule_info where client_id=$cid and slot_id=$i and slot_type='3' and schedule_date >= curdate() and camp_schedule_info.status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($camp_id,$sdate) = $sth->fetchrow_array())
	{
    	$sdate = $sdate . " " . $hour . ":00";
		$sql="update campaign set profile_id=$temp_pid,brand_id=$bid,scheduled_datetime='$sdate',scheduled_date=date('$sdate'),scheduled_time=time('$sdate') where campaign_id=$camp_id and deleted_date is null";
		$rows = $dbhu->do($sql);
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
	$rows = $dbhu->do($sql);
	$i++;
}
$i=1;
while ($i <= $chunk_cnt)
{
   	$tid = $query->param("tid_W_$i");
   	$pid = $query->param("pid_W_$i");
   	$bid = $query->param("bid_W_$i");
   	$cstatus = $query->param("stat_W_$i");
   	$am_pm= $query->param("am_pm_W_$i");
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
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id,status) values($cid,$i,'W','$hour',$pid,$bid, '$cstatus')";
	$rows = $dbhu->do($sql);
	$i++;
}
$i=1;
while ($i <= $daily_cnt)
{
   	$tid = $query->param("tid_D_$i");
   	$pid = $query->param("pid_D_$i");
   	$bid = $query->param("bid_D_$i");
   	$performance = $query->param("perf_D_$i");
   	$log_camp = $query->param("log_D_$i");
   	$source_url = $query->param("url_D_$i");
   	$cstatus = $query->param("stat_D_$i");
	$sql = "insert into schedule_info(client_id,slot_id,slot_type,schedule_time,profile_id,brand_id,vsgID, status,performance,log_campaign,mta_id,source_url) values($cid,$i,'D','',0,$bid,'$vsgID', '$cstatus',$performance,'$log_camp',$pid,'$source_url')";
	$rows = $dbhu->do($sql);
	#
	# Update all campaigns for this client and slot
	#
	$sql = "select campaign_id from camp_schedule_info where client_id=$cid and slot_id=$i and slot_type='D' and status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	while (($camp_id) = $sth->fetchrow_array())
	{
		$sql="update campaign set brand_id=$bid where campaign_id=$camp_id and deleted_date is null";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
	$i++;
}
if ($special_flag == 0)
{
	print "Location: upd_client_schedule.cgi?client_id=$cid&camp_cnt=$camp_cnt&daily_cnt=$daily_cnt&aol_cnt=$aol_cnt&third_cnt=$third_cnt&rotating_cnt=$rotating_cnt\n\n";
}
else
{
	print "Content-type: text/html\n\n";
print<<"end_of_html";
<html><head><title>Client Updated</title></head>
<body>
<center>
<h4>Client schedule has been updated</h4>
<a href="mainmenu.cgi"><img src="/images/home_blkline.gif" border="0"></a>
</body>
</html>
end_of_html
}
$util->clean_up();
exit(0);
