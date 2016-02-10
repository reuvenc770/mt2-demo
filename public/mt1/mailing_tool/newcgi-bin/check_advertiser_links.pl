#!/usr/bin/perl
#
#------------------------------------------------------------------------
# Purpose: This program checks each scheduled campaign to make sure the advertiser links exist 
#------------------------------------------------------------------------ 
use strict;
use lib "/var/www/html/newcgi-bin";
use util;

my $util = util->new;
my $dbh;
my $rows;
my $sql;
my $sth;
my $sth1;
my $camp_id;
my $cdate;
my $ctime;
my $ctype='N';
# ----- connect to the util database -------
my $dbhq;
my $dbhu;
my $client_id;
my $adv_id;
my $tracking_id;
my $ADV;
($dbhq,$dbhu)=$util->get_dbh();

#
#	Check for camps still in table
#
$sql="select distinct advertiser_id,user_id from campaign where deleted_date is null and scheduled_date between curdate() and date_add(curdate(),interval 3 day)"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($adv_id,$client_id)=$sth->fetchrow_array())
{
	if ($ADV->{$adv_id})
	{
	}
	else
	{
		$sql="select count(*) from advertiser_tracking where advertiser_id=? and client_id=1 and daily_deal='N'";
		$sth1=$dbhu->prepare($sql);
		$sth1->execute($adv_id);
		($ADV->{$adv_id})=$sth1->fetchrow_array();
		$sth1->finish();
	}
	my $cnt;
	$sql="select count(*) from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($adv_id,$client_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt < $ADV->{$adv_id})
	{
		print "NO tracking id for Advertiser $adv_id Client <$client_id>\n";
		$util->genLinks($dbhu,$adv_id,0);
	}
}
$sth->finish();
