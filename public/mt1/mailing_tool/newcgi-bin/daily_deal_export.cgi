#!/usr/bin/perl
#===============================================================================
# Name   : daily_deal_export.cgi 
#
#--Change Control---------------------------------------------------------------
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $sql;
my $sql1;
my $sth;
my $dbh;
my $uname;
my $company;
my $group_id=$query->param('group_id');
my $client_id=$query->param('client_id');
my $rows;
my $filename;
my $sth1;
my $bname;
my $ddname;
my $usaname;
my $aname;
my $creativeid;
my $subjectid;
my $fromid;
my $aid;
my $tsubject;
my $tfrom;
my $tcid;
my ($uid,$fname,$lname,$ctype,$cday,$bid,$cname,$cid);
my $slot_id;
my $sourceURL;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$group_id=~s/_/ /g;
$filename=$group_id."_".$client_id.".csv";
open(LOG,">/data3/3rdparty/$filename");
print LOG "client_name,client_id,Slotid,sourceURL,Day,USAName,DailyDealSetting,AdvertiserName,\n";
if ($client_id > 0)
{
	$sql="select u.first_name,daily_deals.client_id,daily_deals.cday,campaign.advertiser_id,csi.slot_id,dds.name,usa.name,si.source_url,ai.advertiser_name from daily_deals,campaign,camp_schedule_info csi,schedule_info si, DailyDealSetting dds,UniqueScheduleAdvertiser usa,user u,advertiser_info ai where campaign.advertiser_id=ai.advertiser_id and daily_deals.campaign_id=campaign.campaign_id and daily_deals.client_id=csi.client_id and daily_deals.client_id=u.user_id and campaign.campaign_id=csi.campaign_id and campaign.deleted_date is null and daily_deals.client_id=$client_id and csi.slot_id=si.slot_id and si.client_id=$client_id and si.slot_type='D' and si.mta_id=dds.dd_id and dds.settingType='Daily' and csi.usa_id=usa.usa_id and csi.status='A' and si.status='A' order by daily_deals.client_id,daily_deals.cday,csi.slot_id";
}
else
{
	if ($group_id eq "ALL")
	{
		$sql="select u.first_name,daily_deals.client_id,daily_deals.cday,campaign.advertiser_id,csi.slot_id,dds.name,usa.name,si.source_url,ai.advertiser_name from daily_deals,campaign,camp_schedule_info csi,schedule_info si, DailyDealSetting dds,UniqueScheduleAdvertiser usa,user u,advertiser_info ai  where campaign.advertiser_id=ai.advertiser_id and daily_deals.campaign_id=campaign.campaign_id and daily_deals.client_id=csi.client_id and daily_deals.client_id=u.user_id and campaign.campaign_id=csi.campaign_id and campaign.deleted_date is null and csi.slot_id=si.slot_id and si.client_id=csi.client_id and si.slot_type='D' and si.mta_id=dds.dd_id and dds.settingType='Daily' and csi.usa_id=usa.usa_id and csi.status='A' and si.status='A' and u.status='A' order by daily_deals.client_id,daily_deals.cday,csi.slot_id";
	}
	else
	{
		$sql="select u.first_name,daily_deals.client_id,daily_deals.cday,campaign.advertiser_id,csi.slot_id,dds.name,usa.name,si.source_url from daily_deals,campaign,camp_schedule_info csi,user u,schedule_info si, DailyDealSetting dds,UniqueScheduleAdvertiser usa where daily_deals.campaign_id=campaign.campaign_id and daily_deals.client_id=csi.client_id and campaign.campaign_id=csi.campaign_id and campaign.deleted_date is null and u.user_id=daily_deals.client_id and u.client_type='$group_id' and csi.slot_id=si.slot_id and si.client_id=csi.client_id and si.slot_type='D' and si.mta_id=dds.dd_id and dds.settingType='Daily' and csi.usa_id=usa.usa_id and csi.status='A' and u.status='A' order by daily_deals.client_id,daily_deals.cday,csi.slot_id";
	}
}
$sth=$dbhu->prepare($sql);
$sth->execute();
my $SLOT;
my $old_client=0;
my $old_uname;
my $last_slot=0;
my $last_day=0;
while (($uname,$uid,$cday,$aid,$slot_id,$ddname,$usaname,$sourceURL,$aname)=$sth->fetchrow_array())
{
	if ($old_client != $uid)
	{
		if ($old_client == 0)
		{
			$old_client=$uid;
			$old_uname=$uname;
		}
		else
		{
			my $tslot;
			my $tdname;
			$sql="select slot_id,dds.name,si.source_url from schedule_info si, DailyDealSetting dds where slot_type='D' and client_id=? and si.mta_id=dds.dd_id and dds.settingType='Daily'";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($old_client);
			while (($tslot,$tdname,$sourceURL)=$sthq->fetchrow_array())
			{
				my $i=1;
				while ($i <= 7)
				{
					if (!$SLOT->{$tslot}{$i})
					{
						$SLOT->{$tslot}{$i}=1;
						print LOG "$old_uname,$old_client,$tslot,$sourceURL,$i,,$tdname,,\n";
					}
					$i++;
				}
			}
			$sthq->finish();
			$old_client=$uid;
			$old_uname=$uname;
			$SLOT={};
			$last_slot=0;
		}
	}
	if ($last_day == 0)
	{
		$last_day=$cday;
	}
	my $tslot_id=$last_slot+1;
	if ($slot_id != $tslot_id)	
	{
		my $i=$tslot_id;
		while ($i < $slot_id)
		{
			my $tdname;
			$sql="select dds.name,si.source_url from schedule_info si, DailyDealSetting dds where slot_type='D' and client_id=? and si.mta_id=dds.dd_id and dds.settingType='Daily' and slot_id=?";
			my $sthq=$dbhu->prepare($sql);
			$sthq->execute($uid,$i);
			($tdname,$sourceURL)=$sthq->fetchrow_array();
			$sthq->finish();
			$SLOT->{$i}{$cday}=1;
			print LOG "$uname,$uid,$i,$sourceURL,$cday,,$tdname,,\n";
			$i++;
		}
	}
	$last_slot=$slot_id;
	$last_day=$cday;
	print LOG "$uname,$uid,$slot_id,$sourceURL,$cday,$usaname,$ddname,$aname,\n";
	$SLOT->{$slot_id}{$cday}=1;
}
$sth->finish();
my $tslot;
my $tdname;
$sql="select slot_id,dds.name,si.source_url from schedule_info si,DailyDealSetting dds where slot_type='D' and client_id=? and si.mta_id=dds.dd_id and dds.settingType='Daily' order by slot_id";
my $sthq=$dbhu->prepare($sql);
$sthq->execute($old_client);
while (($tslot,$tdname,$sourceURL)=$sthq->fetchrow_array())
{
	my $i=1;
	while ($i <= 7)
	{
		if (!$SLOT->{$tslot}{$i})
		{
			print LOG "$old_uname,$old_client,$tslot,$sourceURL,$i,,$tdname,,\n";
		}
		$i++;
	}
}
$sthq->finish();
close(LOG);

