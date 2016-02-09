#!/usr/bin/perl

# ******************************************************************************
# schedule_copy_save.cgi
#
# this page saves information about the copied schedule 
#
# History
# ******************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;
use thirdparty;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $rows;
my $dbh;
my $images = $util->get_images_url;
my $stime;
my $company;
my $network_id;
my $tparty;
my $vsgID;
my $STRONGMAIL_ID=10;
my $daycnt;
my $daycnt2;
my $daycnt1;
my $temp_date;
my $sdate=$query->param('sdate');
my $edate=$query->param('edate');
my $tdate=$query->param('tdate');
my $stype=$query->param('stype');
if ($stype eq "")
{
	$stype="C";
}
my $camp_id;
my $exclude_from_brands_w_articles;
my $brand_id;
my $new_camp_id;
my $slot_id;
my $cname;
my $creative1;
my $aid;
my $catid;
my $aname;
my $client_id;
my $subject1;
my $from1;
my $from_addr;
my $send_email;
my $sdate1;
my ($supp_name,$last_updated,$filedate);
my $suppid;
my $pid;
my $bid;
my $mta_id;
my $log_camp;
$mta_id=1;

# connect to the util database
###$util->db_connect();

my ($dbhq,$dbhu)=$util->get_dbh();
###$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0) 
{
    print "Location: notloggedin.cgi\n\n";
	$util->clean_up();
    exit(0);
}
open (LOG,">/tmp/copy.log");
#
# Check to make sure all dates entered
#
if (($sdate eq "") || ($edate eq "") || ($tdate eq ""))
{
	display_error("One or more dates are blank.  All dates must be entered");
	exit(0);
}
$sql = "select datediff('$edate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($daycnt < 0)
{
	display_error("End date must be later than Start date.");
	exit(0);
}
$sql = "select datediff('$tdate',curdate())";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($daycnt < 0)
{
	display_error("To date must be later than current date.");
	exit(0);
}
#
$sql="select '$tdate' between '$sdate' and '$edate'";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
if ($daycnt == 1)
{
	display_error("To date cannot be between start and end date.");
	exit(0);
}
$sql = "select datediff('$edate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt) = $sth->fetchrow_array();
$sth->finish();
$sql = "select datediff('$tdate','$sdate')";
$sth = $dbhq->prepare($sql);
$sth->execute();
($daycnt1) = $sth->fetchrow_array();
$sth->finish();
print LOG "Date diff $daycnt - $daycnt1\n";
#
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head><title>Copy Success Page</title></head>
<body>
<center>
end_of_html
my @networks = $query->param('network');
foreach my $network (@networks) 
{
#
#	Delete all records for this network for specified time
#
	$sql="select campaign_id,slot_id,schedule_date from camp_schedule_info where client_id=$network and slot_type='$stype' and schedule_date >= '$tdate' and schedule_date <= date_add('$tdate',interval $daycnt day) and status='A'";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
print LOG "<$sql>\n";
	while (($camp_id,$slot_id,$temp_date) = $sth->fetchrow_array())
	{
		$sql = "update campaign set deleted_date=now() where campaign_id=$camp_id and status='S' and deleted_date is null";
		$rows = $dbhu->do($sql);
		$sql = "update camp_schedule_info set status='D' where client_id=$network and campaign_id=$camp_id and slot_id=$slot_id and schedule_date='$temp_date'";
		$rows = $dbhu->do($sql);
	}
	$sth->finish();
#
	$sql="select camp_schedule_info.campaign_id,camp_schedule_info.slot_id,schedule_date,advertiser_info.advertiser_id,advertiser_info.category_id,advertiser_info.advertiser_name,camp_schedule_info.client_id,hour(schedule_info.schedule_time),advertiser_info.exclude_from_brands_w_articles,campaign.brand_id from camp_schedule_info,campaign,advertiser_info,schedule_info where camp_schedule_info.client_id=$network and camp_schedule_info.slot_type='$stype' and schedule_date >= '$sdate' and schedule_date <= '$edate' and camp_schedule_info.slot_id=schedule_info.slot_id and schedule_info.client_id=$network and schedule_info.slot_type='$stype' and schedule_info.status='A' and camp_schedule_info.campaign_id=campaign.campaign_id and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.status='A' and deleted_date is null and camp_schedule_info.status='A'"; 
	$sth = $dbhq->prepare($sql);
	$sth->execute();
print LOG "<$sql>\n";
	while (($camp_id,$slot_id,$temp_date,$aid,$catid,$aname,$client_id,$stime,$exclude_from_brands_w_articles,$brand_id) = $sth->fetchrow_array())
	{
		#
		# Check for exclusions
		#
        my $sth1a;
        my $reccnt;
        $sql = "select count(*) from client_category_exclusion,client_advertiser_exclusion where (client_category_exclusion.client_id=? and client_category_exclusion.category_id=?) or (client_advertiser_exclusion.client_id=? and client_advertiser_exclusion.advertiser_id=?)";
        $sth1a = $dbhq->prepare($sql);
        $sth1a->execute($client_id,$catid,$client_id,$aid);
        ($reccnt) = $sth1a->fetchrow_array();
        $sth1a->finish();
        #
        if ($reccnt == 0)
        {
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
			$sql = "insert into campaign(user_id,max_emails,advertiser_id,id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,server_id,trigger_creative,last60_flag,aol_flag,yahoo_flag,hotmail_flag,other_flag,open_flag,list_cnt,open_category_id,disable_flag,profile_id,brand_id,campaign_name,status,scheduled_datetime,scheduled_date,scheduled_time,campaign_type) select user_id,max_emails,campaign.advertiser_id,id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,1,trigger_creative,last60_flag,aol_flag,yahoo_flag,hotmail_flag,other_flag,open_flag,list_cnt,open_category_id,'N',profile_id,brand_id,campaign_name,'S',date_add(scheduled_datetime,interval $daycnt1 day),date(date_add(scheduled_datetime,interval $daycnt1 day)),time(date_add(scheduled_datetime,interval $daycnt1 day)),campaign_type from campaign,advertiser_info where campaign_id=$camp_id and deleted_date is null and campaign.advertiser_id=advertiser_info.advertiser_id and advertiser_info.status='A'"; 
print LOG "<$sql>\n";
			$rows = $dbhu->do($sql);
#
			$sql = "select max(campaign_id) from campaign where campaign_name=(select campaign_name from campaign where campaign_id=$camp_id)"; 
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($new_camp_id) = $sth1->fetchrow_array();
			$sth1->finish();
print LOG "New camp id - <$new_camp_id>\n";
#
#		Get the brand and profile id from the schedule_info table
#
			$sql = "select profile_id,brand_id,third_party_id,vsgID from schedule_info where client_id=$network and slot_id=$slot_id and slot_type='$stype'"; 
			$sth1 = $dbhq->prepare($sql);
			$sth1->execute();
			($pid,$bid,$tparty,$vsgID) = $sth1->fetchrow_array();
			$sth1->finish();
			if (($tparty == 0) && ($stype eq "W"))
			{
				$sql = "select third_party_id from list_profile where profile_id=$pid";
				$sth1 = $dbhq->prepare($sql);
				$sth1->execute();
				($tparty) = $sth1->fetchrow_array();
				$sth1->finish();
			}
			if ($tparty == $STRONGMAIL_ID)
			{
				$sql = "update campaign set status='C',sent_datetime=scheduled_date,profile_id=$pid,brand_id=$bid where campaign_id=$new_camp_id";
			}
			else
			{ 
				$sql = "update campaign set profile_id=$pid,brand_id=$bid where campaign_id=$new_camp_id";
			}
			$rows = $dbhu->do($sql);
			if ($tparty == $STRONGMAIL_ID)
			{
	    		if ($stime == 12)
	    		{
	        		$stime = $stime - 12;
	    		}
	    		elsif ($stime == 24)
	    		{
	        		$stime = $stime - 12;
	    		}
	        	$sql="insert into campaign_log(campaign_id,date_sent,user_id) values($new_camp_id,date_add('$temp_date',interval $daycnt1 day),$network)";
	        	$rows=$dbhu->do($sql);
				$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=$new_camp_id";
				$sth1 = $dbhq->prepare($sql);
				$sth1->execute();
				($cname,$sdate1) = $sth1->fetchrow_array();
				$sth1->finish();
				$sql = "insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id) values($network,$slot_id,'$stype',date_add('$temp_date',interval $daycnt1 day),$new_camp_id)";
				$rows = $dbhu->do($sql);
				thirdparty::deploy_it($dbhq,$tparty,$new_camp_id,$bid,$aid,$network);
			}
#
#   Check to see if advertiser rotation setup
#
	    	$sql = "select creative1_id,subject1,from1 from advertiser_setup where advertiser_id=(select advertiser_id from campaign where campaign_id=$new_camp_id)";
	    	$sth1 = $dbhq->prepare($sql);
	    	$sth1->execute();
	    	$send_email = 0;
	    	if (($creative1,$subject1,$from1) = $sth1->fetchrow_array())
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
	    	$sth1->finish();

		    if ($send_email == 1)
		    {
		    	$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=$new_camp_id";
		    	$sth1 = $dbhq->prepare($sql);
		    	$sth1->execute();
				($cname,$sdate1) = $sth1->fetchrow_array();
				$sth1->finish();
		        open (MAIL,"| /usr/sbin/sendmail -t");
		        $from_addr = "No Creative Rotation <info\@zetainteractive.com>";
		        print MAIL "From: $from_addr\n";
		        print MAIL "To: setup\@zetainteractive.com\n";
		        print MAIL "Subject: Creative Rotation Missing\n";
		        my $date_str = $util->date(6,6);
		        print MAIL "Date: $date_str\n";
		        print MAIL "X-Priority: 1\n";
		        print MAIL "X-MSMail-Priority: High\n";
		        print MAIL "Need Creative Rotation for $cname - scheduled for $sdate1\n"
		;
		        close MAIL;
		    }
		    	$sql = "select vendor_supp_list_id from advertiser_info where advertiser_id=(select advertiser_id from campaign where campaign_id=$new_camp_id)";
		    	$sth1 = $dbhq->prepare($sql);
		    	$sth1->execute();
				($suppid) = $sth1->fetchrow_array();
				$sth1->finish();
#
# Check suppression for advertiser
#
$sql = "select list_name,last_updated,filedate,datediff(curdate(),last_updated) from vendor_supp_list_info where list_id=$suppid";
$sth1 = $dbhq->prepare($sql);
$sth1->execute();
($supp_name,$last_updated,$filedate,$daycnt2) = $sth1->fetchrow_array();
$sth1->finish();
if ($supp_name ne "NONE")
{
	if ($filedate eq "")
    {
    	if ($daycnt2 > 7)
        {
	    	$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=$new_camp_id";
	    	$sth1 = $dbhq->prepare($sql);
	    	$sth1->execute();
			($cname,$sdate1) = $sth1->fetchrow_array();
			$sth1->finish();
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "Out of Date Suppression <info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: $cname has a suppression file from $last_updated\n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
    	if ($daycnt2 > 10)
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
        $sth1 = $dbhq->prepare($sql) ;
        $sth1->execute();
        ($daycnt2) = $sth1->fetchrow_array();
        $sth1->finish();
        if ($daycnt2 > 7)
        {
	    	$sql = "select campaign_name,scheduled_datetime from campaign where campaign_id=$new_camp_id";
	    	$sth1 = $dbhq->prepare($sql);
	    	$sth1->execute();
			($cname,$sdate1) = $sth1->fetchrow_array();
			$sth1->finish();
    		open (MAIL,"| /usr/sbin/sendmail -t");
    		$from_addr = "Out of Date Suppression <info\@zetainteractive.com>";
    		print MAIL "From: $from_addr\n";
    		print MAIL "To: setup\@zetainteractive.com\n";
    		print MAIL "Subject: $cname has a suppression file from $filedate\n"; 
    		my $date_str = $util->date(6,6);
    		print MAIL "Date: $date_str\n";
        if ($daycnt2 > 10)
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
    		print MAIL "Suppression Id is $suppid\n"; 
    		print MAIL "Message from schedule_copy_save.cgi\n"; 
    		close MAIL;
}
		if ($tparty != $STRONGMAIL_ID)
		{
			$sql = "insert into camp_schedule_info(client_id,slot_id,slot_type,schedule_date,campaign_id) values($network,$slot_id,'$stype',date_add('$temp_date',interval $daycnt1 day),$new_camp_id)";
			$rows = $dbhu->do($sql);
		}
		}
	}
	else
	{
    	my $company_name;
        $sql="select company from user where user_id=$client_id";
        $sth1 = $dbhq->prepare($sql) ;
        $sth1->execute();
        ($company_name) = $sth1->fetchrow_array();
        $sth1->finish();
    	$sql = "select campaign_name from campaign where campaign_id=$camp_id";
    	$sth1 = $dbhq->prepare($sql);
    	$sth1->execute();
		($cname) = $sth1->fetchrow_array();
		$sth1->finish();
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Campaign Excluded <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: schedule\@zetainteractive.com\n";
        print MAIL "Subject: Campaign Excluded for $aname\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "Advertiser: $aname Campaign: $cname excluded for $company_name\n";
        close MAIL;
	}
	}
	$sql = "select company from user where user_id=$network"; 
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	($company) = $sth1->fetchrow_array();
	$sth1->finish();
	print "Successfully Copied $company<br>\n";
	$sth->finish();
}
print "<br><a href=\"/cgi-bin/mainmenu.cgi\"><img src=\"/images/home.gif\" border=0></a>\n";
print "</body></html>\n";
$util->clean_up();
close(LOG);
exit(0);

sub display_error
{
	my ($mesg) = @_ ;
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head><title>Error</title></head>
<body>
<center>
<h3>$mesg</h3>
</center>
</body>
</html>
end_of_html
}
