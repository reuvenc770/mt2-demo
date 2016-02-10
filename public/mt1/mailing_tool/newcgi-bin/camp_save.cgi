#!/usr/bin/perl

# *****************************************************************************************
# camp_save.cgi
#
# this page saves or updates the campaign
#
# History
# Jim Sobeck	02/03/2005	Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sth1;
my $sql;
my $dbh;
my $list_id;
my $iopt;
my $email_addr;
my $rows;
my $aname;
my $errmsg;
my $campaign_id;
my $old_campaign_id;
my $id;
my $campaign_name;
my $sdate;
my $shour;
my $subject;
my $other_addr;
my $from_addr;
my ($vendor_supp_list_id,$vendor_domain_supp_list_id,$category_id,$open_category_id,$content_id);
my $image_url;
my $title;
my $subtitle;
my $date_str;
my $greeting;
my $introduction;
my $k;
my $cname;
my $status;
my $num_articles;
my %checked = ( 'on' => 'Y', '' => 'N' );
my $list_cnt;
my $BASE_DIR;
my $aid;
my $temp_status;
my $catid;
my $userid;

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

$old_campaign_id = $query->param('old_campaign_id');
$campaign_name = $query->param('campaign_name');
$sdate = $query->param('sdate');

	#
	# check to see if advertiser or category is excluded
	#
	$sql="select user_id,campaign.advertiser_id,advertiser_info.category_id,advertiser_name from campaign,list_profile,advertiser_info where campaign_id=$old_campaign_id and campaign.advertiser_id=advertiser_info.advertiser_id and campaign.profile_id=list_profile.profile_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($userid,$aid,$catid,$aname) = $sth->fetchrow_array();
	$sth->finish();

    my $reccnt;
	my $sth1a;
    $sql = "select count(* ) from client_category_exclusion,client_advertiser_exclusion where (client_category_exclusion.client_id=? and client_category_exclusion.category_id=?) or (client_advertiser_exclusion.client_id=? and client_advertiser_exclusion.advertiser_id=?)";
    $sth1a = $dbhq->prepare($sql);
    $sth1a->execute($userid,$catid,$userid,$aid);
    ($reccnt) = $sth1a->fetchrow_array();
    $sth1a->finish();

	if ($reccnt == 0)
	{
	my $campaign_name1 = $dbhq->quote($campaign_name);
	# add the campaign to the database now with status of "Draft"
	$sql = "insert into campaign(user_id,max_emails,advertiser_id,id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,server_id,trigger_creative,last60_flag,aol_flag,yahoo_flag,hotmail_flag,other_flag,open_flag,list_cnt,open_category_id,disable_flag,profile_id,brand_id,campaign_type) select user_id,max_emails,advertiser_id,id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,1,trigger_creative,last60_flag,aol_flag,yahoo_flag,hotmail_flag,other_flag,open_flag,list_cnt,open_category_id,'N',profile_id,brand_id,campaign_type from campaign where campaign_id=$old_campaign_id"; 
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Inserting campaign record $sql : $errmsg");
		exit(0);
	}

	# get the campaign id just inserted
	$sql = "select max(campaign_id) from campaign";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	$campaign_id = $sth->fetchrow_array();
	$sth->finish();

	if ($sdate ne "")
	{
		$sql = "select hour(scheduled_datetime) from campaign where campaign_id=$old_campaign_id";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		$shour= $sth->fetchrow_array();
		$sth->finish();
		if (($shour eq "") || ($shour eq "0"))
		{
			$shour = "07";
		}

		$sql = "select date_add(curdate(),interval $sdate day)";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		$sdate= $sth->fetchrow_array();
		$sth->finish();
		$sdate = $sdate . " " . $shour . ":00";
		$sql="update campaign set scheduled_datetime='$sdate',scheduled_date=date('$sdate'),scheduled_time=time('$sdate') where campaign_id=$campaign_id";
		$rows = $dbhu->do($sql);
	}
	$sql = "select advertiser_id from campaign where campaign_id=$campaign_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	$aid= $sth->fetchrow_array();
	$sth->finish();
	$sql = "select status from campaign where campaign_id=$old_campaign_id";
	$sth = $dbhq->prepare($sql);
	$sth->execute();
	($temp_status)= $sth->fetchrow_array();
	$sth->finish();
	if ($temp_status ne "W")
	{
		$temp_status = "D";
	}
	$sql = "update campaign set campaign_name=$campaign_name1,created_datetime=now(),status='$temp_status',unsubscribe_cnt=0,fullmbx_cnt=0,notdelivered_cnt=0 where campaign_id = $campaign_id";
	$dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Updating campaign for Copy: $sql : $errmsg");
		exit(0);
	}
# go to the next page
if ($temp_status ne "W")
{
	print "Location: show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U\n\n";
}
else
{
	print "Location: show_campaign.cgi?campaign_id=$campaign_id&aid=$aid&mode=U&daily_flag=Y\n\n";
}
	}
	else
	{
    	my $company_name;
        $sql="select company from user where user_id=$userid";
        $sth = $dbhq->prepare($sql) ;
        $sth->execute();
        ($company_name) = $sth->fetchrow_array();
        $sth->finish();
        open (MAIL,"| /usr/sbin/sendmail -t");
        my $from_addr = "Campaign Excluded <info\@zetainteractive.com>";
        print MAIL "From: $from_addr\n";
        print MAIL "To: schedule\@zetainteractive.com\n";
        print MAIL "Subject: Campaign Excluded for $aname\n";
        my $date_str = $util->date(6,6);
        print MAIL "Date: $date_str\n";
        print MAIL "X-Priority: 1\n";
        print MAIL "X-MSMail-Priority: High\n";
        print MAIL "Advertiser: $aname Campaign: $campaign_name excluded for $company_name\n";
        close MAIL;
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
<html><head><title>ERROR</title></head>
<body>
<center><h3>Error: Campaign not copied - Advertiser $aname excluded for this client</h3>
</center></body></html>
end_of_html
	}
$util->clean_up();
exit(0);
