#!/usr/bin/perl
# *****************************************************************************************
# unique_slot_add.cgi
#
# this page adds a new UniqueSlot 
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
my @dname;
my @cdname;

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

my $shour= $query->param('shour');
my $smin = $query->param('smin');
my $am_pm= $query->param('am_pm');
my $stophour= $query->param('stophour');
my $stopmin = $query->param('stopmin');
my $stop_am_pm= $query->param('stop_am_pm');
my $cgroupid= $query->param('cgroupid');
my $igroupid= $query->param('igroupid');
my $pid= $query->param('pid');
my @dname= $query->param('dname');
my @cdname= $query->param('cdname');
my $pdname=$query->param('pdname');
my $cpdname=$query->param('cpdname');
my $template_id= $query->param('template_id');
my $utype= $query->param('utype');
my $chour= $query->param('chour');
my $mtaid= $query->param('mtaid');
my $randomize= $query->param('randomize');
my $mail_from= $query->param('mail_from');
my $return_path = $query->param('return_path');
my $prepull = $query->param('prepull');
if ($prepull eq "")
{
	$prepull="N";
}
my $use_master = $query->param('use_master');
my $useRdns= $query->param('useRdns');
if ($randomize eq "")
{
	$randomize="N";
}
if ($use_master eq "")
{
	$use_master="N";
}
my $ConvertSubject= $query->param('ConvertSubject');
my $ConvertFrom= $query->param('ConvertFrom');
my $jlogProfileID= $query->param('jlogProfileID');
if ($useRdns eq "")
{
	$useRdns="N";
}
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

$sql = "insert into UniqueSlot(client_group_id,ip_group_id,schedule_time,profile_id,mailing_domain,template_id,slot_type,hour_offset,randomize_records,mta_id,mail_from,use_master,useRdns,return_path,prepull,ConvertSubject,ConvertFrom,jlogProfileID,end_time) values ($cgroupid,$igroupid,'$thour',$pid,'$dname[0]',$template_id,'$utype',$chour,'$randomize',$mtaid,'$mail_from','$use_master','$useRdns','$return_path','$prepull','$ConvertSubject','$ConvertFrom',$jlogProfileID,'$stoptime')";
$rows = $dbhu->do($sql);
if ($dbhu->err() != 0)
{
	$errmsg = $dbhu->errstr();
	util::logerror("Inserting Unique Slot record $sql: $errmsg");
	exit(0);
}
my $slot_id;

$sql="select max(slot_id) from UniqueSlot where client_group_id=$cgroupid and ip_group_id=$igroupid and slot_type='$utype'";
my $sth=$dbhu->prepare($sql);
$sth->execute();
($slot_id)=$sth->fetchrow_array();
$sth->finish();

my $i=0;
while ($i <= $#dname)
{
	$sql="insert into UniqueSlotDomain(slot_id,mailing_domain) values($slot_id,'$dname[$i]')";
	$rows=$dbhu->do($sql);
	$i++;
}
foreach my $d (@cdname)
{
	$sql="insert into UniqueSlotContentDomain(slot_id,domain_name) values($slot_id,'$d]')";
	$rows=$dbhu->do($sql);
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
		$sql="insert ignore into UniqueSlotDomain(slot_id,mailing_domain) values($slot_id,'$tname')";
		$rows=$dbhu->do($sql);
		if ($add_domain == 1)
		{
			$sql="update UniqueSlot set mailing_domain='$tname' where slot_id=$slot_id";
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
	$sql="insert ignore into UniqueSlotContentDomain(slot_id,domain_name) values($slot_id,'$tname')";
	$rows=$dbhu->do($sql);
}
print "Location: unique_slot.cgi\n\n";
$pms->clean_up();
exit(0);
