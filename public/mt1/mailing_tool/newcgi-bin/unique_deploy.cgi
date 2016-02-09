#!/usr/bin/perl

#******************************************************************************
# unique_deploy.cgi
#
# this page deploys a campaign 
#
# History
# Jim Sobeck, 05/28/08, Creation
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $sid;
my $rows;
my $errmsg;
my $images = $util->get_images_url;
my $adv_id;
my $profile_id;
my $cname;
my $tracking_id;
my $nl_id;
my $sdate;
my $stime;
my $cdate;
my $cgroupid;
my $group_id;

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
my $uid = $query->param('uid');
$sql="select nl_id,advertiser_id,profile_id,campaign_name,send_date,send_time,curdate(),client_group_id,group_id from unique_campaign where unq_id=? and campaign_type='TEST'";
$sth=$dbhu->prepare($sql);
$sth->execute($uid);
if (($nl_id,$adv_id,$profile_id,$cname,$sdate,$stime,$cdate,$cgroupid,$group_id)=$sth->fetchrow_array())
{
	$sth->finish();
	add_campaigns($uid,$profile_id,$sdate,$stime,$cdate,$cgroupid,$group_id);
	$sql="update unique_campaign set campaign_type='DEPLOYED',status='START' where unq_id=$uid";
	my $rows=$dbhu->do($sql);
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html>
<head></head>
<body>
<center>
<h2>Campaign <b>$cname</b> has been deployed.</h2>
<br>
<a href="unique_deploy_list.cgi">Home</a>
</center>
</body></html>
end_of_html
}
else
{
	print "Content-type: text/html\n\n";
	print<<"end_of_html";
<html>
<head></head>
<body>
<center>
<h2>ID $uid has already been deployed.</h2>
<br>
<a href="unique_deploy_list.cgi">Home</a>
</center>
</body></html>
end_of_html
}
exit(0);

sub add_campaigns
{
	my ($tid,$profile_id,$sdate,$stime,$cdate,$cgroupid,$ipgroup_id)=@_;
	my $sql;
	my $profile_name;
	my $client_id;
	my $brand_id;
	my $camp_id;
	my $third_id;
	my $added_camp;
	my $cnt;
	my $priority;

	$sql="select count(*) from IpGroup ip where group_id=? and (ip.goodmail_enabled='Y' or ip.group_name like 'discover%' or ip.group_name like 'credithelpadvisor%')";
	my $sth1=$dbhu->prepare($sql);
	$sth1->execute($ipgroup_id);
	($cnt)=$sth1->fetchrow_array();
	$sth1->finish();
	if ($cnt > 0)
	{
		$priority=1;
	}
	else
	{
		$priority=5;
	}

	$added_camp=0;

	$sql="select client_id from ClientGroupClients where client_group_id=?";
	my $STHQ=$dbhq->prepare($sql);
	$STHQ->execute($cgroupid);
	while (($client_id) = $STHQ->fetchrow_array())
	{
		$sql="select brand_id,third_party_id from client_brand_info where client_id=? and nl_id=? and status='A' and brand_type='Newsletter'";
		my $STHQ1=$dbhq->prepare($sql);
		$STHQ1->execute($client_id,$nl_id);
		if (($brand_id,$third_id) = $STHQ1->fetchrow_array())
		{	
			my $timestr=$sdate." ".$stime;
			$sql = "insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,id) values($client_id,'$cname','C',now(),'$timestr',$adv_id,$profile_id,$brand_id,'$sdate','$stime','NEWSLETTER','$tid')";
			$rows=$dbhu->do($sql);
			$sql = "select max(campaign_id) from campaign where campaign_name='$cname' and scheduled_date='$sdate' and id='$tid' and advertiser_id=$adv_id and profile_id=$profile_id and brand_id=$brand_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($camp_id) = $sth->fetchrow_array();
			$sth->finish();
#		   	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,curdate(),$client_id)";
#		   	$rows=$dbhu->do($sql);
			if (($sdate eq $cdate) and ($added_camp == 0))
			{
				$sql="insert into current_campaigns(campaign_id,scheduled_date,scheduled_time,campaign_type) values($camp_id,curdate(),'$stime','DEPLOYED')";
				$rows=$dbhu->do($sql);
				$added_camp=1;
				$sql="select tracking_id from advertiser_tracking where advertiser_id=? and client_id=? and daily_deal='N'"; 
				my $sth1=$dbhu->prepare($sql);
				$sth1->execute($adv_id,$client_id);
				if (($tracking_id)=$sth1->fetchrow_array())
				{
				}
				else
				{
					gen_tracking($adv_id);
				}
				$sth1->finish();
			}
		}
		$STHQ1->finish();
	}
	$STHQ->finish();
}
sub gen_tracking()
{
	my ($aid)=@_;
	$util->genLinks($dbhu,$aid,0);
}
