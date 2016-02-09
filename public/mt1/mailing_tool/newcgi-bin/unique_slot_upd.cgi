#!/usr/bin/perl
# *****************************************************************************************
# unique_slot_upd.cgi
#
# this page updates a UniqueSlot 
#
# History
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $pms = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $userid;
my $uid;
my $sdate;

# connect to the pms database
my ($dbhq,$dbhu)=$pms->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $pms->clean_up();
    exit(0);
}

# get fields from the form

my $sid= $query->param('sid');
my $shour= $query->param('shour');
my $smin= $query->param('smin');
my $am_pm= $query->param('am_pm');
my $stophour= $query->param('stophour');
my $stopmin= $query->param('stopmin');
my $stop_am_pm= $query->param('stop_am_pm');
my $cgroupid= $query->param('cgroupid');
my $igroupid= $query->param('igroupid');
my $pid= $query->param('pid');
my @dname = $query->param('dname');
my @cdname = $query->param('cdname');
my $pdname=$query->param('pdname');
my $cpdname=$query->param('cpdname');
my $utype= $query->param('utype');
my $log_camp = $query->param('log_camp');
my $mtaid= $query->param('mtaid');
if ($log_camp eq '')
{
	$log_camp="Off";
}
my $chour= $query->param('chour');
my $randomize=$query->param('randomize');
if ($randomize eq "")
{
	$randomize="N";
}
my $template_id= $query->param('template_id');
my $surl = $query->param('surl');
my $input_url = $query->param('input_url');
my $zip= $query->param('zip');
my $mail_from = $query->param('mail_from');
my $return_path = $query->param('return_path');
my $prepull = $query->param('prepull');
my $ConvertSubject= $query->param('ConvertSubject');
my $ConvertFrom = $query->param('ConvertFrom');
my $jlogProfileID= $query->param('jlogProfileID');
if ($prepull eq "")
{
	$prepull="N";
}
my $use_master = $query->param('use_master');
my $useRdns= $query->param('useRdns');
if ($use_master eq "")
{
	$use_master="N";
}
if ($useRdns eq "")
{
	$useRdns="N";
}
if ($zip eq "")
{
	$zip="ALL";
}
if ($input_url ne "")
{
	$surl=$input_url;
}
$surl=~s/'/''/g;