print "Content-Type: text/html\n\n";
print<<"end_of_html";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Exported Daily Deal Schedule</title>

<style type="text/css">

body {
	background: url(http://www.affiliateimages.com/temp/bg.jpg) top center repeat-x #99D1F4;
	font: .75em/1.3em Tahoma, Arial, sans-serif;
	color: #4d4d4d;
  }

h1, h2 {
	font-family: 'Trebuchet MS', Arial, san-serif;
	text-align: center;
	font-weight: normal;
  }

h1 {
	font-size: 2em;
  }

h2 {
	font-size: 1.2em;
  }

h4 {
	font-weight: normal;
	margin: 1em 0;
	text-align: center;
  }

h4 input {
	font-size: .8em;
  }

a:link, a:visited {
	color: #33f;
	text-decoration: none;
  }

a:hover, a:focus {
	color: #66f;
	text-decoration: underline;
  }

div.filter {
	text-align: center;
  }

div.filter select {
	font: 11px/14px Tahoma, Arial, sans-serif;
  }

#container {
	width: 90%;
	padding-top: 5%;
	width: expression( document.body.clientWidth < 1025 ? "1024px" : "auto" ); /* set min-width for IE */
	min-width: 1024px;
	margin: 0 auto;
  }

div.overflow {
	/* overflow: auto; */
  }

table {
	background: #FFF;
	border: 1px solid #666;
	width: 780px;
	margin: 0 auto;
	margin-bottom: .5em;
  }

table td {
	padding: .325em;
	border: 1px solid #ABC;
	text-align: center;
  }

table .label {
	font-weight: bold;
	color: #000;
  }

table tr.alt {
	background: #DDD;
  }

table tr.label {
	background: #6C3;
  }

table td.label {
	text-align: left;
	background: #6C3;
  }

td.field {
	width: 60%;
  }

input.field, select.field, textarea.field {
	padding: .15em;
	border: 1px solid #999;
	color: #000;
	font-family: Tahoma, Arial, sans-serif;
  }

input.field:hover, select.field:hover, textarea.field:hover {
	background: #F9FFE9;
  }

input.field:focus, select.field:focus, textarea.field:focus {
	background: #F9FFE9;
	border: 1px inset;
  }

.submit {
	text-align: center;
	margin-bottom: .3em;
  }

input.submit {
	font-size: 2em;
	color: #444;
  }

input.radio {
	border: 0;
  }

.note {
	font-size: .8em;
  }

</style>

</head>

<body>
<center>
<h4><a href="/downloads/$filename">Click here</a> to download file</h4>
</center>
<br>
</body>
</html>
end_of_html
