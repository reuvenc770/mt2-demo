#!/usr/bin/perl
#
#------------------------------------------------------------------------
# Purpose: This program gets the currently scheduled deals for tomorrow
#		   by default.  You can pass a date to get a different days
#			campaigns and places them in the current_campaigns table
#------------------------------------------------------------------------ 
use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $rows;
my $sql;
my $sth;
my $camp_id;
my $cdate;
my $send_cnt;
my $inj_cnt;
my $ctime;
my $ctype;
my $priority;
my $uid;
my $cname;
my $cstatus;
my $brand_id;
my $client_id;
my $ip_group;

# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

my $sdate=$ARGV[0];
$cdate = localtime();
print "$$ - Starting at $cdate <$sdate>\n";
if ($sdate eq "")
{
	$sql="select date_add(curdate(),interval 1 day)";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	($sdate)=$sth->fetchrow_array();
	$sth->finish();
}
#
#	Check for camps still in table
#
my $reccnt;
$sql="select count(*) from current_campaigns where scheduled_date < '$sdate'";
$sth=$dbhu->prepare($sql);
$sth->execute();
($reccnt)=$sth->fetchrow_array();
$sth->finish();
#if ($reccnt > 0)
#{
	open (MAIL,"| /usr/sbin/sendmail -t");
    my $from_addr = "Campaigns not sent from current_campaigns<info\@zetainteractive.com>";
   	print MAIL "From: $from_addr\n";
    print MAIL "To: alert.operations\@zetainteractive.com, serverops\@zetainteractive.com, jsobeck\@zetainteractive.com\n";
    print MAIL "Cc: sysadmin\@zetainteractive.com\n";
	print MAIL "Subject: Some campaigns not sent\n"; 
	my $date_str = $util->date(6,6);
    print MAIL "Date: $date_str\n";
	print MAIL "X-Priority: 1\n";
    print MAIL "X-MSMail-Priority: High\n";
    print MAIL "$reccnt campaigns not sent today\n\n"; 
	$sql="select campaign_id,scheduled_date,scheduled_time,campaign_type from current_campaigns where scheduled_date < '$sdate'";
	$sth=$dbhu->prepare($sql);
	$sth->execute();
	while (($camp_id,$cdate,$ctime,$ctype)=$sth->fetchrow_array())
	{
		$sql="select c.campaign_name ,c.id, c.brand_id, 3c.client_id from campaign c JOIN 3rdparty_campaign 3c on c.campaign_id = 3c.campaign_id where c.campaign_id=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($camp_id);
		($cname, $uid, $brand_id, $client_id)=$sth1->fetchrow_array();
		$sth1->finish();

		if ($ctype eq "DEPLOYED")
		{
#			$sql="update 3rdparty_campaign set status='CANCELLED' where campaign_id in (select campaign_id from campaign where id='$uid' and scheduled_date='$cdate')";
#			$rows=$dbhu->do($sql);
		}
		else
		{
			print MAIL"\tCID:$camp_id\t$brand_id\t$client_id\t$cdate $ctime\t$cname\t$ctype\n";
		}
	}
	$sth->finish();

	$sql="delete from current_campaigns where scheduled_date < '$sdate'";
	$rows=$dbhu->do($sql);

	# Get all uniques not sent
	#
    print MAIL "\n\nUnique campaigns not finished\n\n"; 
	$sql="select uc.campaign_name,ig.group_name,uc.unq_id,uc.status,uc.send_date,uc.send_cnt,uc.inj_cnt from unique_campaign uc 
	JOIN IpGroup ig ON uc.group_id = ig.group_id
	where uc.send_date=date_sub('$sdate',interval 1 day) 
	and uc.status not in ('PULLED','CANCELLED') 
	and uc.group_id not in (select group_id from IpGroup where goodmail_enabled='Y')";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($cname,$ip_group,$uid,$cstatus,$cdate,$send_cnt,$inj_cnt)=$sth1->fetchrow_array())
	{
			print MAIL"\t$cname\t$ip_group\t$uid\t$cstatus\t$send_cnt\t$inj_cnt\n";
			$sql="update unique_campaign set status='CANCELLED',cancel_reason='Nightly Process' where unq_id=$uid";
			$rows=$dbhu->do($sql);
			print "<$sql>\n";
	}
	$sth1->finish();
	$sql="select uc.campaign_name,ig.group_name,uc.unq_id,uc.status,uc.send_date,uc.send_cnt,uc.inj_cnt from unique_campaign uc
	JOIN IpGroup ig ON uc.group_id = ig.group_id
	where uc.send_date=date_sub('$sdate',interval 1 day) 
	and uc.status in ('PAUSED','START') 
	and uc.group_id not in (select group_id from IpGroup where goodmail_enabled='Y') 
	and uc.slot_type = 'Time Based'";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($cname,$ip_group,$uid,$cstatus,$cdate,$send_cnt,$inj_cnt)=$sth1->fetchrow_array())
	{
			print MAIL"\t$cname\t$ip_group\t$uid\t$cstatus\t$send_cnt\t$inj_cnt\n";
			$sql="update unique_campaign set status='CANCELLED',cancel_reason='Nightly Process' where unq_id=$uid";
			$rows=$dbhu->do($sql);
			print "<$sql>\n";
	}
	$sth1->finish();
	#
	# Cancel any deploys older than 48 hours
	#
	$sql="select uc.campaign_name,ig.group_name,uc.unq_id,uc.status,uc.send_date,uc.send_cnt,uc.inj_cnt from unique_campaign uc
	JOIN IpGroup ig ON uc.group_id = ig.group_id
	where uc.send_date=date_sub('$sdate',interval 2 day) 
	and uc.status not in ('PULLED','CANCELLED')";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($cname,$ip_group,$uid,$cstatus,$cdate,$send_cnt,$inj_cnt)=$sth1->fetchrow_array())
	{
			print MAIL"\t$cname\t$ip_group\t$uid\t$cstatus\t$send_cnt\t$inj_cnt\n";
			$sql="update unique_campaign set status='CANCELLED',cancel_reason='Nightly Process' where unq_id=$uid";
			$rows=$dbhu->do($sql);
			print "<$sql>\n";
	}
	$sth1->finish();
	close MAIL;
