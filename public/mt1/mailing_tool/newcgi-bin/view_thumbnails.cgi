#!/usr/bin/perl
#===============================================================================
# Purpose: Edit advertiser data (eg 'user' table).
# Name   : advertiser_disp.cgi (edit_advertiser_info.cgi)
#
#--Change Control---------------------------------------------------------------
# 01/05/04  Jim Sobeck  Creation
#===============================================================================

#-----  include Perl Modules ---------
use strict;
use CGI;
use util;

#------  get some objects to use later ---------
my $util = util->new;
my $query = CGI->new;
my $name;
my $sql;
my $sth;
my $dbh;
my $phone;
my $email;
my $company;
my $id;
my $aid = $query->param('aid');
my $cid;
my $cname;
my $cdate;
my $cindex;
my $click_cnt;
my $thumbnail;
my $sth1;
my $ctr;

#-----  check for login  ------
my $user_id = util::check_security();
if ($user_id == 0)
{
    print "Location: notloggedin.cgi\n\n";
    exit(0);
}
#------  connect to the util database -----------
my ($dbhq,$dbhu)=$util->get_dbh();
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Thumbnails</title>
</head>

<body>
end_of_html
$sql = "select creative_name,creative_id,date_format(creative_date,\"%m/%d/%y\"),thumbnail from creative where advertiser_id=? and status='A' order by creative_name";
$sth = $dbhq->prepare($sql);
$sth->execute($aid);
while (($cname,$cid,$cdate,$thumbnail) = $sth->fetchrow_array())
{
#	$sql="select (sum(el.click_cnt)/sum(el.open_cnt)*100),sum(el.click_cnt) from email_log el,campaign c where el.creative_id=? and el.campaign_id=c.campaign_id and c.scheduled_date >= date_sub(curdate(),interval 60 day) and c.deleted_date is null and c.advertiser_id=$aid";
#	$sth1 = $dbhq->prepare($sql);
#	$sth1->execute($cid);
#	($ctr,$click_cnt) = $sth1->fetchrow_array();
#	$sth1->finish();

	my $sent_cnt;
	my $bounce_cnt;
#	$sql="select sum(cnt) from campaign_files where creative_id=? and status='LOADED' and timest >= '2007-04-02'";
	my $send_cnt=0;
	if ($sent_cnt > 0)
	{
		my $temp=($click_cnt/$sent_cnt)*100000;
		$cindex=sprintf("%4.2f",$temp);
	}
	else
	{
		$cindex=0;
	}

	if ($ctr eq "")
	{
		$ctr = "0.00";
	}
	if ($cindex eq "")
	{
		$cindex = "0";
	}
	$sql="select count(*) from advertiser_setup where class_id=4 and (creative1_id=? or creative2_id=? or creative3_id=? or creative4_id=? or creative5_id=? or creative6_id=? or creative7_id=? or creative8_id=? or creative9_id=? or creative10_id=? or creative11_id=? or creative12_id=? or creative13_id=? or creative14_id=? or creative15_id=?)"; 
	$sth1 = $dbhq->prepare($sql);
	$sth1->execute($cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid,$cid);
	($sent_cnt) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($sent_cnt > 0)
	{
		print "<p><b>$cname - $cid (Since: $cdate - $ctr% Click - $cindex Index - R)</b></br>\n";
	}
	else
	{
		print "<p><b>$cname - $cid (Since: $cdate - $ctr% Click - $cindex Index)</b></br>\n";
	}
	print "<img border=0 src=\"http://www.affiliateimages.com/images/thumbnail/$thumbnail\"></p>\n";
}
$sth->finish();
print<<"end_of_html";
</body>
</html>
end_of_html
