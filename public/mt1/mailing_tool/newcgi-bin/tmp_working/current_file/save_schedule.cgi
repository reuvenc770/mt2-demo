#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of weekly.html page 
# Name   : save_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 08/08/05  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use Net::FTP;
use util;
use thirdparty;
use HTML::LinkExtor;
use WWW::Curl::easy;
use URI::Split qw(uri_split uri_join);
use File::Basename;

#------  get some objects to use later ---------
my $util = util->new;
my $thirdparty = thirdparty->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $sth1;
my $dir2;
my $rows;
my $camp_id;
my $dbh;
my $phone;
my $email;
my $id;
my $aim;
my $website;
my $username;
my $password;
my $tables;
my $adv_id= $query->param('adv_id');
my $startdate = $query->param('startdate');
my $nid= $query->param('nid');
my $stype= $query->param('stype');
if ($stype eq "")
{
	$stype="C";
}
my $advertiser_name;
my $sdate;
my $sdate1;
my $cname;
my $edate;
my $cdate;
my $startdate1;
my $cday;
my $slot_id;
my $tday;
my $client_id;
my $stime;
my $profile_id;
my $brand_id;
my $third_id;
my $creative1;
my $subject1;
my $from1;
my $send_email;
my $from_addr;
my $suppid;
my $supp_name;
my $last_updated;
my $filedate;
my $daycnt;
my $deploy_name;
my $mailer_name;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
$util->db_connect();
$dbh = $util->get_dbh;
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Weekly</title>
</head>
<body>
end_of_html
$sql = "select advertiser_name,vendor_supp_list_id from advertiser_info where advertiser_id=$adv_id"; 
$sth = $dbh->prepare($sql);
$sth->execute();
($cname,$suppid) = $sth->fetchrow_array();
$sth->finish();
#
# Check suppression for advertiser
#
$sql = "select list_name,last_updated,filedate,datediff(curdate(),last_updated) from vendor_supp_list_info where list_id=$suppid";
$sth = $dbh->prepare($sql);
$sth->execute();
($supp_name,$last_updated,$filedate,$daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($supp_name ne "NONE")
{
	if ($filedate eq "")
    {
    	if ($daycnt > 7)
        {
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "Out of Date Suppression <info\@spirevision.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@spirevision.com\n";
    		print MAIL "Subject: $cname has a suppression file from $last_updated\n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
    		print MAIL "$cname has a suppression file from $last_updated\n"; 
    		close MAIL;
		}
	}
	else
	{
    	$sql = "select datediff(curdate(),'$filedate')";
        $sth = $dbh->prepare($sql) ;
        $sth->execute();
        ($daycnt) = $sth->fetchrow_array();
        $sth->finish();
        if ($daycnt > 7)
        {
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "Out of Date Suppression <info\@spirevision.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@spirevision.com\n";
    		print MAIL "Subject: $cname has a suppression file from $filedate\n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
    		print MAIL "$cname has a suppression file from $filedate\n"; 
    		close MAIL;
		}
	}
}
else
{
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "No Suppression File <info\@spirevision.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@spirevision.com\n";
    		print MAIL "Subject: $cname has no suppression file \n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
    		print MAIL "$cname has no suppression file\n"; 
    		close MAIL;
}
#
my @chkboxs= $query->param('chkbox');
foreach my $chkbox (@chkboxs) 
{
   ($client_id,$slot_id,$cday) = split('_',$chkbox);
	$sql = "select hour(schedule_time),profile_id,brand_id,third_party_id from schedule_info where client_id=$client_id and slot_id=$slot_id and slot_type='$stype'";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($stime,$profile_id,$brand_id,$third_id) = $sth->fetchrow_array();
	$sth->finish();
#
#
		
	$sql = "select date_add('$startdate',interval $cday day)"; 
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($sdate) = $sth->fetchrow_array();
	$sth->finish();
	if ($stime == 12)
	{
		$stime = $stime - 12;
	}
	elsif ($stime == 24)
	{
		$stime = $stime - 12;
	}
	$sdate1 = $sdate . " " . $stime . ":00";
#
	if ($stype eq "3")
	{
		$sql = "select mailer_name from third_party_defaults where third_party_id=$third_id";
		my $sthq = $dbh->prepare($sql); 
		$sthq->execute(); 
		($mailer_name) = $sthq->fetchrow_array();
		$sthq->finish();
		$deploy_name=$cname . " (" . $mailer_name . ")";
		$sql = "insert into campaign(campaign_name,status,created_datetime,scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id) values('$deploy_name','C',now(),'$sdate1','$sdate1',$adv_id,$profile_id,$brand_id)";
	}
	else
	{
		$sql = "insert into campaign(campaign_name,status,created_datetime,scheduled_datetime,advertiser_id,profile_id,brand_id) values('$cname','S',now(),'$sdate1',$adv_id,$profile_id,$brand_id)";
	}
	$rows=$dbh->do($sql);
#
#	Check to see if advertiser rotation setup
#
	$sql = "select creative1_id,subject1,from1 from advertiser_setup where advertiser_id=$adv_id and class_id=4";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$send_email = 0;
	if (($creative1,$subject1,$from1) = $sth->fetchrow_array())
	{
		if (($creative1 == 0) || ($subject1 == 0) || ($from1 == 0))
		{
			$send_email = 1;
		}
	}
	else
	{
		$send_email = 1;
	}
	$sth->finish();

	if ($send_email == 1)
	{
    	open (MAIL,"| /usr/sbin/sendmail -t");
    	$from_addr = "No Creative Rotation <info\@spirevision.com>";
    	print MAIL "From: $from_addr\n";
    	print MAIL "To: setup\@spirevision.com\n";
    	print MAIL "Subject: Creative Rotation Missing\n";
    	my $date_str = $util->date(6,6);
    	print MAIL "Date: $date_str\n";
    	print MAIL "X-Priority: 1\n";
    	print MAIL "X-MSMail-Priority: High\n";
    	print MAIL "Need Creative Rotation for $cname - scheduled for $sdate1\n";
    	close MAIL;
	}
#
#	Get the campaign id and add to camp_schedule_info
#
	if ($stype ne "3")
	{
		$sql = "select campaign_id from campaign where campaign_name='$cname' and scheduled_datetime='$sdate1' and advertiser_id=$adv_id and profile_id=$profile_id and brand_id=$brand_id";
	}
	else
	{
		$sql = "select max(campaign_id) from campaign where campaign_name='$deploy_name' and scheduled_datetime='$sdate1' and advertiser_id=$adv_id and profile_id=$profile_id and brand_id=$brand_id";
	}
	$sth = $dbh->prepare($sql);
	$sth->execute();
	($camp_id) = $sth->fetchrow_array();
	$sth->finish();
	if ($stype ne "3")
	{
		$sql="insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id) values($client_id,$slot_id,'$stype','$sdate',$camp_id)";
		$rows=$dbh->do($sql);
	}
	else
	{
    	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,'$sdate',$client_id)";
    	$rows=$dbh->do($sql);
		$sql="insert into 3rdparty_campaign(third_party_id,client_id,brand_id,advertiser_id,deploy_name,campaign_id,scheduled_datetime) values($third_id,$client_id,$brand_id,$adv_id,'$deploy_name',$camp_id,'$sdate1')";
		$rows=$dbh->do($sql);
		$sql="insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id) values($client_id,$slot_id,'3','$sdate',$camp_id)";
		$rows=$dbh->do($sql);
        thirdparty::deploy_it($dbh,$third_id,$camp_id,$brand_id,$adv_id,$client_id);
	}		
}
print<<"end_of_html";
<center>
<h2>Schedule successfully Updated</h2>
<br>
end_of_html
if ($stype eq "C")
{ 
	print "<a href=\"/weekly.html\" target=_top>Weekly Schedule</a>\n";
}
elsif ($stype eq "A")
{
	print "<a href=\"/weekly_aol.html\" target=_top>Weekly AOL Schedule</a>\n";
}
else
{
	print "<a href=\"/weekly_3rd.html\" target=_top>3rd Party Weekly Schedule</a>\n";
}
print<<"end_of_html";
<br>
<a href="/cgi-bin/mainmenu.cgi" target=_top>Home</a>
</body>
</html>
end_of_html
