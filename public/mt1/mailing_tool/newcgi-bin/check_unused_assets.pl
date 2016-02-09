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
my $dbhq;
my $dbhu;
my $gid;
my $gname;
my $cnt;
($dbhq,$dbhu)=$util->get_dbh();
my $gid;
my $gstr="";
#
$sql="select distinct ig.group_id from DailyDealSettingDetailIpGroup ddsd, IpGroup ig,schedule_info si  where ddsd.group_id=ig.group_id and ddsd.dd_id=si.mta_id and si.slot_type='D' and si.status='A' and ig.status='Active'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($gid)=$sth->fetchrow_array())
{
	$gstr.=$gid.",";
}
$sth->finish();
chop($gstr);
$sql="select distinct ddsd.group_id from DailyDealSetting dds,DailyDealSettingDetail ddsd where dds.dd_id=ddsd.dd_id and dds.settingType='Trigger' and ddsd.group_id > 0";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($gid)=$sth->fetchrow_array())
{
	$gstr.=$gid.",";
}
$sth->finish();
chop($gstr);
#
#	Check for unused IP Groups 
#
$sql="select group_id,group_name from IpGroup where status='Active' and group_id not in (select distinct group_id from unique_campaign where send_date >= date_sub(curdate(),interval 2 week)) and group_id > 0 and group_id not in ($gstr)"; 
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($gid,$gname)=$sth->fetchrow_array())
{
	$sql="select count(*) from unique_campaign where group_id=? and send_date >= date_sub(curdate(),interval 30 day)"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($gid);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		print "Removing IPGroup $gid - $gname\n";
		$sql="update IpGroup set status='Deleted' where group_id=$gid";
		$rows=$dbhu->do($sql);
	}	
}
$sth->finish();

#
#	Check for unused Client Groups 
#
$sql="select client_group_id,group_name from ClientGroup where status='A' and client_group_id not in (select distinct client_group_id from unique_campaign where send_date >= date_sub(curdate(),interval 2 week)) and client_group_id not in (4400,4402,4629,4628,7308) and client_group_id not in (select distinct client_group_id from DeployPool where status='Active') and group_name not like 'ESP%' and client_group_id != (select parmval from sysparm where parmkey='SUPER_CLIENT_GROUP')";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($gid,$gname)=$sth->fetchrow_array())
{
	$sql="select count(*) from unique_campaign where client_group_id=? and send_date >= date_sub(curdate(),interval 30 day)"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($gid);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		print "Removing ClientGroup $gid - $gname\n";
		$sql="update ClientGroup set status='D' where client_group_id=$gid";
		$rows=$dbhu->do($sql);
	}	
}
$sth->finish();
#
#	Check for unused Profiles 
#
my $pid;
my $pname;
$sql="select profile_id,profile_name from UniqueProfile where status='A' and profile_id not in (select distinct profile_id from unique_campaign where send_date >= date_sub(curdate(),interval 2 week)) and profile_id not in (5320,4422,4668) and profile_id not in (select distinct profile_id from DeployPool where status='Active')";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($pid,$pname)=$sth->fetchrow_array())
{
	$sql="select count(*) from unique_campaign where profile_id=? and send_date >= date_sub(curdate(),interval 30 day)"; 
	$sth1=$dbhu->prepare($sql);
	$sth1->execute($pid);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		print "Removing Profile $pid - $pname\n";
		$sql="update UniqueProfile set status='D' where profile_id=$pid";
		$rows=$dbhu->do($sql);
	}	
}
$sth->finish();
