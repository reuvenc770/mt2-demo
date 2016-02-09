#!/usr/bin/perl

# *****************************************************************************************
# 3rd_upd_advertiser.cgi
#
# this page updates select list based on advertiser_id
#
# History
# Jim Sobeck, 01/19/05, Creation
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
my $aid;
my $aname;
my $errmsg;
my $images = $util->get_images_url;
my $cfrom;
my @from_array;
my $sth1;

# connect to the util database
$util->db_connect();
$dbh = $util->get_dbh;

my $cid = $query->param('cid');
#
print "Content-Type: text/html\n\n";
print<<"end_of_html";
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>CREATE EMAIL</title>
<script language="JavaScript">
function doit(value,text)
{
	parent.main.addAdvertiser(value,text);
}
</script>
</head>
<body>
<script language="JavaScript">
doit(0,"Select One");
end_of_html
my $cdate;
my $aname;
my $offer_type;
my $payout;
my $ecpm;
my $tid;
my $incoming_aid;
if ($cid == -1)
{
	$sql="select advertiser_id,advertiser_name,offer_type,format(payout,2),ecpm from advertiser_info where status in ('A','S') order by advertiser_name";
}
else
{
	$sql="select advertiser_id,advertiser_name,offer_type,format(payout,2),ecpm from advertiser_info where category_id=$cid and status in ('A','S') order by advertiser_name";
}
$sth = $dbh->prepare($sql) ;
$sth->execute();
while (($aid,$aname,$offer_type,$payout,$ecpm) = $sth->fetchrow_array())
{
	$sql="select max(date_format(scheduled_datetime,\"%m/%d/%Y\")) from campaign where advertiser_id=$aid";
	$sth1 = $dbh->prepare($sql) ;
	$sth1->execute();
	($cdate) = $sth1->fetchrow_array();
	$sth1->finish();
	my $temp_str;
	if ($cdate ne "")
	{
		$temp_str = $aname . "(Last Used " .$cdate . " - " . $offer_type . ":" . $payout . " - eCPM: " . $ecpm . ")";
	}
	else
	{
		$temp_str = $aname . "(Never Used - " . $offer_type . ":" . $payout . " - eCPM: " . $ecpm . ")";
	}
	print "doit($aid,\"$temp_str\");\n";
}
$sth->finish();
print "parent.main.set_advertiser();\n";
print "</script>\n";
print "</body>\n";
print "</html>\n";
$util->clean_up();
exit(0);
