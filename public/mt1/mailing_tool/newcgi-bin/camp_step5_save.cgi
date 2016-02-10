#!/usr/bin/perl

# *****************************************************************************************
# camp_step5_save.cgi
#
# this page is the save screen from the campaign introduction
#
# History
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
my $sth2;
my $html_template;
my @class;
my $temp_id;
my $company;
my $linkcnt;
my $sql;
my $dbh;
my $lists;
my @list_ids;
my $userid;
my $email_addr;
my $rows;
my $errmsg;
my $campaign_id;
my $status;
my $template_id;
my $user_id;
my $subject;
my $creative2_id;
my $creative3_id;
my $creative4_id;
my $creative5_id;
my $creative6_id;
my $creative7_id;
my $creative8_id;
my $creative9_id;
my $creative10_id;
my $creative11_id;
my $creative12_id;
my $creative13_id;
my $creative14_id;
my $creative15_id;
my $subject1=0;
my $subject2=0;
my $subject3=0;
my $subject4=0;
my $subject5=0;
my $subject6=0;
my $subject7=0;
my $subject8=0;
my $subject9=0;
my $subject10=0;
my $subject11=0;
my $subject12=0;
my $subject13=0;
my $subject14=0;
my $subject15=0;
my $from1=0;
my $from2=0;
my $from3=0;
my $from4=0;
my $from5=0;
my $from6=0;
my $from7=0;
my $from8=0;
my $from9=0;
my $from10=0;
my $footer_color;
my $tid;
my $aid;
my $suppid;
my $domain_suppid;
my $internal_flag;
my $uns_flag;
my $redir_flag;
my $unsub_url;
my $image_url;
my $title;
my $subtitle;
my $date_str;
my $greeting;
my $introduction;
my $closing;
my $show_ad_top;
my $show_ad_bottom;
my $top_ad_opt;
my $top_ad_code;
my $bottom_ad_opt;
my $bottom_ad_code;
my $tell_a_friend;
my $BASE_DIR;
my $catid;
my $content_id;
my $subdomain_name;
my $trigger_email;
my $trigger_email_campaign_id;
my $revenue;
my $cstatus;
$cstatus='D';

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

# get the fields from the form 

