#!/usr/bin/perl

# *****************************************************************************************
# conversion_category_trigger_save.cgi
#
# this page is the save screen from the conversion category Trigger page 
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
my $t1;
my $t2;
my $altt;
my $company;
$cstatus='D';

# connect to the util database
my ($dbhq,$dbhu)=$util->get_dbh();

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

# get the fields from the form 

$cid = $query->param('cid');
my $client_id = $query->param('client_id');
my $trigger_creative = $query->param('trigger_creative');
my $trigger_creative2 = $query->param('trigger_creative2');
my $aid1=$query->param('advertiser_id1');
my $aid2=$query->param('advertiser_id2');
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
if ($aid1 eq "")
{
	$aid1=0;
}
if ($aid2 eq "")
{
	$aid2=0;
}
$sql="delete from conversion_category_trigger where client_id=$client_id and category_id=$cid"; 
$rows = $dbhu->do($sql);
if ($client_id == 0)
{
	$sql="delete from conversion_category_trigger where client_id > 0 and category_id=$cid"; 
	$rows = $dbhu->do($sql);
}
#
	$sql = "insert into conversion_category_trigger(client_id,category_id,trigger1,trigger2,trigger1_aid,trigger2_aid) values($client_id,$cid,$trigger_creative,$trigger_creative2,$aid1,$aid2)"; 
	$rows = $dbhu->do($sql);
	if ($dbhu->err() != 0)
	{
		$errmsg = $dbhu->errstr();
		util::logerror("Error inserting conversion_category_trigger record $sql");
		exit(0);
	}
#
# Check to see to setup campaign record for trigger emails
#
if ($aid1 > 0)
{
	$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid1"; 
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($aname) = $sth->fetchrow_array();
    $sth->finish();
#
	my $temp_str = $aname." ".$cid;
	$sql="select campaign_id from campaign where campaign_name='$temp_str' and status='T' and deleted_date is null";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    if (($cid) = $sth->fetchrow_array())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,campaign_type) values('$temp_str',1,'T',now(),$aid1,$trigger_creative,'TRIGGER')";
		$rows=$dbhu->do($sql);
	}
}
if ($aid2 > 0)
{
	$sql = "select advertiser_name from advertiser_info where advertiser_id=$aid2"; 
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    ($aname) = $sth->fetchrow_array();
    $sth->finish();
#
	my $temp_str = $aname." ".$cid;
	$sql="select campaign_id from campaign where campaign_name='$temp_str' and status='T' and deleted_date is null";
    $sth = $dbhq->prepare($sql);
    $sth->execute();
    if (($cid) = $sth->fetchrow_array())
	{
		$sth->finish();
	}
	else
	{
		$sth->finish();
		$sql = "insert into campaign(campaign_name,user_id,status,created_datetime,advertiser_id,creative1_id,campaign_type) values('$temp_str',1,'T',now(),$aid2,$trigger_creative2,'TRIGGER')";
		$rows=$dbhu->do($sql);
	}
}
print "Content-type: text/html\n\n";
print<<"end_of_html";
<html>
<head></head>
<body>
<script Language=JavaScript>
document.location="/cgi-bin/conversion_category_trigger_list.cgi?userid=$client_id";
</script>
</body>
</html>
end_of_html

# exit function

$util->clean_up();
exit(0);