if ($am_pm eq "PM")
{
	#make sure we have set 12:00:00 for 12pm not 24:00:00
	if($shour < 12){
		$shour = $shour + 12;
	}
}
else
{
	if ($shour < 10)
    {
    	$shour = "0" . $shour;
    }
	if ($shour == 12)
	{
		$shour="00";
	}
}
if (length($smin) == 1)
{
	$smin="0".$smin;
}
my $thour = $shour . ":". $smin.":00";
if (($stophour == 0) and ($stopmin == 0))
{
	$stophour="00";
	$stopmin="00";
}
elsif ($stop_am_pm eq "PM")
{
	#make sure we have set 12:00:00 for 12pm not 24:00:00
	if($stophour < 12){
		$stophour = $stophour + 12;
	}
}
else
{
	if ($stophour < 10)
    {
    	$stophour = "0" . $stophour;
    }
	if ($stophour == 12)
	{
		$stophour="00";
	}
}
if (length($stopmin) == 1)
{
	$stopmin="0".$stopmin;
}
my $stoptime = $stophour . ":". $stopmin.":00";
$sql = "update UniqueSlot set client_group_id=$cgroupid,ip_group_id=$igroupid,schedule_time='$thour',profile_id=$pid,mailing_domain='$dname[0]',template_id=$template_id,slot_type='$utype',hour_offset=$chour,log_campaign='$log_camp',randomize_records='$randomize',mta_id=$mtaid,source_url='$surl',zip='$zip',mail_from='$mail_from',use_master='$use_master',useRdns='$useRdns',return_path='$return_path',prepull='$prepull',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',jlogProfileID=$jlogProfileID,end_time='$stoptime' where slot_id=$sid";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting Unique Slot record $sql: $errmsg");
	exit(0);
}
$sql="delete from UniqueSlotDomain where slot_id=$sid";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#dname)
{
	$sql="insert into UniqueSlotDomain(slot_id,mailing_domain) values($sid,'$dname[$i]')";
	$rows = $dbhu->do($sql);
	$i++;
}
$sql="delete from UniqueSlotContentDomain where slot_id=$sid";
$rows = $dbhu->do($sql);
my $i=0;
while ($i <= $#cdname)
{
	$sql="insert into UniqueSlotContentDomain(slot_id,domain_name) values($sid,'$cdname[$i]')";
	$rows = $dbhu->do($sql);
	$i++;
}
my $nl_id=14;
my $bid;
$sql="select brand_id from client_brand_info where client_id=64 and status='A' and nl_id=?";
$sth = $dbhq->prepare($sql) ;
$sth->execute($nl_id);
($bid)=$sth->fetchrow_array();
$sth->finish();

$pdname =~ s/[ \n\r\f\t]/\|/g ;
$pdname =~ s/\|{2,999}/\|/g ;
my @d_array = split '\|', $pdname;
my $tname;
my $cnt;
my $add_domain;
$add_domain=0;
if ($#dname < 0)
{
	$add_domain=1;
}
foreach $tname (@d_array)
{
#	$sql="select distinct domain from brand_available_domains where brandID=? and domain != 'arthuradvertising.com' and domain=? union select distinct url from brand_url_info where brand_id=? and url_type in ('O','Y') and url=?";
#	$sth=$dbhu->prepare($sql);
#	$sth->execute($bid,$tname,$bid,$tname);
#	if (($cnt)=$sth->fetchrow_array())
#	{
		$sql="insert ignore into UniqueSlotDomain(slot_id,mailing_domain) values($sid,'$tname')";
		$rows=$dbhu->do($sql);
		if ($add_domain == 1)
		{
			$sql="update UniqueSlot set mailing_domain='$tname' where slot_id=$sid";
			$rows=$dbhu->do($sql);
		}
		$add_domain=0;
#	}
#	$sth->finish();
}
$cpdname =~ s/[ \n\r\f\t]/\|/g ;
$cpdname =~ s/\|{2,999}/\|/g ;
my @d_array = split '\|', $cpdname;
my $tname;
my $cnt;
foreach $tname (@d_array)
{
	$sql="insert ignore into UniqueSlotContentDomain(slot_id,domain_name) values($sid,'$tname')";
	$rows=$dbhu->do($sql);
}
#
# update any slots already scheduled
#
$sql="select us.unq_id,uc.send_date from UniqueSchedule us, unique_campaign uc where us.slot_id=$sid and us.unq_id=uc.unq_id and uc.send_date >= curdate() and uc.server_id=0 and uc.status='START'";
$sth=$dbhu->prepare($sql);
$sth->execute();
while (($uid,$sdate)=$sth->fetchrow_array())
{
	$sql="update unique_campaign set group_id=$igroupid,client_group_id=$cgroupid,profile_id=$pid,mailing_template=$template_id,slot_type='$utype',hour_offset=$chour,log_campaign='$log_camp',mailing_domain='$dname[0]',randomize_records='$randomize',mta_id=$mtaid,source_url='$surl',mail_from='$mail_from',use_master='$use_master',useRdns='$useRdns',return_path='$return_path',prepull='$prepull',ConvertSubject='$ConvertSubject',ConvertFrom='$ConvertFrom',jlogProfileID=$jlogProfileID where unq_id=$uid";
	$rows = $dbhu->do($sql);
	$sql="delete from UniqueDomain where unq_id=$uid";
	$rows = $dbhu->do($sql);
	$sql="delete from UniqueContentDomain where unq_id=$uid";
	$rows = $dbhu->do($sql);
	$sql="insert ignore into UniqueDomain select $uid,mailing_domain from UniqueSlotDomain where slot_id=$sid";
	$rows = $dbhu->do($sql);
	$sql="insert ignore into UniqueContentDomain select $uid,domain_name from UniqueSlotContentDomain where slot_id=$sid";
	$rows = $dbhu->do($sql);

	$sql="select campaign_id from campaign where id='$uid' and scheduled_date='$sdate' and deleted_date is null";
	my $campid;
	my $cdate=$sdate." ".$thour;

	my $sth1=$dbhu->prepare($sql);
	$sth1->execute();
	while (($campid)=$sth1->fetchrow_array())
	{
		$sql="update campaign set scheduled_time='$thour',scheduled_datetime='$cdate' where campaign_id=$campid";
		$rows = $dbhu->do($sql);
	}
	$sth1->finish();
}
$sth->finish();

print "Location: unique_slot.cgi\n\n";
$pms->clean_up();
exit(0);
