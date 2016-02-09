#!/usr/bin/perl
# *****************************************************************************************
# list_save.cgi
#
# this page saves the list changes
#
# History
# Grady Nash, 8/30/01, Creation
# *****************************************************************************************

# include Perl Modules

use strict;
use CGI;
use util;

# get some objects to use later

my $util = util->new;
my $query = CGI->new;
my $sth;
my $sql;
my $dbh;
my $rows;
my $errmsg;
my $oldaid = $query->param('oldaid');
my $aname = $query->param('advertiser_name');
my $include_creative = $query->param('include_creative');
my $newaid;

# connect to the util database

$util->db_connect();
$dbh = $util->get_dbh;

# check for login

my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    $util->clean_up();
    exit(0);
}

if ($include_creative eq "Y")
{
	$sql = "insert into advertiser_info(advertiser_name,email_addr,internal_email_addr,physical_addr,status,offer_type,payout,vendor_supp_list_id,suppression_url,auto_download,suppression_username,suppression_password,category_id,unsub_image,unsub_link) select '$aname',email_addr,internal_email_addr,physical_addr,'A',offer_type,payout,vendor_supp_list_id,suppression_url,auto_download,suppression_username,suppression_password,category_id,unsub_image,unsub_link from advertiser_info where advertiser_id=$oldaid";
}
else
{
	$sql = "insert into advertiser_info(advertiser_name,email_addr,internal_email_addr,physical_addr,status,offer_type,vendor_supp_list_id) select '$aname',email_addr,internal_email_addr,physical_addr,'A',offer_type,1 from advertiser_info where advertiser_id=$oldaid";
}
$rows = $dbh->do($sql);
if ($dbh->err() != 0)
{
	$errmsg = $dbh->errstr();
	util::logerror("Updating advertiser_info $sql: $errmsg");
	exit(0);
}
$sql = "select max(advertiser_id) from advertiser_info where advertiser_name='$aname'";
$sth = $dbh->prepare($sql);
$sth->execute();
($newaid) = $sth->fetchrow_array();
$sth->finish();

$sql = "insert into advertiser_contact_info(advertiser_id,contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes) select $newaid,contact_name,contact_phone,contact_email,contact_company,contact_aim,contact_website,contact_username,contact_password,contact_notes from advertiser_contact_info where advertiser_id=$oldaid";
$rows = $dbh->do($sql);

$sql = "insert into advertiser_approval(advertiser_id,email_addr) select $newaid,email_addr from advertiser_approval where advertiser_id=$oldaid";
$rows = $dbh->do($sql);
$sql = "insert into advertiser_email(advertiser_id,email_addr) select $newaid,email_addr from advertiser_email where advertiser_id=$oldaid";
$rows = $dbh->do($sql);
$sql = "insert into advertiser_seedlist(advertiser_id,email_addr) select $newaid,email_addr from advertiser_seedlist where advertiser_id=$oldaid";
$rows = $dbh->do($sql);
#
if ($include_creative eq "Y")
{
	$sql = "insert into advertiser_from(advertiser_id,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by) select $newaid,advertiser_from,approved_flag,original_flag,status,date_approved,approved_by from advertiser_from where advertiser_id=$oldaid";
	$rows = $dbh->do($sql);
	$sql = "insert into advertiser_subject(advertiser_id,advertiser_subject,approved_flag,original_flag,status,date_approved,approved_by) select $newaid,advertiser_subject,approved_flag,original_flag,status,date_approved,approved_by from advertiser_subject where advertiser_id=$oldaid and advertiser_subject.status='A'";
	$rows = $dbh->do($sql);
	$sql = "insert into creative(advertiser_id,status,creative_name,original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,date_approved,approved_by) select $newaid,status,creative_name,original_flag,trigger_flag,approved_flag,creative_date,inactive_date,unsub_image,default_subject,default_from,image_directory,thumbnail,html_code,date_approved,approved_by from creative where advertiser_id=$oldaid";
	$rows = $dbh->do($sql);
	#
	# Get the default subject and from old advertiser
	#
	my ($sname,$cstatus,$date_approved,$approved_by,$cid);
	my $sid;
	my $sth1;
$|=1 ;
	open(LOG,">/tmp/chk.log");
	$sql = "select advertiser_subject,advertiser_subject.status,advertiser_subject.date_approved,advertiser_subject.approved_by,creative_id from advertiser_subject,creative where default_subject=subject_id and creative.advertiser_id=$newaid and advertiser_subject.status='A'";
	print LOG "Sql1 - <$sql>\n";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($sname,$cstatus,$date_approved,$approved_by,$cid) = $sth->fetchrow_array())
	{
		$sql = "select subject_id from advertiser_subject where advertiser_subject='$sname' and status='$cstatus' and advertiser_id=$newaid";
		print LOG "Sql2 - <$sql>\n";
		$sth1 = $dbh->prepare($sql);
		$sth1->execute();
		($sid) = $sth1->fetchrow_array();
		$sth1->finish();
		$sql = "update creative set default_subject=$sid where creative_id=$cid";
		print LOG "Sql3 - <$sql>\n";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
	$sql = "select advertiser_from,advertiser_from.status,advertiser_from.date_approved,advertiser_from.approved_by,creative_id from advertiser_from,creative where default_from=from_id and creative.advertiser_id=$newaid";
		print LOG "Sql4 - <$sql>\n";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	while (($sname,$cstatus,$date_approved,$approved_by,$cid) = $sth->fetchrow_array())
	{
		$sql = "select from_id from advertiser_from where advertiser_from='$sname' and status='$cstatus' and advertiser_id=$newaid";
		print LOG "Sql5 - <$sql>\n";
		$sth1 = $dbh->prepare($sql);
		$sth1->execute();
		($sid) = $sth1->fetchrow_array();
		$sth1->finish();
		$sql = "update creative set default_from=$sid where creative_id=$cid";
		print LOG "Sql6 - <$sql>\n";
		$rows = $dbh->do($sql);
	}
	$sth->finish();
	close(LOG);
#
	$sql = "insert into advertiser_tracking(advertiser_id,url,code,date_added,client_id,link_id,date_approved,approved_by,daily_deal) select $newaid,url,code,date_added,client_id,link_id,date_approved,approved_by,daily_deal from advertiser_tracking where advertiser_id=$oldaid";
	$rows = $dbh->do($sql);
   $sql = "update advertiser_info set url_count=(select count(*) from advertiser_tracking where advertiser_tracking.advertiser_id=advertiser_info.advertiser_id and advertiser_tracking.advertiser_id=#newaid)";
   $rows = $dbh->do($sql);
}
else
{
	$sql = "insert into advertiser_from(advertiser_id,advertiser_from) values($newaid,'{{FOOTER_SUBDOMAIN}}')";
	$rows = $dbh->do($sql);
}
print "Location: advertiser_disp2.cgi?pmode=U&puserid=$newaid\n\n";

# exit function

$util->clean_up();
exit(0);
