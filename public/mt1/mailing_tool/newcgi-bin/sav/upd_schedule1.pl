#!/usr/bin/perl

# *****************************************************************************************
# upd_schedule.pl 
#
# This program updates the schedule_info table in the util database
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later
my $util = util->new;
my $errmsg;
my $sth;
my $sth2;
my $sql;
my $dbh;
my $dbh1;
my $cstatus;
my $cname;
my $emails_sent;
my $cdate;
my $property='AdvantageZone';
my $cid;
my $max_emails;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;
#
#	Delete all old rows for this property
#
$sql = "delete from schedule_info where property='$property' and scheduled_datetime >= date_sub(curdate(),interval 7 day)";
my $rows = $dbh->do($sql);
#
$sql = "select campaign_name,campaign_id, emails_sent,status,scheduled_date,max_emails from campaign where scheduled_date >= date_sub(curdate(),interval 7 day) and deleted_date is null order by scheduled_date";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($cname,$cid,$emails_sent,$cstatus,$cdate,$max_emails) = $sth->fetchrow_array())
{
	# if not already sent then need to calculate amount that will be sent
	#
	if ($cstatus ne 'C')
	{
		if ($max_emails == -1)
		{
			$sql = "select sum(member_cnt)-sum(aol_cnt)-sum(hotmail_cnt)-sum(msn_cnt) from list, campaign_list where list.list_id=campaign_list.list_id and campaign_id=$cid";
			$sth2 = $dbh->prepare($sql);
			$sth2->execute();
			($emails_sent) = $sth2->fetchrow_array();
			$sth2->finish();
		}
		else
		{
			$emails_sent = $max_emails;
		}
	}
    $cname =~ s/'/''/g;
	$sql = "insert into schedule_info(property, campaign_name, scheduled_datetime, drop_cnt) values('$property','$cname','$cdate',$emails_sent)";
	my $rows = $dbh->do($sql);
}
$sth->finish();

$dbh->disconnect;
$util->clean_up();
exit(0);