#}

open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "Deploy Pools not sent<info\@zetainteractive.com>";
   	print MAIL "From: $from_addr\n";
    print MAIL "To: mailops\@zetainteractive.com, jsobeck\@zetainteractive.com\n";
	print MAIL "Subject: Deploy Pools not sent\n"; 
	my $date_str = $util->date(6,6);
    print MAIL "Date: $date_str\n";
	print MAIL "X-Priority: 1\n";
    print MAIL "X-MSMail-Priority: High\n";
$sql="select deployPoolName,group_name,profile_name from DeployPool dp,ClientGroup cg, UniqueProfile up where dp.status='Active' and create_datetime < date_sub(curdate(),interval 2 day) and deployPoolID not in (select distinct deployPoolID from unique_campaign where deployPoolID > 0 and send_date between date_sub(curdate(),interval 7 day) and curdate()) and dp.client_group_id=cg.client_group_id and dp.profile_id=up.profile_id";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $poolName;
my $group_name;
my $profilename;
while (($poolName,$group_name,$profilename)=$sth1->fetchrow_array())
{
	print MAIL "$poolName,$group_name,$profilename\n";
}
$sth1->finish();
close(MAIL);
#
$sql="update DeployPool set status='Deleted' where status='Active' and create_datetime < date_sub(curdate(),interval 2 day) and deployPoolID not in (select distinct deployPoolID from unique_campaign where deployPoolID > 0 and send_date between date_sub(curdate(),interval 10 day) and date_add(curdate(),interval 1 day))";
my $rows=$dbhu->do($sql);
#
# Send another alert that will be ignored
#
$sql="select date_sub(curdate(),interval 1 day)";
$sth=$dbhu->prepare($sql);
$sth->execute();
($sdate)=$sth->fetchrow_array();
$sth->finish();
open (MAIL,"| /usr/sbin/sendmail -t");
my $from_addr = "Random Advertiser <info\@zetainteractive.com>";
print MAIL "From: $from_addr\n";
print MAIL "To: mailops\@zetainteractive.com"; 
print MAIL "Subject: Random Advertiser Scheduled $sdate\n"; 
my $date_str = $util->date(6,6);
print MAIL "Date: $date_str\n";
print MAIL "X-Priority: 1\n";
print MAIL "X-MSMail-Priority: High\n";
$sql="select send_date,advertiser_name,unq_id from unique_campaign uc join advertiser_info ai on ai.advertiser_id=uc.advertiser_id where send_date=date_sub(curdate(),interval 1 day) and RandomType != ''";
my $sth1=$dbhu->prepare($sql);
$sth1->execute();
my $aname;
my $unq_id;
while (($sdate,$aname,$unq_id)=$sth1->fetchrow_array())
{
	print MAIL "$sdate,$aname,$unq_id\n";
}
$sth1->finish();
close(MAIL);
my $cdate = localtime();
print "$$ - Finishing at $cdate <$sdate>\n";
$util->clean_up();
exit(0) ;