$campaign_id = $query->param('cid');
my $campaign_name = $query->param('campaign_name');
my $tid0 = $query->param('tid0');
my $daily_flag = $query->param('daily_flag');
my $cnetwork = $query->param('cnetwork');
my $cday = $query->param('cday');
my $tid1 = $query->param('tid1');
my $am_pm = $query->param('am_pm');
my $profile_id = $query->param('profile_id');
my $brand_id = $query->param('brand_id');
my $nextfunc = $query->param('nextfunc');
if ($profile_id eq "")
{
	$profile_id = 0;
}
if ($brand_id eq "")
{
	$brand_id = 0;
}
my $sdate;
if ($daily_flag ne "Y")
{
if ($tid0 eq "")
{
	$sdate="";
}
else
{
		$sdate="";
	    my @date_parts = split(/\//,$tid0);
		my $day_cnt;
        my $date_str = $date_parts[2] . "-" . $date_parts[0] . "-" . $date_parts[1];
        $sql = "select to_days('$date_str')-to_days(curdate())";
        $sth = $dbhq->prepare($sql);
        $sth->execute();
        ($day_cnt) = $sth->fetchrow_array();
        $sth->finish;
        if ($day_cnt >= 0)
        {
			$sdate = $date_str . " ";
			if ($am_pm eq "PM")
			{
				$tid1 = $tid1 + 12;
				if ($tid1 >= 24)
				{
					$tid1 = 12;
				}
			}
			elsif (($am_pm eq "AM") && ($tid1 == 12))
			{
				$tid1 = 0;
			}
			$sdate = $sdate . $tid1 . ":00";
			$cstatus='S';
		}
}
}
else
{
	if ($am_pm eq "PM")
	{
		$tid1 = $tid1 + 12;
		if ($tid1 >= 24)
		{
			$tid1 = 12;
		}
	}		
	elsif (($am_pm eq "AM") && ($tid1 == 12))
	{
		$tid1 = 0;
	}
	$sdate = "2005-01-01 " . $tid1 . ":00";
	$cstatus='W';
}
if ($daily_flag eq "Y")
{
	@class= $query->param('classid');
}
$status = $query->param('status');
my $creative1_id = $query->param('creative1');
if ($cstatus eq "W")
{
$creative2_id = 0; 
$creative3_id = 0; 
$creative4_id = 0; 
$creative5_id = 0; 
$creative6_id = 0; 
$creative7_id = 0; 
$creative8_id = 0; 
$creative9_id = 0; 
$creative10_id = 0;
$creative11_id = 0; 
$creative12_id = 0; 
$creative13_id = 0; 
$creative14_id = 0; 
$creative15_id = 0; 
}
else
{
$creative2_id = $query->param('creative2');
$creative3_id = $query->param('creative3');
$creative4_id = $query->param('creative4');
$creative5_id = $query->param('creative5');
$creative6_id = $query->param('creative6');
$creative7_id = $query->param('creative7');
$creative8_id = $query->param('creative8');
$creative9_id = $query->param('creative9');
$creative10_id = $query->param('creative10');
$creative11_id = $query->param('creative11');
$creative12_id = $query->param('creative12');
$creative13_id = $query->param('creative13');
$creative14_id = $query->param('creative14');
$creative15_id = $query->param('creative15');
$subject1 = $query->param('subject1');
$subject2 = $query->param('subject2');
$subject3 = $query->param('subject3');
$subject4 = $query->param('subject4');
$subject5 = $query->param('subject5');
$subject6 = $query->param('subject6');
$subject7 = $query->param('subject7');
$subject8 = $query->param('subject8');
$subject9 = $query->param('subject9');
$subject10 = $query->param('subject10');
$subject11 = $query->param('subject11');
$subject12 = $query->param('subject12');
$subject13 = $query->param('subject13');
$subject14 = $query->param('subject14');
$subject15 = $query->param('subject15');
$from1 = $query->param('from1');
$from2 = $query->param('from2');
$from3 = $query->param('from3');
$from4 = $query->param('from4');
$from5 = $query->param('from5');
$from6 = $query->param('from6');
$from7 = $query->param('from7');
$from8 = $query->param('from8');
$from9 = $query->param('from9');
$from10 = $query->param('from10');
}
$aid = $query->param('advertiser_id');
$content_id = $query->param('content_id');
$content_id=1;
my $trigger_creative = $query->param('trigger_creative');
if ($trigger_creative eq "")
{
	$trigger_creative=0;
}
if ($cstatus eq 'S') 
{
	if ($brand_id > 0)
	{
	my $cname;
		my $brand_name;
		my $sid;
	$sql = "select advertiser_info.category_id,category_name from advertiser_info,category_info where advertiser_id=$aid and advertiser_info.category_id=category_info.category_id";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($catid,$cname) = $sth->fetchrow_array();
    $sth->finish();

	$sql="select brand_name,brandsubdomain_info.subdomain_id,subdomain_name from category_brand_info,brandsubdomain_info,client_brand_info where client_brand_info.status='A' and category_id=$catid and brandsubdomain_info.subdomain_id=category_brand_info.subdomain_id and category_brand_info.brand_id=$brand_id and category_brand_info.brand_id=client_brand_info.brand_id";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    if (($brand_name,$sid,$subdomain_name) = $sth->fetchrow_array())
	{
    	$sth->finish();
	}
	else
	{
    	$sth->finish();
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
		<html><head><title>ERROR</title></head>
		<body>
		<h2>You must assign a Brand Subdomain for the brand for the specified advertiser category $cname</h2>
		<p>Your changes were not saved.  Use your browsers back button to fix the problem.</p>
		</body>
		</html>
end_of_html
		exit(0);
	}
	}

	if (($subject1 == 0) || ($from1 == 0) || ($creative1_id == 0))
	{
		my $reccnt;
		$sql="select count(*) from advertiser_setup where advertiser_id=$aid";
		$sth = $dbhq->prepare($sql);
		$sth->execute();
		($reccnt) = $sth->fetchrow_array();
		$sth->finish();
		if ($reccnt == 0)
		{
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
		<html><head><title>ERROR</title></head>
		<body>
		<h2>You must select a value for the First Subject, From Line, and Creative</h2>
		<p>Your changes were not saved.  Use your browsers back button to fix the problem.</p>
		</body>
		</html>
end_of_html
		exit(0);
		}
	}
    $sql = "select distinct user.user_id,company from list_profile,user where list_profile.client_id=user.user_id and list_profile.profile_id=$profile_id";
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute();
	while (($temp_id,$company) = $sth1->fetchrow_array())
	{
		$sql = "select count(*) from advertiser_tracking where client_id =$temp_id and advertiser_tracking.advertiser_id=$aid";
		if ($daily_flag eq "Y")
		{
			$sql = $sql . " and daily_deal='Y'";
		}
		else
		{
			$sql = $sql . " and daily_deal='N'";
		}
		$sth2 = $dbhq->prepare($sql);
		$sth2->execute();
		($linkcnt) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($linkcnt <= 0)
		{
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
		<html><head><title>ERROR</title></head>
		<body>
		<h2>No URL defines for $company for this advertiser.</h2>
		<p>Your changes were not saved.  Use your browsers back button to fix the problem.</p>
		</body>
		</html>
end_of_html
		exit(0);
		}
		#
		# Check to see if this deal scheduled for same client in last 3 days
		#
        $sql = "select count(*) from campaign where advertiser_id=$aid and campaign_id != $campaign_id and scheduled_date >= date_sub(curdate(),interval 3 day) and campaign_id in (select distinct campaign_id from campaign,list_profile where list_profile.profile_id=campaign.profile_id and client_id=$temp_id)";
		$sth2 = $dbhq->prepare($sql);
		$sth2->execute();
		($linkcnt) = $sth2->fetchrow_array();
		$sth2->finish();
		if ($linkcnt > 0)
		{
		print "Content-type: text/html\n\n";
		print<<"end_of_html";
		<html><head><title>ERROR</title></head>
		<body>
		<h2>This deal has been scheduled for $company in the last 3 days!</h2>
		<p>Your changes were saved.  If you wish to fix the problem, use your browsers BACK button.</p>
		<br>
		<center><a href="/cgi-bin/mainmenu.cgi"><img src="/images/home.gif" border=0></a></center>
		</body>
		</html>
end_of_html
		$nextfunc="";
		}
	}
	$sth1->finish();
}
if (($campaign_id eq "") or ($campaign_id == 0))
{
	# insert record into campaign
	$sql = "insert into campaign(campaign_name,status,advertiser_id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,server_id,trigger_creative,scheduled_datetime,user_id,created_datetime,profile_id,brand_id,scheduled_date,scheduled_time,campaign_type) values('$campaign_name','$cstatus',$aid,$creative1_id,$creative2_id,$creative3_id,$creative4_id,$creative5_id,$creative6_id,$creative7_id,$creative8_id,$creative9_id,$creative10_id,$creative11_id,$creative12_id,$creative13_id,$creative14_id,$creative15_id,$subject1,$subject2,$subject3,$subject4,$subject5,$subject6,$subject7,$subject8,$subject9,$subject10,$subject11,$subject12,$subject13,$subject14,$subject15,$from1,$from2,$from3,$from4,$from5,$from6,$from7,$from8,$from9,$from10,$content_id,$trigger_creative,'$sdate',$user_id,now(),$profile_id,$brand_id,date('$sdate'),time('$sdate'),'REGULAR')";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Error inserting campaign record $sql");
		exit(0);
	}
	#
	# Get Campaign_id from record just inserted
	#
	$sql="select max(campaign_id) from campaign where campaign_name='$campaign_name' and advertiser_id=$aid";
	$sth = $dbhu->prepare($sql);
	$sth->execute();
	($campaign_id) = $sth->fetchrow_array();
	$sth->finish();
	#
	# If daily_deal then add a record
	#
	if ($daily_flag eq "Y")
	{
		$sql="insert into daily_deals(campaign_id,client_id,cday) values($campaign_id,$cnetwork,$cday)";
		$rows = $dbhu->do($sql);
		$sql="update campaign set campaign_type='DAILY' where campaign_id=$campaign_id";
		$rows = $dbhu->do($sql);
		my $i=0;
		while ($i <= $#class)
		{
			$sql="insert into DailyIsp(campaign_id,class_id) values($campaign_id,$class[$i])";
			$rows = $dbhu->do($sql);
			$i++;
		}
	}
}
elsif ($status ne "C")
{
	# update this campaign's info

	$sql = "update campaign set campaign_name='$campaign_name',creative1_id=$creative1_id,creative2_id=$creative2_id,creative3_id=$creative3_id,creative4_id=$creative4_id,creative5_id=$creative5_id,creative6_id=$creative6_id,creative7_id=$creative7_id,creative8_id=$creative8_id,creative9_id=$creative9_id,creative10_id=$creative10_id,creative11_id=$creative11_id,creative12_id=$creative12_id,creative13_id=$creative13_id,creative14_id=$creative14_id,creative15_id=$creative15_id,subject1 = '$subject1', subject2='$subject2', subject3='$subject3', subject4='$subject4',subject5='$subject5',subject6='$subject6',subject7='$subject7',subject8='$subject8',subject9='$subject9',subject10='$subject10',subject11='$subject11',subject12='$subject12',subject13='$subject13',subject14='$subject14',subject15='$subject15',from1= '$from1',from2= '$from2',from3= '$from3',from4= '$from4',from5= '$from5',from6= '$from6',from7= '$from7',from8= '$from8',from9= '$from9',from10= '$from10',advertiser_id=$aid,server_id=$content_id,trigger_creative='$trigger_creative',scheduled_datetime='$sdate',status='$cstatus',profile_id=$profile_id,brand_id=$brand_id,scheduled_date=date('$sdate'),scheduled_time=time('$sdate') where campaign_id = $campaign_id";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Updating campaign record for $campaign_id: $sql");
		exit(0);
	}
	if ($daily_flag eq "Y")
	{
		$sql = "delete from daily_deals where campaign_id=$campaign_id"; 
		$rows = $dbhu->do($sql);
		$sql="insert into daily_deals(campaign_id,client_id,cday) values($campaign_id,$cnetwork,$cday)";
		$rows = $dbhu->do($sql);
		$sql="delete from DailyIsp where campaign_id=$campaign_id";
		$rows = $dbhu->do($sql);
		my $i=0;
		while ($i <= $#class)
		{
			$sql="insert into DailyIsp(campaign_id,class_id) values($campaign_id,$class[$i])";
			$rows = $dbhu->do($sql);
			$i++;
		}
	}
}

# figure out which button was clicked, and go to the appropriate screen

if ($nextfunc eq "list")
{
	print "Location: camp_edit_lists.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "save")
{
	print "Location: show_campaign.cgi?aid=$aid&campaign_id=$campaign_id&mode=U&daily_flag=$daily_flag\n\n";
}
elsif ($nextfunc eq "test")
{
	print "Location: camp_test.cgi?campaign_id=$campaign_id\n\n";
}
elsif ($nextfunc eq "exit")
{
	print "Location: mainmenu.cgi\n\n";
}
elsif ($nextfunc eq "")
{
}
else
{
	print "Content-type: text/html\n\n";
	print "<html><body>Unknown function <br> nextfunc=$nextfunc</body></html>\n";
}

# exit function

$util->clean_up();
exit(0);
