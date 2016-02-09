#!/usr/bin/perl
#
#------------------------------------------------------------------------
# Purpose: This program copies all the daily deals and triggers 
#------------------------------------------------------------------------ 
use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $rows;
my $sql;
my $sth;
my $cid;
my $newcid;

# ----- connect to the util database -------
my $dbhq;
my $dbhu;
($dbhq,$dbhu)=$util->get_dbh();

print "$$ - Starting at Copy Daily Deals\n";
#
$sql="select dd.campaign_id from daily_deals dd, campaign c where dd.campaign_id=c.campaign_id and c.deleted_date is null";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid)=$sth->fetchrow_array())
{
	print "$$ - Copying Daily Deal $cid\n";
	$sql="insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,creative1_id,subject1,from1) select user_id,campaign_name,status,now(),scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,creative1_id,subject1,from1 from campaign where campaign_id=$cid";
	$rows=$dbhu->do($sql);
	undef $newcid;
    $sql="select LAST_INSERT_ID()";
    my $stha = $dbhu->prepare($sql);
    $stha->execute();
    ($newcid) = $stha->fetchrow_array();
    $stha->finish();
	if ($newcid)
	{
		print "$$ - Daily Deal $cid copied to $newcid\n";
		$sql="update daily_deals set campaign_id=$newcid where campaign_id=$cid";
		$rows=$dbhu->do($sql);
		$sql="update camp_schedule_info set status='D' where campaign_id=$cid";
		$rows=$dbhu->do($sql);
		$sql="insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id,nl_id,override,usa_id,status) select client_id,slot_id,slot_type,schedule_date,$newcid,nl_id,override,usa_id,'A' from camp_schedule_info where campaign_id=$cid";
		$rows=$dbhu->do($sql);
		$sql="update campaign set deleted_date=curdate() where campaign_id=$cid";
		$rows=$dbhu->do($sql);
	}
}
$sth->finish();
#
# Copy Triggers
#
$sql="select distinct c.campaign_id from campaign c, CategoryTrigger ct where ct.campaign_id=c.campaign_id and c.deleted_date is null";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($cid)=$sth->fetchrow_array())
{
	print "$$ - Copying Trigger Deal $cid\n";
	$sql="insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,creative1_id,subject1,from1) select user_id,campaign_name,status,now(),scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,creative1_id,subject1,from1 from campaign where campaign_id=$cid";
	$rows=$dbhu->do($sql);
	undef $newcid;
    $sql="select LAST_INSERT_ID()";
    my $stha = $dbhu->prepare($sql);
    $stha->execute();
    ($newcid) = $stha->fetchrow_array();
    $stha->finish();
	if ($newcid)
	{
		print "$$ - Trigger $cid copied to $newcid\n";
	
		$sql="update CategoryTrigger set campaign_id=$newcid where campaign_id=$cid";
		$rows=$dbhu->do($sql);
		$sql="update campaign set deleted_date=curdate() where campaign_id=$cid";
		$rows=$dbhu->do($sql);
	}
}
$sth->finish();
