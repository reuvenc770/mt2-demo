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
$util->db_connect();
$dbh = $util->get_dbh;
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
$sql = "select creative_name,creative_id,date_format(creative_date,\"%m/%d/%y\"),thumbnail from creative where advertiser_id=$aid and status='A' order by creative_name";
$sth = $dbh->prepare($sql);
$sth->execute();
while (($cname,$cid,$cdate,$thumbnail) = $sth->fetchrow_array())
{
	$sql="select (sum(click_cnt)/sum(open_cnt)*100),((sum(click_cnt)/sum(sent_cnt))*100000) from creative_log where creative_id=$cid";
	$sth1 = $dbh->prepare($sql);
	$sth1->execute();
	($ctr,$cindex) = $sth1->fetchrow_array();
	$sth1->finish();
	if ($ctr eq "")
	{
		$ctr = "0.00";
	}
	if ($cindex eq "")
	{
		$cindex = "0";
	}
	print "<p><b>$cname (Since: $cdate - $ctr% Click - $cindex Index)</b></br>\n";
	print "<img border=0 src=\"http://www.affiliateimages.com/images/thumbnail/$thumbnail\"></p>\n";
}
$sth->finish();
print<<"end_of_html";
</body>
</html>
end_of_html
