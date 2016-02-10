#!/usr/bin/perl

# *****************************************************************************************
# advertiser_setup_save.cgi
#
# this page is the save screen from the advertiser setup page 
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
my $html_template;
my $sql;
my $dbh;
my $lists;
my $cid;
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
my $subject2;
my $subject3;
my $subject4;
my $subject5;
my $subject6;
my $subject7;
my $subject8;
my $subject9;
my $subject10;
my $subject11;
my $subject12;
my $subject13;
my $subject14;
my $subject15;
my $subject16;
my $subject17;
my $subject18;
my $subject19;
my $subject20;
my $subject21;
my $subject22;
my $subject23;
my $subject24;
my $subject25;
my $subject26;
my $subject27;
my $subject28;
my $subject29;
my $subject30;
my $from1;
my $from2;
my $from3;
my $from4;
my $from5;
my $from6;
my $from7;
my $from8;
my $from9;
my $from10;
my $from11;
my $from12;
my $from13;
my $from14;
my $from15;
my $from16;
my $from17;
my $from18;
my $from19;
my $from20;
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

$aid = $query->param('aid');
my $creative1_id = $query->param('creative1');
my $creative2_id = $query->param('creative2');
my $creative3_id = $query->param('creative3');
my $creative4_id = $query->param('creative4');
my $creative5_id = $query->param('creative5');
my $creative6_id = $query->param('creative6');
my $creative7_id = $query->param('creative7');
my $creative8_id = $query->param('creative8');
my $creative9_id = $query->param('creative9');
my $creative10_id = $query->param('creative10');
my $creative11_id = $query->param('creative11');
my $creative12_id = $query->param('creative12');
my $creative13_id = $query->param('creative13');
my $creative14_id = $query->param('creative14');
my $creative15_id = $query->param('creative15');
my $subject1 = $query->param('subject1');
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
$subject16 = $query->param('subject16');
$subject17 = $query->param('subject17');
$subject18 = $query->param('subject18');
$subject19 = $query->param('subject19');
$subject20 = $query->param('subject20');
$subject21 = $query->param('subject21');
$subject22 = $query->param('subject22');
$subject23 = $query->param('subject23');
$subject24 = $query->param('subject24');
$subject25 = $query->param('subject25');
$subject26 = $query->param('subject26');
$subject27 = $query->param('subject27');
$subject28 = $query->param('subject28');
$subject29 = $query->param('subject29');
$subject30 = $query->param('subject30');
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
$from11 = $query->param('from11');
$from12 = $query->param('from12');
$from13 = $query->param('from13');
$from14 = $query->param('from14');
$from15 = $query->param('from15');
$from16 = $query->param('from16');
$from17 = $query->param('from17');
$from18 = $query->param('from18');
$from19 = $query->param('from19');
$from20 = $query->param('from20');
my $trigger_creative = $query->param('trigger_creative');
my $trigger_creative2 = $query->param('trigger_creative2');
my $aname;
my $cname;
my $temp_aid;
if ($trigger_creative eq "")
{
	$trigger_creative=0;
}
if ($trigger_creative2 eq "")
{
	$trigger_creative2=0;
}
if (($subject1 == 0) || ($from1 == 0) || ($creative1_id == 0))
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
$sql="delete from advertiser_setup where advertiser_id=$aid";
$rows = $dbhu->do($sql);
#
	$sql = "insert into advertiser_setup(advertiser_id,creative1_id,creative2_id,creative3_id,creative4_id,creative5_id,creative6_id,creative7_id,creative8_id,creative9_id,creative10_id,creative11_id,creative12_id,creative13_id,creative14_id,creative15_id,subject1,subject2,subject3,subject4,subject5,subject6,subject7,subject8,subject9,subject10,subject11,subject12,subject13,subject14,subject15,subject16,subject17,subject18,subject19,subject20,subject21,subject22,subject23,subject24,subject25,subject26,subject27,subject28,subject29,subject30,from1,from2,from3,from4,from5,from6,from7,from8,from9,from10,from11,from12,from13,from14,from15,from16,from17,from18,from19,from20,trigger_creative,trigger_creative2,date_modified) values($aid,$creative1_id,$creative2_id,$creative3_id,$creative4_id,$creative5_id,$creative6_id,$creative7_id,$creative8_id,$creative9_id,$creative10_id,$creative11_id,$creative12_id,$creative13_id,$creative14_id,$creative15_id,$subject1,$subject2,$subject3,$subject4,$subject5,$subject6,$subject7,$subject8,$subject9,$subject10,$subject11,$subject12,$subject13,$subject14,$subject15,$subject16,$subject17,$subject18,$subject19,$subject20,$subject21,$subject22,$subject23,$subject24,$subject25,$subject26,$subject27,$subject28,$subject29,$subject30,$from1,$from2,$from3,$from4,$from5,$from6,$from7,$from8,$from9,$from10,$from11,$from12,$from13,$from14,$from15,$from16,$from17,$from18,$from19,$from20,$trigger_creative,$trigger_creative2,now())";
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Error inserting advertiser_setup record $sql");
		exit(0);
	}
#
# Check to see to setup campaign record for trigger emails
#
if ($trigger_creative > 0)
{
	$sql = "select advertiser_name,creative_name,creative.advertiser_id from creative,advertiser_info where trigger_flag='Y' and creative.advertiser_id=advertiser_info.advertiser_id and creative_id=$trigger_creative";
        $sth = $dbhq->prepare($sql);
        $sth->execute();
        ($aname,$cname,$temp_aid) = $sth->fetchrow_array();
        $sth->finish();
#
	my $temp_str = $aname . " " . $cname;
	$sql="select campaign_id from campaign where campaign_name='$temp_str' and status='T'";
        $sth = $dbhq->prepare($sql);
        $sth->execute();
        if (($cid) = $sth->fetchrow_array())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,campaign_type) values('$temp_str',1,'T',now(),$temp_aid,$trigger_creative,'TRIGGER')";
		$rows=$dbhu->do($sql);
	}
}
if ($trigger_creative2 > 0)
{
	$sql = "select advertiser_name,creative_name,creative.advertiser_id from creative,advertiser_info where trigger_flag='Y' and creative.advertiser_id=advertiser_info.advertiser_id and creative_id=$trigger_creative2";
        $sth = $dbhq->prepare($sql);
        $sth->execute();
        ($aname,$cname,$temp_aid) = $sth->fetchrow_array();
        $sth->finish();
#
	my $temp_str = $aname . " " . $cname;
	$sql="select campaign_id from campaign where campaign_name='$temp_str' and status='T'";
        $sth = $dbhq->prepare($sql);
        $sth->execute();
        if (($cid) = $sth->fetchrow_array())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,campaign_type) values('$temp_str',1,'T',now(),$temp_aid,$trigger_creative2,'TRIGGER')";
		$rows=$dbhu->do($sql);
	}
}
my $pmesg="Advertiser Setup Information was updated.";
print "Location: /cgi-bin/advertiser_disp2.cgi?puserid=$aid&pmesg=$pmesg\n\n";

# exit function

$util->clean_up();
exit(0);
