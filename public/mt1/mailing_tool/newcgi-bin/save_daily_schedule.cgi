#!/usr/bin/perl
#===============================================================================
# Purpose: Bottom frame of daily_schedule.html page 
# Name   : save_daily_schedule.cgi 
#
#--Change Control---------------------------------------------------------------
# 02/27/09  Jim Sobeck  Creation
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
my $cday;
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
my $STRONGMAIL_ID=10;
my $exclude_from_brands_w_articles;
my $disp_msg;
my $usa_id= $query->param('usa_id');
my $nid= $query->param('nid');
my $delete=$query->param('Delete.x');
if ($delete eq "")
{
	$delete=0;
}
else
{
	$delete=1;
}
my $stype="D"; 
my $advertiser_name;
my $cname;
my $slot_id;
my $tday;
my $client_id;
my $stime;
my $priority;
my $log_camp;
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
my $mta_id;
my $exclude_days;
my $exflag;
$mta_id=1;
my $current_day;
my $current_hour;
my $creative_id;
my $from_id;
my $subject_id;
my $adv_id;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
$disp_msg="";
#
$sql="select dayofweek(curdate()),hour(curtime())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($current_day,$current_hour) = $sth->fetchrow_array();
$sth->finish();

$sql="select advertiser_id,creative_id,subject_id,from_id from UniqueScheduleAdvertiser where usa_id=?";
$sth=$dbhu->prepare($sql);
$sth->execute($usa_id);
($adv_id,$creative_id,$subject_id,$from_id)=$sth->fetchrow_array();
$sth->finish();

$sql = "select advertiser_name,vendor_supp_list_id,category_id,exclude_from_brands_w_articles,exclude_days from advertiser_info where advertiser_id=$adv_id"; 
$sth = $dbhq->prepare($sql);
$sth->execute();
($cname,$suppid,$catid,$exclude_from_brands_w_articles,$exclude_days) = $sth->fetchrow_array();
$sth->finish();
#
# Check suppression for advertiser
#
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
			if (($current_day == 6) and ($current_hour >= 17))
			{
				$disp_msg="Advertiser $cname has a suppression file older than 7 days.  Please consult with Neal, if you really need to schedule this";
			}
  		  	if ($daycnt > 10)
       		{
    			print MAIL "X-Priority: 1\n";
    			print MAIL "X-MSMail-Priority: High\n";
				if ($current_hour >= 17)
				{
					$disp_msg="Advertiser $cname has a suppression file older than 10 days.  Please consult with Neal, if you really need to schedule this";
				}
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
my @chkboxs= $query->param('chkbox');
foreach my $chkbox (@chkboxs) 
{
   ($client_id,$slot_id,$cday) = split('_',$chkbox);
	my $sth1a;
	#
	# check to see if campaign already scheduled
	#
	$sql="select camp_schedule_info.campaign_id from camp_schedule_info,daily_deals where daily_deals.client_id=? and camp_schedule_info.slot_id=? and camp_schedule_info.slot_type=? and camp_schedule_info.campaign_id=daily_deals.campaign_id and camp_schedule_info.client_id=? and daily_deals.cday=? and camp_schedule_info.status='A'";
    $sth1a = $dbhq->prepare($sql);
    $sth1a->execute($client_id,$slot_id,$stype,$client_id,$cday);
	if (($camp_id)=$sth1a->fetchrow_array())
	{
		if ($delete)
		{
			$sql = "update campaign set deleted_date = now() where campaign_id = $camp_id";
			$rows = $dbhu->do($sql);
			$sql="update camp_schedule_info set status='D' where campaign_id=$camp_id";
			$rows = $dbhu->do($sql);
			$sql="delete from daily_deals where campaign_id=$camp_id";
			$rows = $dbhu->do($sql);
			$sql="delete from DailyIsp where campaign_id=$camp_id";
			$rows = $dbhu->do($sql);
		}
		else
		{
			$cname=~s/'/''/g;
			$sql="update campaign set campaign_name='$cname',advertiser_id=$adv_id,creative1_id=$creative_id,subject1=$subject_id,from1=$from_id where campaign_id=$camp_id";
			$rows=$dbhu->do($sql);
			$sql="update camp_schedule_info set usa_id=$usa_id where campaign_id=$camp_id and slot_type='$stype' and client_id=$client_id and slot_id=$slot_id";
			$rows=$dbhu->do($sql);
		}
		next;
	}
	if ($delete)
	{
		next;
	}
    my $reccnt;
    $sql = "select count(*) from client_category_exclusion,client_advertiser_exclusion where (client_category_exclusion.client_id=? and client_category_exclusion.category_id=?) or (client_advertiser_exclusion.client_id=? and client_advertiser_exclusion.advertiser_id=?)";
    $sth1a = $dbhq->prepare($sql);
    $sth1a->execute($client_id,$catid,$client_id,$adv_id);
    ($reccnt) = $sth1a->fetchrow_array();
    $sth1a->finish();
    #
    if ($reccnt == 0)
    {
		$sql = "select brand_id,third_party_id,mta_id,performance,log_campaign from schedule_info where client_id=$client_id and slot_id=$slot_id and slot_type='$stype'";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($brand_id,$third_id,$mta_id,$priority,$log_camp) = $sth->fetchrow_array();
		$sth->finish();
		if ($exclude_from_brands_w_articles eq "Y")
		{		
			$sql="select count(*) from brand_article ba, article a where ba.brand_id=? and ba.article_id=a.article_id and a.status='A'";
    		$sth1a = $dbhq->prepare($sql);
    		$sth1a->execute($brand_id);
    		($reccnt) = $sth1a->fetchrow_array();
    		$sth1a->finish();
		}
		if ($reccnt == 0)
		{
			$deploy_name=$cname;
			$deploy_name=~s/'/''/g;
			$sql = "insert into campaign(user_id,campaign_name,status,created_datetime,scheduled_datetime,sent_datetime,advertiser_id,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type,creative1_id,subject1,from1) values($client_id,'$deploy_name','W',now(),curdate(),curtime(),$adv_id,0,$brand_id,curdate(),curtime(),'DAILY',$creative_id,$subject_id,$from_id)";
			$rows=$dbhu->do($sql);
	#
	#	Get the campaign id and add to camp_schedule_info
	#
			$sql = "select max(campaign_id) from campaign where campaign_name='$deploy_name' and scheduled_date=curdate() and advertiser_id=$adv_id and profile_id=0 and campaign_type='DAILY' and brand_id=$brand_id";
			$sth = $dbhq->prepare($sql);
			$sth->execute();
			($camp_id) = $sth->fetchrow_array();
			$sth->finish();
			$sql="insert into daily_deals(campaign_id,client_id,cday) values($camp_id,$client_id,$cday)";
			$rows=$dbhu->do($sql);
			$sql="insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id,nl_id,usa_id) values($client_id,$slot_id,'$stype',curdate(),$camp_id,$cday,$usa_id)";
			$rows=$dbhu->do($sql);
		}
		else
		{
			$disp_msg="Can't schedule this offer to brands with articles";
		}
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
		$disp_msg="Advertiser ".$cname." excluded for ".$company_name;
	}
}
if ($disp_msg ne "")
{
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Error</title>
</head>
<body>
<center><h3>$disp_msg</h3></center>
</body>
</html>
end_of_html
exit;
}
else
{
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
	<a href="/daily_schedule.html" target=_top>Daily Deals Schedule</a>
<br>
<a href="/cgi-bin/mainmenu.cgi" target=_top>Home</a>
</body>
</html>
end_of_html
}
