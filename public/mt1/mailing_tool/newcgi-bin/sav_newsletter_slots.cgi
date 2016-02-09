#!/usr/bin/perl

# ******************************************************************************
# sav_newsletter_slots.cgi
#
# this page saves the newsletter slot information 
#
# History
#	Jim Sobeck	01/04/07	Creation	
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $client_id;
my $sql;
my $sid;
my $dbh;
my $list_id;
my $iopt;
my $sdate;
my $camp_id;
my $rows;
my $errmsg;
my $pname;
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
my $i;
my $bid;
my $cstatus;
my $pid;
my $temp_pid;
my $am_pm;
my $hour;
my $nl_id;
my $nl_slots;

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


$sql="select nl_id,nl_slots from newsletter";
$sth1=$dbhq->prepare($sql);
$sth1->execute();
while (($nl_id,$nl_slots)=$sth1->fetchrow_array())
{
	$sql = "delete from nl_slot_info where nl_id=$nl_id"; 
	$rows = $dbhu->do($sql);

# Update the information in the tables
	$i=1;
	while ($i <= $nl_slots)
	{
   		$tid = $query->param("tid_${nl_id}_$i");
   		$pid = $query->param("pid_${nl_id}_$i");
   		$cstatus = $query->param("stat_${nl_id}_$i");
		$am_pm= $query->param("am_pm_${nl_id}_$i");
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
		$sql="insert into nl_slot_info(nl_id,slot_id,schedule_time,profile_id,status) values($nl_id,$i,'$thour',$pid,'$cstatus')"; 
		$rows = $dbhu->do($sql);

		#
		# Update all campaigns for this client and slot
		#
		$sql = "select campaign_id,schedule_date from camp_schedule_info where nl_id=$nl_id and slot_id=$i and slot_type='N' and schedule_date >= curdate() and status='A'";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		while (($camp_id,$sdate) = $sth->fetchrow_array())
		{
    		$sdate = $sdate . " " . $hour . ":00";
			$sql="update campaign set profile_id=$pid,scheduled_datetime='$sdate',scheduled_date=date('$sdate'),scheduled_time=time('$sdate') where campaign_id=$camp_id and status='S' and deleted_date is null";
			$rows = $dbhu->do($sql);
		}
		$sth->finish();
		$i++;
	}
}
print "Location: /newcgi-bin/upd_newsletter_schedule.cgi\n\n"; 
$util->clean_up();
exit(0);
