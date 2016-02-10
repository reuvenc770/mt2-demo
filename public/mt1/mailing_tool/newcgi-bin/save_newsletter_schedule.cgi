#!/usr/bin/perl
#===============================================================================
# Purpose: Script to save newsletter schedule page 
# Name   : save_newsletter_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 01/08/07  Jim Sobeck  Creation
# 01/09/07	Jim Sobeck	Added logic for batch delete
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use Net::FTP;
use util;
use HTML::LinkExtor;
use URI::Split qw(uri_split uri_join);
use File::Basename;

#------  get some objects to use later ---------
my $util = util->new;
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
my $profile_name;
my $password;
my $tables;
my $STRONGMAIL_ID=10;
my $adv_id= $query->param('adv_id');
my $startdate = $query->param('startdate');
my $nl_id= $query->param('nl_id');
my $delflag= $query->param('delflag');
if ($delflag eq "")
{
	$delflag="N";
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
my $catid;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
#
$sql = "select advertiser_name,vendor_supp_list_id,category_id from advertiser_info where advertiser_id=$adv_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($cname,$suppid,$catid) = $sth->fetchrow_array();
$sth->finish();
$third_id=$STRONGMAIL_ID;
#
# Check suppression for advertiser
#
if ($delflag eq "N")
{
$sql = "select list_name,last_updated,filedate,datediff(curdate(),last_updated) from vendor_supp_list_info where list_id=$suppid";
$sth = $dbhq->prepare($sql);
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
    		$from_addr = "Out of Date Suppression <info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: $cname has a suppression file from $last_updated\n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    	if ($daycnt > 10)
        {
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
		}
    		print MAIL "$cname has a suppression file from $last_updated\n"; 
    		close MAIL;
		}
	}
	else
	{
    	$sql = "select datediff(curdate(),'$filedate')";
        $sth = $dbhq->prepare($sql) ;
        $sth->execute();
        ($daycnt) = $sth->fetchrow_array();
        $sth->finish();
        if ($daycnt > 7)
        {
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "Out of Date Suppression <info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: $cname has a suppression file from $filedate\n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
        if ($daycnt > 10)
        {
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
		}
    		print MAIL "$cname has a suppression file from $filedate\n"; 
    		close MAIL;
		}
	}
}
else
{
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "No Suppression File <info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: $cname has no suppression file \n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    		print MAIL "X-Priority: 1\n";
    		print MAIL "X-MSMail-Priority: High\n";
    		print MAIL "$cname has no suppression file\n"; 
    		close MAIL;
}
#
#
#	Check to see if advertiser rotation setup
#
$sql = "select creative1_id,subject1,from1 from advertiser_setup where advertiser_id=$adv_id and class_id=4";
$sth = $dbhq->prepare($sql);
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
	$from_addr = "No Creative Rotation <info\@zetainteractive.com>";
	print MAIL "From: $from_addr\n";
	print MAIL "To: setup\@zetainteractive.com\n";
	print MAIL "Subject: Creative Rotation Missing\n";
	my $date_str = $util->date(6,6);
	print MAIL "Date: $date_str\n";
	print MAIL "X-Priority: 1\n";
	print MAIL "X-MSMail-Priority: High\n";
	print MAIL "Need Creative Rotation for $cname - scheduled for $sdate1\n";
	close MAIL;
}
#
$sql = "select mailer_name from third_party_defaults where third_party_id=$third_id";
my $sthq = $dbhq->prepare($sql); 
$sthq->execute(); 
($mailer_name) = $sthq->fetchrow_array();
$sthq->finish();
$deploy_name=$cname . " (" . $mailer_name . ")";
$deploy_name=~s/'/''/g;
#
my @chkboxs= $query->param('chkbox');
my $tnl_id;
$client_id=0;
foreach my $chkbox (@chkboxs) 
{
   ($tnl_id,$slot_id,$cday) = split('_',$chkbox);
	#
	# Check to see if any exclusions
	#
	my $sth1a;
    my $reccnt;
    $sql = "select count(*) from client_category_exclusion,client_advertiser_exclusion where (client_category_exclusion.client_id=? and client_category_exclusion.category_id=?) or (client_advertiser_exclusion.client_id=? and client_advertiser_exclusion.advertiser_id=?)";
    $sth1a = $dbhq->prepare($sql);
    $sth1a->execute($client_id,$catid,$client_id,$adv_id);
    ($reccnt) = $sth1a->fetchrow_array();
    $sth1a->finish();
    #
    if ($reccnt == 0)
    {
		$sql = "select hour(schedule_time),nl_slot_info.profile_id,profile_name from nl_slot_info,list_profile where nl_slot_info.nl_id=$tnl_id and slot_id=$slot_id and nl_slot_info.profile_id=list_profile.profile_id";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($stime,$profile_id,$profile_name) = $sth->fetchrow_array();
		$sth->finish();
#
#
		$sql = "select date_add('$startdate',interval $cday day)"; 
		$sth = $dbhq->prepare($sql);
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
		# Get all the profiles and brands defined for this newletter
		#
		$sql="select profile_id,client_id from list_profile where profile_name=? and nl_id=? and status='A'";
		my $STHQ=$dbhq->prepare($sql);
		$STHQ->execute($profile_name,$tnl_id);
		my $cpid;
		while (($cpid,$client_id) = $STHQ->fetchrow_array())
		{
			$sql="select brand_id from client_brand_info where client_id=? and nl_id=? and status='A' and brand_type='Newsletter'";
			my $STHQ1=$dbhq->prepare($sql);
			$STHQ1->execute($client_id,$tnl_id);
			if (($brand_id) = $STHQ1->fetchrow_array())
			{	
				# 
				# Check to see if campaign already exists for the slot
				#
				$sql="select campaign_id from camp_schedule_info where nl_id=$tnl_id and slot_id=$slot_id and slot_type='N' and schedule_date='$sdate' and client_id=$client_id";
				my $sthc=$dbhq->prepare($sql);
				$sthc->execute();
				my $old_camp_id;
				if (($old_camp_id) = $sthc->fetchrow_array())
				{
					$sthc->finish();
					$sql="update campaign set advertiser_id=$adv_id,campaign_name='$deploy_name' where campaign_id=$old_camp_id";
					$rows=$dbhu->do($sql);
				}
				else
				{
					$sthc->finish();
					$sql = "insert into campaign(campaign_name,status,created_datetime,scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type) values('$deploy_name','C',now(),'$sdate1','$sdate1',$adv_id,$cpid,$brand_id,date('$sdate1'),time('$sdate1'),'NEWSLETTER')";
					$rows=$dbhu->do($sql);
					$sql = "select max(campaign_id) from campaign where campaign_name='$deploy_name' and scheduled_date=date('$sdate1') and scheduled_time=time('$sdate1') and advertiser_id=$adv_id and profile_id=$cpid and brand_id=$brand_id";
					$sth = $dbhq->prepare($sql);
					$sth->execute();
					($camp_id) = $sth->fetchrow_array();
					$sth->finish();
				   	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($camp_id,'$sdate',$client_id)";
				   	$rows=$dbhu->do($sql);
					$sql="insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id,nl_id) values($client_id,$slot_id,'N','$sdate',$camp_id,$tnl_id)";
					$rows=$dbhu->do($sql);
				}
			}
			$STHQ1->finish();
		}
		$STHQ->finish();
	}
	else
	{
    	my $company_name;
        $sql="select company from user where user_id=$client_id";
        $sth = $dbhq->prepare($sql) ;
        $sth->execute();
        ($company_name) = $sth->fetchrow_array();
        $sth->finish();
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Campaign Excluded <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: schedule\@zetainteractive.com\n";
        print MAIL "Subject: Campaign Excluded for $cname\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "Advertiser: $cname Campaign: $cname excluded for $company_name\n";
        close MAIL;
	}
}
}
#
#	Handle batch delete logic
#
else
{
	my @chkboxs= $query->param('chkbox');
	my $tnl_id;
	$client_id=0;
	foreach my $chkbox (@chkboxs)
	{
   		($tnl_id,$slot_id,$cday) = split('_',$chkbox);
        $sql = "select date_add('$startdate',interval $cday day)";
        $sth = $dbhq->prepare($sql);
        $sth->execute();
        ($sdate) = $sth->fetchrow_array();
        $sth->finish();
		$sql="select campaign_id from camp_schedule_info where slot_id=? and slot_type='N' and schedule_date=? and nl_id=?";
		my $sth1=$dbhu->prepare($sql);
		$sth1->execute($slot_id,$sdate,$tnl_id);
		my $other_cid;
		while (($other_cid)=$sth1->fetchrow_array())
		{
			$sql = "update campaign set deleted_date = now() where campaign_id = $other_cid";
			$rows = $dbhu->do($sql);
			$sql="delete from camp_schedule_info where campaign_id=$other_cid";
			$rows = $dbhu->do($sql);
		}
		$sth1->finish();
	}
}
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Weekly</title>
</head>
<body>
<center>
<h2>Schedule successfully Updated</h2>
<br>
end_of_html
print "<a href=\"/cgi-bin/newsletter_schedule.cgi?nl_id=$nl_id&startdate=$startdate&adv_id=$adv_id\">Newsletter Weekly Schedule</a>\n";
print<<"end_of_html";
<br>
<a href="/cgi-bin/mainmenu.cgi" target=_top>Home</a>
</body>
</html>
end_of_html
